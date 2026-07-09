import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:sipap_flutter/features/auth/providers/auth_provider.dart';
import '../models/pengembalian_model.dart';
import '../repositories/pengembalian_repository.dart';

final pengembalianRepositoryProvider = Provider((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return PengembalianRepository(apiClient: apiClient);
});

class PengembalianNotifier extends Notifier<List<PengembalianModel>> {
  late PengembalianRepository _repository;

  @override
  List<PengembalianModel> build() {
    _repository = ref.watch(pengembalianRepositoryProvider);
    return [];
  }

  Future<PengembalianModel> createPengembalian({
    required int peminjamanId,
    required String kondisiAlat,
  }) async {
    try {
      final result = await _repository.createPengembalian(
        peminjamanId: peminjamanId,
        kondisiAlat: kondisiAlat,
      );
      state = [...state, result];
      return result;
    } catch (e) {
      rethrow;
    }
  }
}

final pengembalianNotifierProvider =
    NotifierProvider<PengembalianNotifier, List<PengembalianModel>>(
  PengembalianNotifier.new,
);
