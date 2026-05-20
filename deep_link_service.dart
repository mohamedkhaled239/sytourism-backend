// import 'package:flutter/material.dart';
// import 'package:flutter/foundation.dart';
// import 'package:sy_tourism/views/screens/news_datails_page.dart';
// import 'package:sy_tourism/views/screens/event_details_page.dart';
// import 'package:sy_tourism/views/screens/location_details_page.dart';
// import 'package:sy_tourism/views/screens/home_page.dart';

// class DeepLinkService {
//   static DeepLinkService? _instance;
//   static DeepLinkService get instance {
//     _instance ??= DeepLinkService._internal();
//     return _instance!;
//   }

//   DeepLinkService._internal();

//   /// Handle deep link navigation
//   void handleDeepLink(BuildContext context, String url) {
//     try {
//       if (kDebugMode) {
//         print('🔗 === HANDLING DEEP LINK START ===');
//         print('  URL: $url');
//         print('  Context mounted: ${context.mounted}');
//         print('====================================');
//       }

//       final uri = Uri.tryParse(url);
//       if (uri == null) {
//         if (kDebugMode) {
//           print('Invalid URL format: $url');
//           print('Not navigating to home for invalid URL');
//         }
//         return;
//       }

//       if (kDebugMode) {
//         print(
//           'Parsed URI - Scheme: ${uri.scheme}, Host: ${uri.host}, Path: ${uri.path}',
//         );
//       }

//       // Handle different URL schemes (tourism_app is primary)
//       if (uri.scheme == 'tourism_app' || uri.scheme == 'seaha') {
//         _handleCustomScheme(context, uri);
//       } else if (uri.scheme == 'http' || uri.scheme == 'https') {
//         _handleHttpScheme(context, uri);
//       } else {
//         if (kDebugMode) {
//           print('Unsupported URL scheme: ${uri.scheme}');
//           print('Not navigating to home for unsupported scheme');
//         }
//       }
//     } catch (e) {
//       if (kDebugMode) {
//         print('Error handling deep link: $e');
//         print('Stack trace: ${StackTrace.current}');
//         print('Not navigating to home due to error');
//       }
//     }
//   }

//   /// Handle custom scheme URLs (tourism_app:// or seaha://)
//   void _handleCustomScheme(BuildContext context, Uri uri) {
//     final type = uri.host; // news, event, location, investment
//     final pathSegments = uri.pathSegments;
//     final idStr = pathSegments.isNotEmpty ? pathSegments.first : null;
//     final id = int.tryParse(idStr ?? '');

//     if (kDebugMode) {
//       print('🔗 === CUSTOM SCHEME DEEP LINK DEBUG ===');
//       print('  Full URI: $uri');
//       print('  Scheme: ${uri.scheme}');
//       print('  Host/Type: $type');
//       print('  Path Segments: $pathSegments');
//       print('  ID String: $idStr');
//       print('  Parsed ID: $id');
//       print('  URI Authority: ${uri.authority}');
//       print('  URI Path: ${uri.path}');
//       print('========================================');
//     }

//     // Ensure we have valid navigation parameters
//     if (type == null || type.isEmpty) {
//       if (kDebugMode) {
//         print('No type specified in URL');
//         print('Not navigating to home for missing type');
//       }
//       return;
//     }

//     if (id == null || id <= 0) {
//       if (kDebugMode) {
//         print('Invalid or missing ID in URL');
//         print('Not navigating to home for invalid ID');
//       }
//       return;
//     }

//     switch (type.toLowerCase()) {
//       case 'news':
//         _navigateToNews(context, id);
//         break;
//       case 'event':
//       case 'events':
//         _navigateToEvent(context, id);
//         break;
//       case 'location':
//       case 'locations':
//         _navigateToLocation(context, id);
//         break;
//       case 'investment':
//       case 'investments':
//         _navigateToInvestment(context, id);
//         break;
//       default:
//         if (kDebugMode) {
//           print('Unknown type: $type');
//           print('Not navigating to home for unknown type');
//         }
//     }
//   }

//   /// Handle HTTP/HTTPS scheme URLs
//   void _handleHttpScheme(BuildContext context, Uri uri) {
//     final pathSegments = uri.pathSegments;

//     if (kDebugMode) {
//       print('=== HTTP SCHEME DEBUG ===');
//       print('Path segments: $pathSegments');
//       print('=========================');
//     }

//     if (pathSegments.length >= 2) {
//       final type = pathSegments[0]; // news, events, locations, investments
//       final idStr = pathSegments[1];
//       final id = int.tryParse(idStr);

//       if (kDebugMode) {
//         print('HTTP scheme - Type: $type, ID: $id');
//       }

//       if (id == null || id <= 0) {
//         if (kDebugMode) {
//           print('Invalid ID in HTTP URL');
//           print('Not navigating to home for invalid HTTP ID');
//         }
//         return;
//       }

//       switch (type.toLowerCase()) {
//         case 'news':
//           _navigateToNews(context, id);
//           break;
//         case 'events':
//           _navigateToEvent(context, id);
//           break;
//         case 'locations':
//           _navigateToLocation(context, id);
//           break;
//         case 'investments':
//           _navigateToInvestment(context, id);
//           break;
//         default:
//           if (kDebugMode) {
//             print('Unknown HTTP type: $type');
//             print('Not navigating to home for unknown HTTP type');
//           }
//       }
//     } else {
//       if (kDebugMode) {
//         print('Invalid HTTP URL structure');
//         print('Not navigating to home for invalid HTTP structure');
//       }
//     }
//   }

//   /// Handle notification data-based navigation (improved)
//   void handleNotificationData(BuildContext context, Map<String, dynamic> data) {
//     try {
//       if (kDebugMode) {
//         print('=== NOTIFICATION DATA HANDLING ===');
//         print('Data: $data');
//         print('==================================');
//       }

//       // First try to get URL from data - try all possible keys
//       final possibleUrls = [
//         data['app_url']?.toString(),
//         data['url']?.toString(),
//         data['launch_url']?.toString(),
//         data['launchUrl']?.toString(), // Sometimes OneSignal uses this key
//       ];

//       if (kDebugMode) {
//         print('🔍 Searching for URLs in notification data:');
//         for (int i = 0; i < possibleUrls.length; i++) {
//           final url = possibleUrls[i];
//           print('  URL[$i]: $url');
//         }
//       }

//       for (final url in possibleUrls) {
//         if (url != null && url.isNotEmpty) {
//           if (kDebugMode) {
//             print('✅ Found valid URL in notification data: $url');
//             print('🚀 Attempting to handle deep link...');
//           }
//           handleDeepLink(context, url);
//           return;
//         }
//       }

//       if (kDebugMode) {
//         print('❌ No valid URL found in notification data');
//       }

//       // If no URL found, try action-based navigation
//       final action = data['action']?.toString();
//       final type = data['type']?.toString();

//       if (kDebugMode) {
//         print('No URL found, trying action: $action, type: $type');
//       }

//       // Try to extract ID from various possible keys
//       int? id;
//       final idKeys = [
//         'news_id',
//         'event_id',
//         'location_id',
//         'investment_id',
//         'id',
//       ];

//       if (kDebugMode) {
//         print('🔍 Searching for ID in data keys:');
//         for (final key in idKeys) {
//           print('  $key: ${data[key]} (type: ${data[key].runtimeType})');
//         }
//       }

//       for (final key in idKeys) {
//         id = _parseId(data[key]);
//         if (id != null) {
//           if (kDebugMode) {
//             print('✅ Found ID $id from key: $key');
//           }
//           break;
//         }
//       }

//       if (id == null && kDebugMode) {
//         print('❌ No valid ID found in notification data');
//         print('Available keys: ${data.keys.toList()}');
//       }

//       // Determine navigation type
//       String? navigationType;

//       if (action != null) {
//         // Extract type from action (e.g., 'view_news' -> 'news')
//         navigationType = action.replaceAll('view_', '');
//         if (kDebugMode) {
//           print('🎯 Navigation type from action: $action -> $navigationType');
//         }
//       } else if (type != null) {
//         navigationType = type;
//         if (kDebugMode) {
//           print('🎯 Navigation type from type field: $navigationType');
//         }
//       }

//       if (kDebugMode) {
//         print('📍 Final navigation decision: type=$navigationType, ID=$id');
//         if (navigationType != null && id != null) {
//           print('✅ Ready to navigate to $navigationType with ID $id');
//         } else {
//           print('❌ Cannot navigate - missing type or ID');
//         }
//       }

//       if (navigationType != null && id != null) {
//         switch (navigationType.toLowerCase()) {
//           case 'news':
//             _navigateToNews(context, id);
//             break;
//           case 'event':
//           case 'events':
//             _navigateToEvent(context, id);
//             break;
//           case 'location':
//           case 'locations':
//             _navigateToLocation(context, id);
//             break;
//           case 'investment':
//           case 'investments':
//             _navigateToInvestment(context, id);
//             break;
//           default:
//             if (kDebugMode) {
//               print('Unknown navigation type: $navigationType');
//               print('Not navigating to home for unknown type');
//             }
//           // Don't navigate to home for unknown types
//         }
//       } else {
//         if (kDebugMode) {
//           print(
//             'Could not determine navigation type or ID from notification data',
//           );
//           print('Data: $data');
//           print('Not navigating to home to avoid interrupting user');
//         }
//         // Don't navigate to home, let user stay where they are
//       }
//     } catch (e) {
//       if (kDebugMode) {
//         print('Error handling notification data: $e');
//         print('Stack trace: ${StackTrace.current}');
//         print('Not navigating to home due to error');
//       }
//       // Don't navigate to home on error, let user stay where they are
//     }
//   }

//   /// Parse ID from various formats
//   int? _parseId(dynamic value) {
//     if (value == null) return null;

//     if (value is int) return value > 0 ? value : null;
//     if (value is String) {
//       final parsed = int.tryParse(value);
//       return parsed != null && parsed > 0 ? parsed : null;
//     }

//     return null;
//   }

//   /// Navigate to news details
//   void _navigateToNews(BuildContext context, int newsId) {
//     if (kDebugMode) {
//       print('=== NAVIGATING TO NEWS ===');
//       print('News ID: $newsId');
//       print('Context: $context');
//       print(
//         'Navigator available: ${Navigator.of(context, rootNavigator: true)}',
//       );
//       print('========================');
//     }

//     try {
//       // Use a more robust navigation approach
//       _performNavigation(
//         context,
//         () {
//           return MaterialPageRoute(
//             builder: (context) => NewsDetailsPage(newsId: newsId),
//             settings: RouteSettings(name: '/news/$newsId'),
//           );
//         },
//         'news',
//         newsId,
//       );
//     } catch (e) {
//       if (kDebugMode) {
//         print('Error navigating to news: $e');
//         print('Stack trace: ${StackTrace.current}');
//         print('Not navigating to home due to navigation error');
//       }
//       // Don't navigate to home on error, let user stay where they are
//     }
//   }

//   /// Navigate to event details
//   void _navigateToEvent(BuildContext context, int eventId) {
//     if (kDebugMode) {
//       print('=== NAVIGATING TO EVENT ===');
//       print('Event ID: $eventId');
//       print('=========================');
//     }

//     try {
//       _performNavigation(
//         context,
//         () {
//           return MaterialPageRoute(
//             builder: (context) => EventDetailsPage(eventId: eventId),
//             settings: RouteSettings(name: '/event/$eventId'),
//           );
//         },
//         'event',
//         eventId,
//       );
//     } catch (e) {
//       if (kDebugMode) {
//         print('Error navigating to event: $e');
//         print('Not navigating to home due to navigation error');
//       }
//       // Don't navigate to home on error, let user stay where they are
//     }
//   }

//   /// Navigate to location details
//   void _navigateToLocation(BuildContext context, int locationId) {
//     if (kDebugMode) {
//       print('=== NAVIGATING TO LOCATION ===');
//       print('Location ID: $locationId');
//       print('=============================');
//     }

//     try {
//       _performNavigation(
//         context,
//         () {
//           return MaterialPageRoute(
//             builder: (context) => LocationDetailsPage(locationId: locationId),
//             settings: RouteSettings(name: '/location/$locationId'),
//           );
//         },
//         'location',
//         locationId,
//       );
//     } catch (e) {
//       if (kDebugMode) {
//         print('Error navigating to location: $e');
//         print('Not navigating to home due to navigation error');
//       }
//       // Don't navigate to home on error, let user stay where they are
//     }
//   }

//   /// Navigate to investment details
//   void _navigateToInvestment(BuildContext context, int investmentId) {
//     if (kDebugMode) {
//       print('=== NAVIGATING TO INVESTMENT ===');
//       print('Investment ID: $investmentId');
//       print('===============================');
//     }

//     // TODO: Create InvestmentDetailsPage when available
//     if (kDebugMode) {
//       print('Investment details page not implemented yet');
//       print('Not navigating to home - staying on current page');
//     }
//     // Don't navigate to home when investment page is not implemented
//   }

//   /// Perform navigation with better strategies that don't remove all routes
//   void _performNavigation(
//     BuildContext context,
//     MaterialPageRoute Function() routeBuilder,
//     String type,
//     int id,
//   ) {
//     try {
//       // Strategy 1: Try push first (don't replace, just add on top)
//       Navigator.of(context).push(routeBuilder());

//       if (kDebugMode) {
//         print('Navigation to $type $id completed successfully with push');
//       }
//     } catch (e1) {
//       if (kDebugMode) {
//         print('push failed: $e1, trying pushReplacement');
//       }

//       try {
//         // Strategy 2: Try pushReplacement as fallback
//         Navigator.of(context).pushReplacement(routeBuilder());

//         if (kDebugMode) {
//           print(
//             'Navigation to $type $id completed successfully with pushReplacement',
//           );
//         }
//       } catch (e2) {
//         if (kDebugMode) {
//           print('pushReplacement failed: $e2, trying with rootNavigator');
//         }

//         try {
//           // Strategy 3: Try with root navigator but use push, not pushAndRemoveUntil
//           Navigator.of(context, rootNavigator: true).push(routeBuilder());

//           if (kDebugMode) {
//             print(
//               'Navigation to $type $id completed successfully with rootNavigator push',
//             );
//           }
//         } catch (e3) {
//           if (kDebugMode) {
//             print('All navigation strategies failed: $e3');
//             print('Not navigating to home to avoid interrupting user');
//           }
//           // Don't throw error or navigate to home, just log the failure
//         }
//       }
//     }
//   }

//   /// Navigate to home page
//   void navigateToHome(BuildContext context) {
//     if (kDebugMode) {
//       print('=== NAVIGATING TO HOME ===');
//       print('Context: $context');
//       print('=========================');
//     }

//     try {
//       Navigator.of(context).pushAndRemoveUntil(
//         MaterialPageRoute(
//           builder: (context) => const HomePage(),
//           settings: const RouteSettings(name: '/home'),
//         ),
//         (route) => false,
//       );

//       if (kDebugMode) {
//         print('Navigation to home completed successfully');
//       }
//     } catch (e) {
//       if (kDebugMode) {
//         print('Error navigating to home: $e');
//         print('Trying with root navigator...');
//       }

//       try {
//         Navigator.of(context, rootNavigator: true).pushAndRemoveUntil(
//           MaterialPageRoute(
//             builder: (context) => const HomePage(),
//             settings: const RouteSettings(name: '/home'),
//           ),
//           (route) => false,
//         );

//         if (kDebugMode) {
//           print(
//             'Navigation to home completed successfully with root navigator',
//           );
//         }
//       } catch (e2) {
//         if (kDebugMode) {
//           print('Failed to navigate to home with root navigator: $e2');
//         }
//       }
//     }
//   }
// }
