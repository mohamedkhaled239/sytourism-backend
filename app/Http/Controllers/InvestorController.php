<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvestorController extends Controller
{
    private function ensureInvestor(): void
    {
        if (!Auth::guard('web')->check() || Auth::guard('web')->user()->user_type !== 'investor') {
            abort(403);
        }
    }

    private function investorLocationsQuery()
    {
        return Location::withoutGlobalScope('admin_tourism_type')
            ->where('is_active', 1);
    }

    public function showLoginForm()
    {
        if (Auth::guard('web')->check() && Auth::guard('web')->user()->user_type === 'investor') {
            return redirect()->route('investor.dashboard');
        }

        return view('investor.login');
    }

    public function showRegisterForm()
    {
        if (Auth::guard('web')->check() && Auth::guard('web')->user()->user_type === 'investor') {
            return redirect()->route('investor.dashboard');
        }

        return view('investor.register');
    }

    public function showVerifyEmailForm(Request $request)
    {
        if (Auth::guard('web')->check() && Auth::guard('web')->user()->user_type === 'investor') {
            return redirect()->route('investor.dashboard');
        }

        return view('investor.verify-email', [
            'email' => $request->query('email', ''),
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::guard('web')->user();

            if ($user->user_type !== 'investor') {
                Auth::guard('web')->logout();

                return back()->withErrors(['email' => 'هذا الحساب ليس حساب مستثمر.']);
            }

            if (!$user->email_verified_at) {
                Auth::guard('web')->logout();

                return back()->withErrors(['email' => 'يرجى تأكيد البريد الإلكتروني أولًا قبل تسجيل الدخول.']);
            }

            if (!$user->is_approved) {
                Auth::guard('web')->logout();

                return back()->withErrors(['email' => 'الحساب قيد المراجعة ولم تتم موافقة الإدارة عليه بعد.']);
            }

            return redirect()->intended(route('investor.dashboard'));
        }

        return back()->withErrors([
            'email' => 'بيانات الدخول غير صحيحة.',
        ]);
    }

    public function dashboard()
    {
        $this->ensureInvestor();

        $allLocations = $this->investorLocationsQuery()
            ->with(['categories', 'governorate', 'city', 'tourismType'])
            ->get();

        $locations = $this->investorLocationsQuery()
            ->with(['categories', 'governorate', 'city', 'tourismType'])
            ->paginate(15);

        $recentInvestments = Investment::with(['locations', 'categories'])
            ->where('is_published', true)
            ->latest()
            ->take(6)
            ->get();

        return view('investor.dashboard', compact('locations', 'allLocations', 'recentInvestments'));
    }

    public function showLocation($id)
    {
        $this->ensureInvestor();

        $location = $this->investorLocationsQuery()
            ->with([
                'categories',
                'governorate',
                'city',
                'tourismType',
                'locationTypes',
                'events' => function ($query) {
                    $query->where('is_published', true)->orderBy('events.start_date', 'desc');
                },
                'investments' => function ($query) {
                    $query->where('is_published', true)
                        ->with(['locations', 'categories'])
                        ->orderByDesc('investments.created_at');
                }
            ])
            ->findOrFail($id);

        return view('investor.locations.show', compact('location'));
    }

    public function investments(Request $request)
    {
        $this->ensureInvestor();

        $locationId = $request->integer('location');

        $investments = Investment::with(['locations.governorate', 'categories'])
            ->where('is_published', true)
            ->when($locationId, function ($query) use ($locationId) {
                $query->whereHas('locations', function ($locationQuery) use ($locationId) {
                    $locationQuery->where('locations.id', $locationId);
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $selectedLocation = $locationId
            ? $this->investorLocationsQuery()->find($locationId)
            : null;

        return view('investor.investments.index', compact('investments', 'selectedLocation'));
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('investor.login');
    }
}
