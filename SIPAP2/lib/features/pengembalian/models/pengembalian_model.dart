/// Pengembalian model matching the Laravel 'pengembalians' table:
/// id, peminjaman_id, tgl_kembali_aktual, kondisi_alat, denda, timestamps
class PengembalianModel {
  final int id;
  final int peminjamanId;
  final String tglKembaliAktual;
  final String kondisiAlat;
  final double denda;
  final String? createdAt;

  PengembalianModel({
    required this.id,
    required this.peminjamanId,
    required this.tglKembaliAktual,
    required this.kondisiAlat,
    required this.denda,
    this.createdAt,
  });

  factory PengembalianModel.fromJson(Map<String, dynamic> json) {
    return PengembalianModel(
      id: json['id'] as int,
      peminjamanId: json['peminjaman_id'] as int,
      tglKembaliAktual: json['tgl_kembali_aktual'] as String? ?? '',
      kondisiAlat: json['kondisi_alat'] as String? ?? '',
      denda: (json['denda'] as num?)?.toDouble() ?? 0.0,
      createdAt: json['created_at'] as String?,
    );
  }

  Map<String, dynamic> toJson() => {
        'id': id,
        'peminjaman_id': peminjamanId,
        'tgl_kembali_aktual': tglKembaliAktual,
        'kondisi_alat': kondisiAlat,
        'denda': denda,
      };

  bool get adaDenda => denda > 0;
}
