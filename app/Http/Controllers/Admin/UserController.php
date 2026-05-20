<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderByRaw("CASE WHEN user_type = 'investor' AND is_approved = 0 THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }

    public function export()
    {
        return Excel::download(new UsersExport(), 'users-' . date('Y-m-d-H-i-s') . '.xlsx');
    }

    public function approveInvestor($id)
    {
        $user = User::findOrFail($id);

        if (!$user->isInvestor()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'هذا الحساب ليس حساب مستثمر');
        }

        if ($user->is_approved) {
            return redirect()->route('admin.users.index')
                ->with('success', 'تمت الموافقة على المستثمر مسبقًا');
        }

        $user->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);

        try {
            Mail::send('emails.investor-approved', ['user' => $user], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('تمت الموافقة على حساب المستثمر');
            });
        } catch (\Throwable $exception) {
            \Log::error('Failed to send investor approval email: ' . $exception->getMessage());
        }

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', 'تمت الموافقة على المستثمر وإرسال الإشعار البريدي');
    }
}
