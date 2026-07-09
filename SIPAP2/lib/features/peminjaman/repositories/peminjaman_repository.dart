import 'package:dio/dio.dart';
import 'package:sipap_flutter/core/network/api_client.dart';
import '../models/peminjaman_model.dart';
import '../models/create_peminjaman_request.dart';

class PeminjamanRepository {
  final ApiClient apiClient;

  PeminjamanRepository({required this.apiClient});

  Future<List<PeminjamanModel>> getListPeminjaman() async {
    try {
      final response = await apiClient.dio.get('/api/peminjamans');
      final List<dynamic> list = response.data as List<dynamic>;
      return list
          .map((item) => PeminjamanModel.fromJson(item as Map<String, dynamic>))
          .toList();
    } on DioException catch (e) {
      throw Exception(e.message ?? 'Gagal mengambil riwayat peminjaman');
    }
  }

  Future<PeminjamanModel> getDetailPeminjaman(int id) async {
    try {
      final response = await apiClient.dio.get('/api/peminjamans/$id');
      return PeminjamanModel.fromJson(response.data as Map<String, dynamic>);
    } on DioException catch (e) {
      throw Exception(e.message ?? 'Gagal mengambil detail peminjaman');
    }
  }

  Future<PeminjamanModel> createPeminjaman(
      CreatePeminjamanRequest request) async {
    try {
      final response = await apiClient.dio.post(
        '/api/peminjamans',
        data: request.toJson(),
      );
      return PeminjamanModel.fromJson(response.data as Map<String, dynamic>);
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
      throw Exception(e.message ?? 'Gagal mengajukan peminjaman');
    } catch (e) {
      rethrow;
    }
  }

  /// Admin only: PATCH /api/peminjamans/{id}/status
  Future<PeminjamanModel> updateStatusPeminjaman(
      int peminjamanId, String status) async {
    try {
      final response = await apiClient.dio.patch(
        '/api/peminjamans/$peminjamanId/status',
        data: {'status': status},
      );
      return PeminjamanModel.fromJson(response.data as Map<String, dynamic>);
    } on DioException catch (e) {
      throw Exception(e.message ?? 'Gagal memperbarui status peminjaman');
    }
  }
}
