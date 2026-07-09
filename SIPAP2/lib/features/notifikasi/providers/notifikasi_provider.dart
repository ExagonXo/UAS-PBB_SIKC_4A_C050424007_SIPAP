import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:sipap_flutter/features/auth/providers/auth_provider.dart';
import '../models/notifikasi_model.dart';
import '../repositories/notifikasi_repository.dart';

final notifikasiRepositoryProvider = Provider((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return NotifikasiRepository(apiClient: apiClient);
});

final notifikasiListProvider = FutureProvider((ref) async {
  final repository = ref.watch(notifikasiRepositoryProvider);
  return repository.getListNotifikasi();
});

final unreadCountProvider = FutureProvider((ref) async {
  final repository = ref.watch(notifikasiRepositoryProvider);
  return repository.getUnreadCount();
});

class NotifikasiNotifier extends Notifier<List<NotifikasiModel>> {
  late NotifikasiRepository _repository;

  @override
  List<NotifikasiModel> build() {
    _repository = ref.watch(notifikasiRepositoryProvider);
    return [];
  }

  Future<void> refreshList() async {
    try {
      state = await _repository.getListNotifikasi();
    } catch (e) {
      rethrow;
    }
  }

  Future<void> markAsRead(int notifikasiId) async {
    try {
      await _repository.markAsRead(notifikasiId);
      // Update local state
      state = state.map((item) {
        if (item.id == notifikasiId) {
          return NotifikasiModel(
            id: item.id,
            userId: item.userId,
            judul: item.judul,
            pesan: item.pesan,
            isRead: true,
            createdAt: item.createdAt,
          );
        }
        return item;
      }).toList();
    } catch (e) {
      rethrow;
    }
  }

  Future<void> markAllAsRead() async {
    try {
      // Loop through all unread and mark as read
      for (final item in state) {
        if (!item.isRead) {
          await _repository.markAsRead(item.id);
        }
      }
      // Update local state
      state = state.map((item) {
        return NotifikasiModel(
          id: item.id,
          userId: item.userId,
          judul: item.judul,
          pesan: item.pesan,
          isRead: true,
          createdAt: item.createdAt,
        );
      }).toList();
    } catch (e) {
      rethrow;
    }
  }

  void addNotifikasi(NotifikasiModel notifikasi) {
    state = [notifikasi, ...state];
  }
}

final notifikasiNotifierProvider =
    NotifierProvider<NotifikasiNotifier, List<NotifikasiModel>>(
  NotifikasiNotifier.new,
);
