import 'package:flutter/foundation.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/config/app_config.dart';
import '../../../core/services/notification_service.dart';
import '../../../core/network/api_client.dart';
import '../../../core/storage/token_storage.dart';
import '../models/user_model.dart';
import '../repositories/auth_repository.dart';

// Providers for dependencies
final tokenStorageProvider = Provider((ref) => TokenStorage());

final apiClientProvider = Provider((ref) {
  final tokenStorage = ref.watch(tokenStorageProvider);
  return ApiClient(
    baseUrl: AppConfig.apiBaseUrl,
    readToken: () => tokenStorage.readToken(),
    onUnauthorized: () async {
      ref.read(authNotifierProvider.notifier).logout();
    },
  );
});

final authRepositoryProvider = Provider((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return AuthRepository(apiClient: apiClient);
});

// Auth State
class AuthState {
  final UserModel? user;
  final String? token;
  final bool isLoading;
  final String? error;

  AuthState({
    this.user,
    this.token,
    this.isLoading = false,
    this.error,
  });

  bool get isAuthenticated => token != null && user != null;

  AuthState copyWith({
    UserModel? user,
    String? token,
    bool? isLoading,
    String? error,
  }) {
    return AuthState(
      user: user ?? this.user,
      token: token ?? this.token,
      isLoading: isLoading ?? this.isLoading,
      error: error ?? this.error,
    );
  }
}

// Auth Notifier using Notifier pattern (Riverpod 3.x)
class AuthNotifier extends Notifier<AuthState> {
  late final AuthRepository _authRepository;
  late final TokenStorage _tokenStorage;

  @override
  AuthState build() {
    _authRepository = ref.watch(authRepositoryProvider);
    _tokenStorage = ref.watch(tokenStorageProvider);
    _initializeAuth();
    return AuthState(isLoading: true);
  }

  Future<void> _initializeAuth() async {
    try {
      final savedToken = await _tokenStorage.readToken();
      if (savedToken != null && savedToken.isNotEmpty) {
        final user = await _authRepository.getMe();
        state = state.copyWith(
          token: savedToken,
          user: user,
          isLoading: false,
        );
        _registerFcmTokenSafely();
      } else {
        state = state.copyWith(isLoading: false);
      }
    } catch (e) {
      await _tokenStorage.clearToken();
      state = AuthState(isLoading: false);
    }
  }

  Future<bool> login(String identifier, String password) async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      final response = await _authRepository.login(identifier, password);
      await _tokenStorage.saveToken(response.token);
      state = state.copyWith(
        user: response.user,
        token: response.token,
        isLoading: false,
      );
      _registerFcmTokenSafely();
      return true;
    } catch (e) {
      state = state.copyWith(
        isLoading: false,
        error: e.toString(),
      );
      return false;
    }
  }

  void logout() async {
    await _tokenStorage.clearToken();
    state = AuthState();
  }

  Future<void> _registerFcmTokenSafely() async {
    try {
      final fcmToken = await NotificationService.instance.getFcmToken();
      if (fcmToken != null) {
        await _authRepository.registerFcmToken(fcmToken);
      }
    } catch (e) {
      // Safe guard if Firebase is not initialized yet
      debugPrint('FCM registration skipped/failed: $e');
    }
  }
}

// Auth Provider
final authNotifierProvider = NotifierProvider<AuthNotifier, AuthState>(
  AuthNotifier.new,
);

// User provider
final userProvider = Provider((ref) {
  return ref.watch(authNotifierProvider).user;
});

// Is authenticated provider
final isAuthenticatedProvider = Provider((ref) {
  return ref.watch(authNotifierProvider).isAuthenticated;
});

// Token provider
final tokenProvider = Provider((ref) {
  return ref.watch(authNotifierProvider).token;
});
