<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Governorate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function index()
    {
        $admins = Admin::with('governorate')->latest()->paginate(20);

        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        $governorates = Governorate::active()->orderBy('name_ar')->get();

        return view('admin.admins.create', compact('governorates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins',
            'password' => 'required|min:8|confirmed',
            'is_super_admin' => 'boolean',
            'account_type' => 'required|in:admin,data_entry_tourism_establishments,data_entry_attractions_routes',
            'governorate_id' => 'nullable|exists:governorates,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $payload = $this->buildPayload($request);
        $payload['password'] = Hash::make($request->password);

        Admin::create($payload);

        return redirect()->route('admin.admins.index')
            ->with('success', 'تم إضافة الحساب بنجاح');
    }

    public function edit($id)
    {
        $admin = Admin::findOrFail($id);
        $governorates = Governorate::active()->orderBy('name_ar')->get();

        return view('admin.admins.edit', compact('admin', 'governorates'));
    }

    public function show($id)
    {
        return redirect()->route('admin.admins.edit', $id);
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $id,
            'password' => 'nullable|min:8|confirmed',
            'is_super_admin' => 'boolean',
            'account_type' => 'required|in:admin,data_entry_tourism_establishments,data_entry_attractions_routes',
            'governorate_id' => 'nullable|exists:governorates,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $data = $this->buildPayload($request);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()->route('admin.admins.index')
            ->with('success', 'تم تحديث الحساب بنجاح');
    }

    public function destroy($id)
    {
        Admin::findOrFail($id)->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', 'تم حذف الحساب بنجاح');
    }

    private function buildPayload(Request $request): array
    {
        $isSuperAdmin = $request->has('is_super_admin');
        $accountType = $isSuperAdmin ? 'admin' : $request->account_type;
        $isDataEntry = in_array($accountType, ['data_entry_tourism_establishments', 'data_entry_attractions_routes'], true);

        if ($isDataEntry && !$request->filled('governorate_id')) {
            throw ValidationException::withMessages([
                'governorate_id' => 'يجب اختيار المحافظة لحساب مدخل البيانات',
            ]);
        }

        return [
            'name' => $request->name,
            'email' => $request->email,
            'is_super_admin' => $isSuperAdmin,
            'account_type' => $accountType,
            'governorate_id' => $isDataEntry ? $request->governorate_id : null,
            'permissions' => $isDataEntry ? ['locations', 'map'] : $request->input('permissions', []),
        ];
    }
}
