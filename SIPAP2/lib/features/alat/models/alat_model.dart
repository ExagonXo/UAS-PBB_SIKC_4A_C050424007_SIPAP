import '../../../core/config/app_config.dart';

class AlatModel {
  final int id;
  final String namaAlat;
  final String? gambar;
  final String? deskripsi;
  final int stok;
  final String status; // tersedia | dipinjam | rusak

  AlatModel({
    required this.id,
    required this.namaAlat,
    this.gambar,
    this.deskripsi,
    required this.stok,
    required this.status,
  });

  // Laravel alats columns: id, nama_alat, deskripsi, gambar, stok, status
  factory AlatModel.fromJson(Map<String, dynamic> json) {
    String? gambarPath = json['gambar'] as String?;
    if (gambarPath != null && gambarPath.isNotEmpty && !gambarPath.startsWith('http')) {
      gambarPath = '${AppConfig.apiBaseUrl}/storage/$gambarPath';
    }
    return AlatModel(
      id: json['id'] as int,
      namaAlat: json['nama_alat'] as String? ?? '',
      gambar: gambarPath,
      deskripsi: json['deskripsi'] as String?,
      stok: json['stok'] as int? ?? 0,
      status: json['status'] as String? ?? 'tersedia',
    );
  }

  Map<String, dynamic> toJson() => {
        'id': id,
        'nama_alat': namaAlat,
        'gambar': gambar,
        'deskripsi': deskripsi,
        'stok': stok,
        'status': status,
      };

  bool get isAvailable => status == 'tersedia' && stok > 0;
}
