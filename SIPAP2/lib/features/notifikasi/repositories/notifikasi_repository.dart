import 'package:sipap_flutter/core/network/api_client.dart';
import '../models/notifikasi_model.dart';

class NotifikasiRepository {
  final ApiClient apiClient;

  NotifikasiRepository({required this.apiClient});

  Future<List<NotifikasiModel>> getListNotifikasi() async {
    final response = await apiClient.dio.get('/api/notifications');
    final List<dynamic> list = response.data as List<dynamic>;
    return list
        .map((item) => NotifikasiModel.fromJson(item as Map<String, dynamic>))
        .toList();
  }

  /// PATCH /api/notifications/{id}/read
  Future<void> markAsRead(int notifikasiId) async {
    await apiClient.dio.patch('/api/notifications/$notifikasiId/read');
  }

  /// Count unread notifications locally from the list
  /// (no dedicated endpoint exists in the backend)
  Future<int> getUnreadCount() async {
    try {
      final list = await getListNotifikasi();
      return list.where((n) => !n.isRead).length;
    } catch (_) {
      return 0;
    }
  }
}
