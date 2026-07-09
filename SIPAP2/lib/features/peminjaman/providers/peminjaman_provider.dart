import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:sipap_flutter/features/auth/providers/auth_provider.dart';
import 'package:sipap_flutter/features/auth/models/user_model.dart';
import '../models/peminjaman_model.dart';
import '../models/create_peminjaman_request.dart';
import '../repositories/peminjaman_repository.dart';

final peminjamanRepositoryProvider = Provider((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return PeminjamanRepository(apiClient: apiClient);
});

final peminjamanListProvider = FutureProvider((ref) async {
  final repository = ref.watch(peminjamanRepositoryProvider);
  return repository.getListPeminjaman();
});

final dosenListProvider = FutureProvider<List<UserModel>>((ref) async {
  // Fallback / Mock dosen list because Laravel backend has no dosen endpoint and no dosen_id in DB
  return [
    UserModel(
        id: 101,
        name: 'Dr. Arifin, M.T.',
        role: 'dosen',
        identifier: '198001012005011001'),
    UserModel(
        id: 102,
        name: 'Budi Utomo, M.Cs.',
        role: 'dosen',
        identifier: '198502022010121002'),
    UserModel(
        id: 103,
        name: 'Sri Wahyuni, M.T.',
        role: 'dosen',
        identifier: '197803032003122001'),
  ];
});

class PeminjamanNotifier extends Notifier<List<PeminjamanModel>> {
  late PeminjamanRepository _repository;

  @override
  List<PeminjamanModel> build() {
    _repository = ref.watch(peminjamanRepositoryProvider);
    return [];
  }

  Future<PeminjamanModel> createPeminjaman(
      CreatePeminjamanRequest request) async {
    try {
      final result = await _repository.createPeminjaman(request);
      state = [...state, result];
      return result;
    } catch (e) {
      rethrow;
    }
  }

  /// Buat peminjaman untuk banyak alat sekaligus.
  /// Mengirim request satu per satu dan mengembalikan list hasil yang berhasil.
  Future<List<PeminjamanModel>> createMultiplePeminjaman(
      List<int> alatIds, String tglPinjam, String tglKembaliRencana) async {
    final results = <PeminjamanModel>[];
    for (final id in alatIds) {
      final req = CreatePeminjamanRequest(
        alatId: id,
        tglPinjam: tglPinjam,
        tglKembaliRencana: tglKembaliRencana,
      );
      final result = await _repository.createPeminjaman(req);
      state = [...state, result];
      results.add(result);
    }
    return results;
  }

  Future<void> refreshList() async {
    try {
      state = await _repository.getListPeminjaman();
    } catch (e) {
      rethrow;
    }
  }

  Future<void> updateStatus(int peminjamanId, String status) async {
    try {
      final updatedPeminjaman =
          await _repository.updateStatusPeminjaman(peminjamanId, status);
      // Update local state with the returned object from backend
      state = state.map((item) {
        if (item.id == peminjamanId) {
          return updatedPeminjaman;
        }
        return item;
      }).toList();
    } catch (e) {
      rethrow;
    }
  }
}

final peminjamanNotifierProvider =
    NotifierProvider<PeminjamanNotifier, List<PeminjamanModel>>(
  PeminjamanNotifier.new,
);
