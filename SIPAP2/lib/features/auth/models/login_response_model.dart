import 'user_model.dart';

class LoginResponseModel {
  final String token;
  final UserModel user;

  LoginResponseModel({
    required this.token,
    required this.user,
  });

  // Laravel Sanctum returns: { "token": "...", "user": { ... } }
  factory LoginResponseModel.fromJson(Map<String, dynamic> json) {
    return LoginResponseModel(
      token: json['token'] as String,
      user: UserModel.fromJson(json['user'] as Map<String, dynamic>),
    );
  }
}
