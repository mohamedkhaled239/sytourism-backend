import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:flutter_localizations/flutter_localizations.dart';

import 'package:sy_tourism/generated/l10n.dart';
import 'package:sy_tourism/bloc/language/app_language_bloc.dart';
import 'package:sy_tourism/bloc/auth/auth_bloc.dart';
import 'package:sy_tourism/bloc/profile/profile_bloc.dart';
import 'package:sy_tourism/bloc/news/news_bloc.dart';
import 'package:sy_tourism/bloc/events/events_bloc.dart';
import 'package:sy_tourism/bloc/event_details/event_details_bloc.dart';
import 'package:sy_tourism/bloc/favorites/favorites_bloc.dart';
import 'package:sy_tourism/bloc/news_details/news_details_bloc.dart';
import 'package:sy_tourism/bloc/event_categories/event_categories_bloc.dart';
import 'package:sy_tourism/bloc/investments/investments_bloc.dart';
import 'package:sy_tourism/bloc/locations/locations_bloc.dart';
import 'package:sy_tourism/bloc/home/home_bloc.dart';
import 'package:sy_tourism/services/api_service.dart';
import 'package:sy_tourism/services/token_service.dart';
import 'package:sy_tourism/services/language_service.dart';
import 'package:sy_tourism/services/notification_service.dart';
import 'package:sy_tourism/views/screens/splash_screen.dart';
import 'package:onesignal_flutter/onesignal_flutter.dart';
import 'package:sy_tourism/services/notification_navigation_service.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  try {
    if (kDebugMode) {
      print('🚀 Starting app initialization...');
    }

    // Initialize services step by step
    await LanguageService.initializeLanguage();
    final apiService = await ApiService.getInstance();
    final tokenService = await TokenService.getInstance();

    // Initialize OneSignal first
    await NotificationService.instance.initialize(apiService: apiService);

    // Register notification opened handler to capture deep links
    OneSignal.Notifications.addClickListener((opened) {
      final n = opened.notification;
      String? url = n.launchUrl; // top-level app_url becomes launchUrl
      final data = Map<String, dynamic>.from(n.additionalData ?? {});
      url ??= data['app_url']?.toString() ?? data['url']?.toString();

      if (url != null && url.isNotEmpty) {
        NotificationNavigationService.instance.storeNotificationData({
          'app_url': url,
          'url': url,
          ...data,
        });
      } else {
        NotificationNavigationService.instance.storeNotificationData(data);
      }
    });

    if (kDebugMode) {
      print('✅ All services initialized successfully');
    }

    runApp(MyApp(apiService: apiService, tokenService: tokenService));
  } catch (e, stackTrace) {
    if (kDebugMode) {
      print('❌ Error during initialization: $e');
      print('Stack trace: $stackTrace');
    }

    // Fallback: run app without notification service
    try {
      final apiService = await ApiService.getInstance();
      final tokenService = await TokenService.getInstance();
      runApp(MyApp(apiService: apiService, tokenService: tokenService));
    } catch (fallbackError) {
      if (kDebugMode) {
        print('❌ Fallback also failed: $fallbackError');
      }
    }
  }
}

class MyApp extends StatelessWidget {
  final ApiService apiService;
  final TokenService tokenService;

  const MyApp({
    super.key,
    required this.apiService,
    required this.tokenService,
  });

  @override
  Widget build(BuildContext context) {
    return RepositoryProvider<ApiService>(
      create: (context) => apiService,
      child: MultiBlocProvider(
        providers: [
          BlocProvider(create: (_) => AppLanguageBloc()),
          BlocProvider(
            create:
                (_) => AuthBloc(
                  apiService: apiService,
                  tokenService: tokenService,
                ),
          ),
          BlocProvider(create: (_) => ProfileBloc(apiService: apiService)),
          BlocProvider(create: (_) => NewsBloc(apiService: apiService)),
          BlocProvider(create: (_) => EventsBloc(apiService: apiService)),
          BlocProvider(create: (_) => EventDetailsBloc(apiService: apiService)),
          BlocProvider(create: (_) => FavoritesBloc(apiService: apiService)),
          BlocProvider(create: (_) => NewsDetailsBloc(apiService: apiService)),
          BlocProvider(
            create: (_) => EventCategoriesBloc(apiService: apiService),
          ),
          BlocProvider(create: (_) => InvestmentsBloc(apiService: apiService)),
          BlocProvider(create: (_) => LocationsBloc(apiService)),
          BlocProvider(create: (_) => HomeBloc(apiService)),
        ],
        child: BlocBuilder<AppLanguageBloc, AppLanguageState>(
          builder: (context, state) {
            final isArabic = state.locale.languageCode == 'ar';

            return MaterialApp(
              title: 'Sy Tourism',
              debugShowCheckedModeBanner: false,
              locale: state.locale,
              supportedLocales: S.delegate.supportedLocales,
              localizationsDelegates: const [
                S.delegate,
                GlobalMaterialLocalizations.delegate,
                GlobalWidgetsLocalizations.delegate,
                GlobalCupertinoLocalizations.delegate,
              ],
              theme: ThemeData(
                fontFamily: isArabic ? 'Cairo' : 'SplineSans',
                textTheme: Theme.of(context).textTheme.apply(
                  fontFamily: isArabic ? 'Cairo' : 'SplineSans',
                ),
              ),
              // Remove the notification wrapper to avoid conflicts
              home: const SplashScreen(),
              //home: HomePage1(),
            );
          },
        ),
      ),
    );
  }
}
