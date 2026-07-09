/// Request model for POST /api/peminjamans
/// Laravel validates: alat_id, tgl_pinjam, tgl_kembali_rencana
class CreatePeminjamanRequest {
  final int alatId;
  final String tglPinjam;
  final String tglKembaliRencana;

  CreatePeminjamanRequest({
    required this.alatId,
    required this.tglPinjam,
    required this.tglKembaliRencana,
  });

  Map<String, dynamic> toJson() => {
        'alat_id': alatId,
        'tgl_pinjam': tglPinjam,
        'tgl_kembali_rencana': tglKembaliRencana,
      };
}
