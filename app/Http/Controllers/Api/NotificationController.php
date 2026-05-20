<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get latest notifications for each type
     */
    public function getLatest()
    {
        try {
            $latestNotifications = Notification::getLatestByType();
            
            return response()->json([
                'success' => true,
                'message' => 'تم جلب آخر الإشعارات بنجاح',
                'data' => $latestNotifications
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الإشعارات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all notifications with pagination
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $type = $request->get('type');

            $query = Notification::orderBy('created_at', 'desc');

            if ($type && in_array($type, Notification::getTypes())) {
                $query->where('type', $type);
            }

            $notifications = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'تم جلب الإشعارات بنجاح',
                'data' => $notifications
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الإشعارات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user's FCM token
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string'
        ]);

        try {
            $user = Auth::user();
            $user->update([
                'fcm_token' => $request->fcm_token
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث رمز الإشعارات بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث رمز الإشعارات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notification statistics
     */
    public function getStats()
    {
        try {
            $stats = [];
            $types = Notification::getTypes();

            foreach ($types as $type) {
                $stats[$type] = [
                    'count' => Notification::where('type', $type)->count(),
                    'latest' => Notification::where('type', $type)
                        ->orderBy('created_at', 'desc')
                        ->first()
                ];
            }

            $stats['total'] = Notification::count();
            $stats['users_with_notifications'] = User::where('notifications_enabled', true)->count();

            return response()->json([
                'success' => true,
                'message' => 'تم جلب إحصائيات الإشعارات بنجاح',
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الإحصائيات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user's OneSignal player id
     */
    public function updateOneSignalPlayerId(Request $request)
    {
        $request->validate([
            'player_id' => 'required|string'
        ]);

        try {
            $user = Auth::user();
            $user->update([
                'onesignal_player_id' => $request->player_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث معرف OneSignal بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث معرف OneSignal',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
