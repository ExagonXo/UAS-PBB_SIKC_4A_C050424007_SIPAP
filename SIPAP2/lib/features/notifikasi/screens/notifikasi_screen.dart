import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import '../models/notifikasi_model.dart';
import '../providers/notifikasi_provider.dart';

class NotifikasiScreen extends ConsumerStatefulWidget {
  const NotifikasiScreen({super.key});

  @override
  ConsumerState<NotifikasiScreen> createState() => _NotifikasiScreenState();
}

class _NotifikasiScreenState extends ConsumerState<NotifikasiScreen> {
  @override
  void initState() {
    super.initState();
    // Fetch notifications list when opening screen
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(notifikasiNotifierProvider.notifier).refreshList();
    });
  }

  @override
  Widget build(BuildContext context) {
    final notifikasiListAsync = ref.watch(notifikasiListProvider);
    final localNotifikasiList = ref.watch(notifikasiNotifierProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Pusat Notifikasi'),
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.mark_chat_read_outlined),
            tooltip: 'Tandai Semua Dibaca',
            onPressed: () => _markAllAsRead(context),
          ),
        ],
      ),
      body: notifikasiListAsync.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (err, stack) => Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.error_outline, size: 64, color: Colors.red),
              const SizedBox(height: 16),
              Text('Error: $err'),
              const SizedBox(height: 16),
              FilledButton(
                onPressed: () {
                  ref.invalidate(notifikasiListProvider);
                  ref.read(notifikasiNotifierProvider.notifier).refreshList();
                },
                child: const Text('Coba Lagi'),
              ),
            ],
          ),
        ),
        data: (_) {
          final displayList = localNotifikasiList.isNotEmpty
              ? localNotifikasiList
              : notifikasiListAsync.value ?? [];

          if (displayList.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.notifications_off_outlined,
                    size: 72,
                    color: Colors.grey[300],
                  ),
                  const SizedBox(height: 16),
                  Text(
                    'Tidak ada notifikasi baru',
                    style: Theme.of(context).textTheme.titleMedium?.copyWith(
                          color: Colors.grey[600],
                          fontWeight: FontWeight.bold,
                        ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'Kami akan memberi tahu saat ada status peminjaman berubah.',
                    textAlign: TextAlign.center,
                    style: Theme.of(context).textTheme.bodySmall?.copyWith(
                          color: Colors.grey,
                        ),
                  ),
                ],
              ),
            );
          }

          return RefreshIndicator(
            onRefresh: () async {
              ref.invalidate(notifikasiListProvider);
              await ref.read(notifikasiNotifierProvider.notifier).refreshList();
            },
            child: ListView.builder(
              padding: const EdgeInsets.symmetric(vertical: 8),
              itemCount: displayList.length,
              itemBuilder: (context, index) {
                final notif = displayList[index];
                return _NotifikasiItem(
                  notifikasi: notif,
                  onTap: () => _handleNotifikasiTap(context, notif),
                );
              },
            ),
          );
        },
      ),
    );
  }

  Future<void> _markAllAsRead(BuildContext context) async {
    try {
      await ref.read(notifikasiNotifierProvider.notifier).markAllAsRead();
      ref.invalidate(unreadCountProvider);
      if (!context.mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Semua notifikasi ditandai dibaca'),
          backgroundColor: Colors.green,
        ),
      );
    } catch (e) {
      if (!context.mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Gagal menandai dibaca: $e'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  void _handleNotifikasiTap(BuildContext context, NotifikasiModel notif) async {
    // Mark as read
    if (!notif.isRead) {
      try {
        await ref.read(notifikasiNotifierProvider.notifier).markAsRead(notif.id);
        ref.invalidate(unreadCountProvider);
      } catch (e) {
        debugPrint('Failed to mark notification as read: $e');
      }
    }
  }
}

class _NotifikasiItem extends StatelessWidget {
  final NotifikasiModel notifikasi;
  final VoidCallback onTap;

  const _NotifikasiItem({required this.notifikasi, required this.onTap});

  String _formatDate(String? dateStr) {
    if (dateStr == null) return '';
    try {
      final dateTime = DateTime.parse(dateStr).toLocal();
      final format = DateFormat('dd MMM yyyy, HH:mm');
      return format.format(dateTime);
    } catch (e) {
      return dateStr;
    }
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final isDitolak = notifikasi.judul.toLowerCase().contains('tolak');
    final isDisetujui = notifikasi.judul.toLowerCase().contains('setuju');
    
    final tipeColor = isDitolak 
        ? Colors.red 
        : (isDisetujui ? Colors.green : Colors.blue);

    final tipeIcon = isDitolak
        ? Icons.cancel_outlined
        : (isDisetujui ? Icons.check_circle_outline : Icons.notifications_none);

    return Container(
      color: notifikasi.isRead ? Colors.transparent : colorScheme.primaryContainer.withValues(alpha: 0.1),
      child: ListTile(
        onTap: onTap,
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
        leading: CircleAvatar(
          backgroundColor: tipeColor.withValues(alpha: 0.15),
          foregroundColor: tipeColor,
          child: Icon(tipeIcon),
        ),
        title: Row(
          children: [
            Expanded(
              child: Text(
                notifikasi.judul,
                style: TextStyle(
                  fontWeight: notifikasi.isRead ? FontWeight.w500 : FontWeight.bold,
                  fontSize: 14,
                ),
              ),
            ),
            if (!notifikasi.isRead)
              Container(
                width: 8,
                height: 8,
                decoration: BoxDecoration(
                  color: colorScheme.primary,
                  shape: BoxShape.circle,
                ),
              ),
          ],
        ),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SizedBox(height: 4),
            Text(
              notifikasi.pesan,
              style: Theme.of(context).textTheme.bodySmall?.copyWith(
                    color: notifikasi.isRead ? Colors.grey[600] : Colors.black87,
                  ),
            ),
            if (notifikasi.createdAt != null) ...[
              const SizedBox(height: 6),
              Text(
                _formatDate(notifikasi.createdAt),
                style: Theme.of(context).textTheme.labelSmall?.copyWith(
                      color: Colors.grey[500],
                    ),
              ),
            ],
          ],
        ),
      ),
    );
  }
}
