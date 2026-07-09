import 'package:sipap_flutter/core/network/api_client.dart';
import '../models/alat_model.dart';

class AlatRepository {
  final ApiClient apiClient;

  AlatRepository({required this.apiClient});

  Future<List<AlatModel>> getListAlat() async {
    final response = await apiClient.dio.get('/api/alats');
    // Laravel returns a JSON array directly
    final List<dynamic> list = response.data as List<dynamic>;
    return list
        .map((item) => AlatModel.fromJson(item as Map<String, dynamic>))
        .toList();
  }

  Future<AlatModel> getDetailAlat(int alatId) async {
    final response = await apiClient.dio.get('/api/alats/$alatId');
    return AlatModel.fromJson(response.data as Map<String, dynamic>);
  }
}
