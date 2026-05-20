// import 'package:flutter/material.dart';
// import 'package:flutter/foundation.dart';
// import 'package:sy_tourism/services/deep_link_service.dart';

// /// Service to handle notification navigation with proper timing
// class NotificationNavigationService {
//   static NotificationNavigationService? _instance;
//   static NotificationNavigationService get instance {
//     _instance ??= NotificationNavigationService._internal();
//     return _instance!;
//   }

//   NotificationNavigationService._internal();

//   Map<String, dynamic>? _pendingNotificationData;
//   bool _isAppReady = false;
//   BuildContext? _currentContext;

//   /// Mark the app as ready for navigation
//   void markAppReady(BuildContext context) {
//     _isAppReady = true;
//     _currentContext = context;

//     if (kDebugMode) {
//       print('🚀 App marked as ready for navigation');
//     }

//     // Process any pending notification
//     _processPendingNotification();
//   }

//   /// Store notification data for later processing
//   void storeNotificationData(Map<String, dynamic> data) {
//     _pendingNotificationData = data;

//     if (kDebugMode) {
//       print('📱 Notification data stored:');
//       print('  Raw data: $data');
//       print('  Keys: ${data.keys.toList()}');
//       print('  Type: ${data['type']}');
//       print('  Action: ${data['action']}');
//       print('  News ID: ${data['news_id']}');
//       print('  Event ID: ${data['event_id']}');
//       print('  Location ID: ${data['location_id']}');
//       print('  Generic ID: ${data['id']}');
//     }

//     // Try to process immediately if app is ready
//     if (_isAppReady && _currentContext != null) {
//       _processPendingNotification();
//     }
//   }

//   /// Process pending notification if exists
//   void _processPendingNotification() {
//     if (_pendingNotificationData == null ||
//         _currentContext == null ||
//         !_isAppReady) {
//       if (kDebugMode) {
//         print(
//           '⏳ Cannot process notification yet - App ready: $_isAppReady, Context: ${_currentContext != null}, Data: ${_pendingNotificationData != null}',
//         );
//       }
//       return;
//     }

//     final data = _pendingNotificationData!;
//     final context = _currentContext!;

//     if (kDebugMode) {
//       print('🔄 Processing pending notification: $data');
//     }

//     // Clear the pending data first
//     _pendingNotificationData = null;

//     // Add a longer delay to ensure the main navigation page is fully loaded
//     Future.delayed(const Duration(milliseconds: 1500), () {
//       if (context.mounted) {
//         try {
//           if (kDebugMode) {
//             print('🚀 Attempting to navigate from notification...');
//           }

//           DeepLinkService.instance.handleNotificationData(context, data);

//           if (kDebugMode) {
//             print('✅ Notification processed successfully');
//           }
//         } catch (e) {
//           if (kDebugMode) {
//             print('❌ Error processing notification: $e');
//           }
//         }
//       } else {
//         if (kDebugMode) {
//           print('❌ Context no longer mounted, cannot navigate');
//         }
//       }
//     });
//   }

//   /// Check if there's pending notification data
//   bool get hasPendingNotification => _pendingNotificationData != null;

//   /// Clear pending notification data
//   void clearPendingNotification() {
//     _pendingNotificationData = null;

//     if (kDebugMode) {
//       print('🧹 Pending notification cleared');
//     }
//   }

//   /// Reset the service state
//   void reset() {
//     _isAppReady = false;
//     _currentContext = null;
//     _pendingNotificationData = null;

//     if (kDebugMode) {
//       print('🔄 Notification navigation service reset');
//     }
//   }
// }
