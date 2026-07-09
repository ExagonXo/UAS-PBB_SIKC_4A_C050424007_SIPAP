class LoginRequestModel {
  final String email;
  final String password;
  final String deviceName;

  LoginRequestModel({
    required this.email,
    required this.password,
    this.deviceName = 'flutter',
  });

  Map<String, dynamic> toJson() => {
        'email': email,
        'password': password,
        'device_name': deviceName,
      };
}
