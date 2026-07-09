/// Notifikasi model matching the Laravel 'notifikasis' table:
/// id, user_id, judul, pesan, is_read, timestamps
class NotifikasiModel {
  final int id;
  final int userId;
  final String judul;
  final String pesan;
  final bool isRead;
  final String? createdAt;

  NotifikasiModel({
    required this.id,
    required this.userId,
    required this.judul,
    required this.pesan,
    required this.isRead,
    this.createdAt,
  });

  factory NotifikasiModel.fromJson(Map<String, dynamic> json) {
    return NotifikasiModel(
      id: json['id'] as int,
      userId: json['user_id'] as int,
      judul: json['judul'] as String? ?? '',
      pesan: json['pesan'] as String? ?? '',
      isRead: json['is_read'] is bool
          ? json['is_read'] as bool
          : (json['is_read'] == 1 || json['is_read'] == '1' || json['is_read'] == true),
      createdAt: json['created_at'] as String?,
    );
  }

  Map<String, dynamic> toJson() => {
        'id': id,
        'user_id': userId,
        'judul': judul,
        'pesan': pesan,
        'is_read': isRead,
        'created_at': createdAt,
      };

  bool get dibaca => isRead;
}
