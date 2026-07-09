import 'package:dio/dio.dart';

class ApiClient {
  ApiClient({
    required String baseUrl,
    required Future<String?> Function() readToken,
    required Future<void> Function() onUnauthorized,
  })  : _readToken = readToken,
        _onUnauthorized = onUnauthorized,
        dio = Dio(
          BaseOptions(
            baseUrl: baseUrl,
            connectTimeout: const Duration(seconds: 30),
            receiveTimeout: const Duration(seconds: 30),
            sendTimeout: const Duration(seconds: 30),
            contentType: Headers.jsonContentType,
            responseType: ResponseType.json,
            headers: const {'Accept': 'application/json'},
          ),
        ) {
    dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          final token = await _readToken();
          if (token != null && token.isNotEmpty) {
            options.headers['Authorization'] = 'Bearer $token';
          }
          handler.next(options);
        },
        onError: (error, handler) async {
          if (error.response?.statusCode == 401) {
            await _onUnauthorized();
          }
          handler.next(error);
        },
      ),
    );
  }

  final Future<String?> Function() _readToken;
  final Future<void> Function() _onUnauthorized;
  final Dio dio;
}