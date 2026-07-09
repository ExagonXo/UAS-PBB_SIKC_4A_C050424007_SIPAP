import 'package:dio/dio.dart';

import '../../../core/network/api_client.dart';
import '../models/login_request_model.dart';
import '../models/login_response_model.dart';
import '../models/user_model.dart';

abstract interface class IAuthRepository {
  Future<LoginResponseModel> login(String email, String password);
  Future<UserModel> getMe();
  Future<void> registerFcmToken(String fcmToken);
  Future<void> logout();
}

class AuthRepository implements IAuthRepository {
  AuthRepository({required ApiClient apiClient}) : _apiClient = apiClient;

  final ApiClient _apiClient;

  @override
  Future<LoginResponseModel> login(String email, String password) async {
    try {
      final request = LoginRequestModel(email: email, password: password);
      final response = await _apiClient.dio.post(
        '/api/login',
        data: request.toJson(),
      );

      // Laravel Sanctum returns token and user directly (no wrapper)
      final data = response.data as Map<String, dynamic>;
      return LoginResponseModel.fromJson(data);
    } on DioException catch (e) {
      // Validation error (422) or wrong credentials
      final responseData = e.response?.data;
      if (responseData is Map<String, dynamic>) {
        final message = responseData['message'] as String?;
        final errors = responseData['errors'] as Map<String, dynamic>?;
        if (errors != null && errors.isNotEmpty) {
          final firstError = errors.values.first;
          if (firstError is List && firstError.isNotEmpty) {
            throw Exception(firstError.first.toString());
          }
        }
        if (message != null) throw Exception(message);
      }
      throw Exception(e.message ?? 'Login error');
    } catch (e) {
      rethrow;
    }
  }

  @override
  Future<UserModel> getMe() async {
    try {
      final response = await _apiClient.dio.get('/api/me');
      final data = response.data as Map<String, dynamic>;
      return UserModel.fromJson(data);
    } on DioException catch (e) {
      throw Exception(e.message ?? 'Error fetching user info');
    } catch (e) {
      rethrow;
    }
  }

  @override
  Future<void> registerFcmToken(String fcmToken) async {
    try {
      await _apiClient.dio.post(
        '/api/fcm-token',
        data: {'fcm_token': fcmToken},
      );
    } on DioException catch (e) {
      throw Exception(e.message ?? 'Error registering FCM token');
    } catch (e) {
      rethrow;
    }
  }

  @override
  Future<void> logout() async {
    try {
      await _apiClient.dio.post('/api/logout');
    } catch (_) {
      // Ignore logout errors
    }
  }
}
