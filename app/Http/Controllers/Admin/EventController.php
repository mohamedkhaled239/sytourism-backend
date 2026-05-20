<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OneSignalService;
use App\Models\Event;
use App\Models\EventOrganizer;
use App\Models\Location;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with(['category', 'locations'])->latest()->paginate(20);
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        $categories = Category::where('type', 'events')->active()->get();
        $locations = Location::active()->get();
        return view('admin.events.create', compact('categories', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'description' => 'required',
            'description_ar' => 'required',
            'main_image' => 'required|image|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'category_id' => 'required|exists:categories,id',
            'locations' => 'required|array|min:1',
            'locations.*' => 'exists:locations,id',
            'organizers' => 'required|array|min:1',
            'organizers.*.name' => 'required|string',
            'organizers.*.name_ar' => 'required|string'
        ]);

        $imagePath = $request->file('main_image')->store('events', 'public');

        $event = Event::create([
            'title' => $request->title,
            'title_ar' => $request->title_ar,
            'description' => $request->description,
            'description_ar' => $request->description_ar,
            'main_image' => $imagePath,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'category_id' => $request->category_id,
            'status' => $request->status ?? 'not_started',
            'is_published' => $request->has('is_published')
        ]);

        // Attach locations
        $event->locations()->attach($request->locations);

        foreach ($request->organizers as $organizer) {
            EventOrganizer::create([
                'event_id' => $event->id,
                'name' => $organizer['name'],
                'name_ar' => $organizer['name_ar'],
                'contact' => $organizer['contact'] ?? null
            ]);
        }

        // Send push notification to all users via OneSignal
        $oneSignalService = new OneSignalService();
        $oneSignalService->sendEventNotification($event);

        return redirect()->route('admin.events.index')
            ->with('success', 'تم إضافة الحدث بنجاح');
    }

    public function edit($id)
    {
        $event = Event::with(['organizers', 'locations'])->findOrFail($id);
        $categories = Category::where('type', 'events')->active()->get();
        $locations = Location::active()->get();
        return view('admin.events.edit', compact('event', 'categories', 'locations'));
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'description' => 'required',
            'description_ar' => 'required',
            'main_image' => 'nullable|image|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'category_id' => 'required|exists:categories,id',
            'locations' => 'required|array|min:1',
            'locations.*' => 'exists:locations,id',
            'organizers' => 'required|array|min:1',
            'organizers.*.name' => 'required|string',
            'organizers.*.name_ar' => 'required|string'
        ]);

        $data = [
            'title' => $request->title,
            'title_ar' => $request->title_ar,
            'description' => $request->description,
            'description_ar' => $request->description_ar,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'category_id' => $request->category_id,
            'status' => $request->status ?? 'not_started',
            'is_published' => $request->has('is_published')
        ];

        if ($request->hasFile('main_image')) {
            Storage::disk('public')->delete($event->main_image);
            $data['main_image'] = $request->file('main_image')->store('events', 'public');
        }

        $event->update($data);

        // Update locations
        $event->locations()->sync($request->locations);

        // Update organizers
        $event->organizers()->delete();
        foreach ($request->organizers as $organizer) {
            EventOrganizer::create([
                'event_id' => $event->id,
                'name' => $organizer['name'],
                'name_ar' => $organizer['name_ar'],
                'contact' => $organizer['contact'] ?? null
            ]);
        }

        return redirect()->route('admin.events.index')
            ->with('success', 'تم تحديث الحدث بنجاح');
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        Storage::disk('public')->delete($event->main_image);
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'تم حذف الحدث بنجاح');
    }
}
