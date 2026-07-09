class UserModel {
  final int id;
  final String name;
  final String role;
  final String identifier;

  UserModel({
    required this.id,
    required this.name,
    required this.role,
    required this.identifier,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] as int,
      name: json['name'] as String,
      role: json['role'] as String,
      identifier: json['identifier'] as String,
    );
  }

  Map<String, dynamic> toJson() => {
        'id': id,
        'name': name,
        'role': role,
        'identifier': identifier,
      };
}
