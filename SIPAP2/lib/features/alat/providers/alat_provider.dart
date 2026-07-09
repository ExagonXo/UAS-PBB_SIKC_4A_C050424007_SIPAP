import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:sipap_flutter/features/auth/providers/auth_provider.dart';
import '../models/alat_model.dart';
import '../repositories/alat_repository.dart';

final alatRepositoryProvider = Provider((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return AlatRepository(apiClient: apiClient);
});

final alatListProvider = FutureProvider((ref) async {
  final repository = ref.watch(alatRepositoryProvider);
  return repository.getListAlat();
});

final alatDetailProvider =
    FutureProvider.family<AlatModel, int>((ref, alatId) async {
  final repository = ref.watch(alatRepositoryProvider);
  return repository.getDetailAlat(alatId);
});
