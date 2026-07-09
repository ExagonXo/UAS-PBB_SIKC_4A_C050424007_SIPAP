import '../../../features/alat/models/alat_model.dart';

/// Peminjaman model matching the Laravel 'peminjamans' table:
/// id, user_id, alat_id, tgl_pinjam, tgl_kembali_rencana, status, timestamps
/// With eager-loaded 'alat' relation when returned from API.
class PeminjamanModel {
  final int id;
  final int userId;
  final int alatId;
  final String tglPinjam;
  final String tglKembaliRencana;
  final String status; // pending | disetujui | ditolak | selesai
  final AlatModel? alat; // eager-loaded relation

  PeminjamanModel({
    required this.id,
    required this.userId,
    required this.alatId,
    required this.tglPinjam,
    required this.tglKembaliRencana,
    required this.status,
    this.alat,
  });

  factory PeminjamanModel.fromJson(Map<String, dynamic> json) {
    return PeminjamanModel(
      id: json['id'] as int,
      userId: json['user_id'] as int,
      alatId: json['alat_id'] as int,
      tglPinjam: json['tgl_pinjam'] as String? ?? '',
      tglKembaliRencana: json['tgl_kembali_rencana'] as String? ?? '',
      status: json['status'] as String? ?? 'pending',
      alat: json['alat'] != null
          ? AlatModel.fromJson(json['alat'] as Map<String, dynamic>)
          : null,
    );
  }

  Map<String, dynamic> toJson() => {
        'id': id,
        'user_id': userId,
        'alat_id': alatId,
        'tgl_pinjam': tglPinjam,
        'tgl_kembali_rencana': tglKembaliRencana,
        'status': status,
      };

  String get statusText {
    switch (status) {
      case 'pending':
        return 'Menunggu';
      case 'disetujui':
        return 'Disetujui';
      case 'menunggu_kembali':
        return 'Menunggu Verifikasi';
      case 'ditolak':
        return 'Ditolak';
      case 'selesai':
        return 'Selesai';
      default:
        return status;
    }
  }

  String get alatNama => alat?.namaAlat ?? 'Alat #$alatId';
}
