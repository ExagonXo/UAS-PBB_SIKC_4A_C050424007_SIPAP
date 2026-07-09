import 'package:dio/dio.dart';
import 'package:sipap_flutter/core/network/api_client.dart';
import '../models/pengembalian_model.dart';

class PengembalianRepository {
  final ApiClient apiClient;

  PengembalianRepository({required this.apiClient});

  /// POST /api/pengembalians (Admin only route in Laravel)
  /// Validates: peminjaman_id, kondisi_alat
  Future<PengembalianModel> createPengembalian({
    required int peminjamanId,
    required String kondisiAlat,
  }) async {
    try {
      final response = await apiClient.dio.post(
        '/api/pengembalians',
        data: {
          'peminjaman_id': peminjamanId,
          'kondisi_alat': kondisiAlat,
        },
      );
      return PengembalianModel.fromJson(response.data as Map<String, dynamic>);
    } on DioException catch (e) {
      final responseData = e.response?.data;
      if (responseData is Map<String, dynamic>) {
        final message = responseData['message'] as String?;
        final errors = responseData['errors'] as Map<String, dynamic>?;
        if (errors != null && errors.isNotEmpty) {
          final firstError = errors.values.first;
          if (firstError is List && firstError.isNotEmpty) {
            throw Exception(firstError.first.toString());
          }
        }
        if (message != null) throw Exception(message);
      }
      throw Exception(e.message ?? 'Gagal memproses pengembalian');
    } catch (e) {
      rethrow;
    }
  }
}
