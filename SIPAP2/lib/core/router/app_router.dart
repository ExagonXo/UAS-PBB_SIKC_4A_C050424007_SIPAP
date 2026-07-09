import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../features/alat/screens/alat_detail_screen.dart';
import '../../features/alat/screens/alat_list_screen.dart';
import '../../features/auth/providers/auth_provider.dart';
import '../../features/auth/screens/home_screen.dart';
import '../../features/auth/screens/login_screen.dart';
import '../../features/auth/screens/splash_screen.dart';
import '../../features/peminjaman/screens/detail_peminjaman_screen.dart';
import '../../features/peminjaman/screens/dosen_konfirmasi_screen.dart';
import '../../features/peminjaman/screens/peminjaman_form_screen.dart';
import '../../features/peminjaman/screens/riwayat_peminjaman_screen.dart';
import '../../features/notifikasi/screens/notifikasi_screen.dart';
import '../../features/pengembalian/screens/pengembalian_form_screen.dart';
import 'package:flutter/material.dart';
import '../constants/app_routes.dart';

final rootNavigatorKey = GlobalKey<NavigatorState>();

final appRouterProvider = Provider<GoRouter>((ref) {
  final authState = ref.watch(authNotifierProvider);
  return GoRouter(
    navigatorKey: rootNavigatorKey,
    initialLocation: AppRoutes.splash,
    redirect: (context, state) {
      final isAuthenticated = authState.isAuthenticated;
      final isLoading = authState.isLoading;
      final isSplash = state.uri.path == AppRoutes.splash;
      final isLogin = state.uri.path == AppRoutes.login;

      // While auth is still being checked, do not perform any redirect
      if (isLoading) {
        return null;
      }

      // Auth check done: if still on splash, redirect to appropriate screen
      if (isSplash) {
        return isAuthenticated ? AppRoutes.home : AppRoutes.login;
      }

      // Guard: if not authenticated and not on login page, redirect to login
      if (!isAuthenticated && !isLogin) {
        return AppRoutes.login;
      }

      // Guard: if authenticated and on login, redirect to home
      if (isAuthenticated && isLogin) {
        return AppRoutes.home;
      }

      return null;
    },
    routes: [
      GoRoute(
        path: AppRoutes.splash,
        builder: (context, state) => const SplashScreen(),
      ),
      GoRoute(
        path: AppRoutes.login,
        builder: (context, state) => const LoginScreen(),
      ),
      GoRoute(
        path: AppRoutes.home,
        builder: (context, state) => const HomeScreen(),
      ),
      // Alat routes
      GoRoute(
        path: AppRoutes.alatList,
        builder: (context, state) => const AlatListScreen(),
      ),
      GoRoute(
        path: AppRoutes.alatDetail,
        builder: (context, state) {
          final id = state.pathParameters['id'] ?? '';
          return AlatDetailScreen(alatId: int.parse(id));
        },
      ),
      // Peminjaman routes
      GoRoute(
        path: AppRoutes.peminjamanList,
        builder: (context, state) => const RiwayatPeminjamanScreen(),
      ),
      GoRoute(
        path: AppRoutes.peminjamanForm,
        builder: (context, state) {
          final alatId = state.uri.queryParameters['alatId'];
          return PeminjamanFormScreen(
            alatId: alatId != null ? int.tryParse(alatId) : null,
          );
        },
      ),
      GoRoute(
        path: AppRoutes.peminjamanDetail,
        builder: (context, state) {
          final id = state.pathParameters['id'] ?? '';
          return DetailPeminjamanScreen(peminjamanId: int.parse(id));
        },
      ),
      // Pengembalian routes
      GoRoute(
        path: AppRoutes.pengembalianForm,
        builder: (context, state) {
          final peminjamanId =
              state.uri.queryParameters['peminjamanId'] ?? '0';
          return PengembalianFormScreen(
            peminjamanId: int.parse(peminjamanId),
          );
        },
      ),
      // Dosen routes
      GoRoute(
        path: AppRoutes.dosenKonfirmasi,
        builder: (context, state) => const DosenKonfirmasiScreen(),
      ),
      // Notifikasi route
      GoRoute(
        path: AppRoutes.notifikasiList,
        builder: (context, state) => const NotifikasiScreen(),
      ),
    ],
  );
});