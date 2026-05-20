import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:sy_tourism/bloc/auth/auth_bloc.dart';
import 'package:sy_tourism/bloc/auth/auth_event.dart';
import 'package:sy_tourism/bloc/auth/auth_state.dart';
import 'package:sy_tourism/views/screens/kind_of_customer.dart';
import 'package:sy_tourism/views/screens/navigation_bar.dart';
import 'package:sy_tourism/utils/app_color.dart';
import 'package:sy_tourism/services/notification_navigation_service.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    // فحص حالة المصادقة عند بدء التطبيق
    context.read<AuthBloc>().add(AuthCheckRequested());

    // علّم الخدمة إن التطبيق جاهز للتنقل بعد أول فريم
    WidgetsBinding.instance.addPostFrameCallback((_) {
      NotificationNavigationService.instance.markAppReady(context);
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColor.white,
      body: BlocListener<AuthBloc, AuthState>(
        listener: (context, state) {
          if (state is AuthAuthenticated) {
            // المستخدم مسجل دخول، انتقل للصفحة الرئيسية
            // علّم الخدمة قبل الانتقال (زيادة موثوقية)
            NotificationNavigationService.instance.markAppReady(context);
            Navigator.of(context).pushReplacement(
              MaterialPageRoute(
                builder: (context) => const MainNavigationPage(),
              ),
            );
          } else if (state is AuthUnauthenticated || state is AuthError) {
            // المستخدم غير مسجل دخول، انتقل لصفحة اختيار نوع العميل
            // علّم الخدمة قبل الانتقال (زيادة موثوقية)
            NotificationNavigationService.instance.markAppReady(context);
            Navigator.of(context).pushReplacement(
              MaterialPageRoute(builder: (context) => const KindOfCustomer()),
            );
          }
        },
        child: BlocBuilder<AuthBloc, AuthState>(
          builder: (context, state) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  // شعار التطبيق
                  Image.asset(
                    'assets/images/Logo.png',
                    height: 120,
                    width: 120,
                  ),
                  const SizedBox(height: 30),

                  // نص الترحيب
                  const Text(
                    'مرحباً بك',
                    style: TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                      color: Colors.black87,
                    ),
                  ),
                  const SizedBox(height: 10),

                  const Text(
                    'تطبيق السياحة والاستثمار',
                    style: TextStyle(fontSize: 16, color: Colors.grey),
                  ),
                  const SizedBox(height: 40),

                  // مؤشر التحميل
                  if (state is AuthLoading || state is AuthInitial)
                    const CircularProgressIndicator(
                      valueColor: AlwaysStoppedAnimation<Color>(
                        AppColor.primaryColor,
                      ),
                    ),
                ],
              ),
            );
          },
        ),
      ),
    );
  }
}
