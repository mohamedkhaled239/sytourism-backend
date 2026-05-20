<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OneSignalService;
use App\Models\News;
use App\Models\NewsImage;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::latest()->paginate(20);
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        $categories = Category::where('type', 'news')->active()->get();
        return view('admin.news.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'content' => 'required',
            'content_ar' => 'required',
            'main_image' => 'required|image|max:2048',
            'additional_images.*' => 'image|max:2048',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id'
        ]);

        $mainImagePath = $request->file('main_image')->store('news', 'public');

        $news = News::create([
            'title' => $request->title,
            'title_ar' => $request->title_ar,
            'content' => $request->content,
            'content_ar' => $request->content_ar,
            'main_image' => $mainImagePath,
            'is_published' => $request->has('is_published')
        ]);

        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $index => $image) {
                $path = $image->store('news', 'public');
                NewsImage::create([
                    'news_id' => $news->id,
                    'image_path' => $path,
                    'order' => $index
                ]);
            }
        }

        // Attach categories
        if ($request->has('categories')) {
            $news->categories()->attach($request->categories);
        }

        // Send push notification to all users via OneSignal
        $oneSignalService = new OneSignalService();
        $oneSignalService->sendNewsNotification($news);

        return redirect()->route('admin.news.index')
            ->with('success', 'تم إضافة الخبر بنجاح');
    }

    public function edit($id)
    {
        $news = News::with(['images', 'categories'])->findOrFail($id);
        $categories = Category::where('type', 'news')->active()->get();
        return view('admin.news.edit', compact('news', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'content' => 'required',
            'content_ar' => 'required',
            'main_image' => 'nullable|image|max:2048',
            'additional_images.*' => 'image|max:2048',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id'
        ]);

        $data = [
            'title' => $request->title,
            'title_ar' => $request->title_ar,
            'content' => $request->content,
            'content_ar' => $request->content_ar,
            'is_published' => $request->has('is_published')
        ];

        if ($request->hasFile('main_image')) {
            Storage::disk('public')->delete($news->main_image);
            $data['main_image'] = $request->file('main_image')->store('news', 'public');
        }

        $news->update($data);

        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $index => $image) {
                $path = $image->store('news', 'public');
                NewsImage::create([
                    'news_id' => $news->id,
                    'image_path' => $path,
                    'order' => $news->images->count() + $index
                ]);
            }
        }

        // Sync categories
        $news->categories()->sync($request->categories ?? []);

        return redirect()->route('admin.news.index')
            ->with('success', 'تم تحديث الخبر بنجاح');
    }

    public function destroy($id)
    {
        $news = News::findOrFail($id);

        Storage::disk('public')->delete($news->main_image);
        foreach ($news->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'تم حذف الخبر بنجاح');
    }
}
