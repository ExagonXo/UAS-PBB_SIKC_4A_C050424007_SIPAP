import 'package:flutter_dotenv/flutter_dotenv.dart';

class AppConfig {
  AppConfig._();

  static const String _defaultBaseUrl = 'http://10.0.2.2:8000';
  static const String _apiBaseUrlDefine = String.fromEnvironment('API_BASE_URL');

  static Future<void> load() async {
    try {
      await dotenv.load(fileName: '.env');
    } catch (_) {
      // Falls back to dart-define or the default base URL when .env is absent.
    }
  }

  static String get apiBaseUrl {
    if (_apiBaseUrlDefine.trim().isNotEmpty) {
      return _apiBaseUrlDefine.trim();
    }

    final dotenvBaseUrl = dotenv.env['API_BASE_URL']?.trim() ?? '';
    if (dotenvBaseUrl.isNotEmpty) {
      return dotenvBaseUrl;
    }

    return _defaultBaseUrl;
  }
}