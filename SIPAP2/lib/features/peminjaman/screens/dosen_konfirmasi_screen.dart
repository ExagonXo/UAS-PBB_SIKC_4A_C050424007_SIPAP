import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:sipap_flutter/features/auth/providers/auth_provider.dart';
import 'package:sipap_flutter/features/peminjaman/models/peminjaman_model.dart';
import 'package:sipap_flutter/features/peminjaman/providers/peminjaman_provider.dart';

class DosenKonfirmasiScreen extends ConsumerWidget {
  const DosenKonfirmasiScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    // Dosen only: menampilkan peminjaman pending dari mahasiswa
    final peminjamanListAsync = ref.watch(peminjamanListProvider);
    final user = ref.watch(userProvider);

    if (user == null || user.role.toLowerCase() != 'dosen') {
      return const Scaffold(
        body: Center(child: Text('Akses ditolak')),
      );
    }

    return Scaffold(
      appBar: AppBar(
        title: const Text('Konfirmasi Pengajuan'),
        elevation: 0,
      ),
      body: peminjamanListAsync.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (err, _) => Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.error_outline, size: 64, color: Colors.red),
              const SizedBox(height: 16),
              Text('Error: $err'),
              const SizedBox(height: 16),
              FilledButton(
                onPressed: () {
                  ref.invalidate(peminjamanListProvider);
                  ref.read(peminjamanListProvider.future);
                },
                child: const Text('Coba Lagi'),
              ),
            ],
          ),
        ),
        data: (list) {
          // Filter hanya yang pending
          final pending =
              list.where((p) => p.status == 'pending').toList();

          if (pending.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.check_circle_outline,
                      size: 72, color: Colors.green[300]),
                  const SizedBox(height: 16),
                  Text(
                    'Tidak ada pengajuan\nyang perlu dikonfirmasi',
                    textAlign: TextAlign.center,
                    style: Theme.of(context).textTheme.bodyLarge?.copyWith(
                          color: Colors.grey[600],
                        ),
                  ),
                ],
              ),
            );
          }

          return RefreshIndicator(
            onRefresh: () async {
              ref.invalidate(peminjamanListProvider);
              await ref.read(peminjamanListProvider.future);
            },
            child: ListView.builder(
              padding: const EdgeInsets.all(12),
              itemCount: pending.length,
              itemBuilder: (context, index) {
                return _KonfirmasiCard(peminjaman: pending[index]);
              },
            ),
          );
        },
      ),
    );
  }
}

class _KonfirmasiCard extends ConsumerWidget {
  final PeminjamanModel peminjaman;

  const _KonfirmasiCard({required this.peminjaman});

  Future<void> _handleKeputusan(
    BuildContext context,
    WidgetRef ref,
    String keputusan,
  ) async {
    String? catatan;

    if (keputusan == 'tolak') {
      catatan = await showDialog<String>(
        context: context,
        builder: (ctx) {
          final catatanController = TextEditingController();
          return AlertDialog(
            title: const Text('Alasan Penolakan'),
            content: TextField(
              controller: catatanController,
              maxLines: 3,
              autofocus: true,
              decoration: InputDecoration(
                hintText: 'Masukkan alasan penolakan...',
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
              ),
            ),
            actions: [
              TextButton(
                onPressed: () => Navigator.of(ctx).pop(null),
                child: const Text('Batal'),
              ),
              FilledButton(
                onPressed: () =>
                    Navigator.of(ctx).pop(catatanController.text),
                style:
                    FilledButton.styleFrom(backgroundColor: Colors.red),
                child: const Text('Tolak'),
              ),
            ],
          );
        },
      );

      if (catatan == null) return; // Dibatalkan
    }

    try {
      final status = keputusan == 'setuju' ? 'disetujui' : 'ditolak';
      await ref
          .read(peminjamanNotifierProvider.notifier)
          .updateStatus(peminjaman.id, status);

      ref.invalidate(peminjamanListProvider);

      if (!context.mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            keputusan == 'setuju'
                ? 'Pengajuan berhasil disetujui'
                : 'Pengajuan berhasil ditolak',
          ),
          backgroundColor:
              keputusan == 'setuju' ? Colors.green : Colors.red,
        ),
      );
    } catch (e) {
      if (!context.mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Gagal: $e'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final colorScheme = Theme.of(context).colorScheme;

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: colorScheme.primaryContainer,
                    shape: BoxShape.circle,
                  ),
                  child: Icon(
                    Icons.person_outline,
                    color: colorScheme.onPrimaryContainer,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        peminjaman.alatNama,
                        style:
                            Theme.of(context).textTheme.titleSmall?.copyWith(
                                  fontWeight: FontWeight.bold,
                                ),
                      ),
                      Text(
                        'ID Peminjaman: #${peminjaman.id}',
                        style: Theme.of(context).textTheme.bodySmall?.copyWith(
                              color: Colors.grey,
                            ),
                      ),
                    ],
                  ),
                ),
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: Colors.orange.withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(
                        color: Colors.orange.withValues(alpha: 0.5)),
                  ),
                  child: Text(
                    'Menunggu',
                    style: Theme.of(context).textTheme.labelSmall?.copyWith(
                          color: Colors.orange,
                          fontWeight: FontWeight.bold,
                        ),
                  ),
                ),
              ],
            ),
            const Divider(height: 20),

             _InfoItem(
              icon: Icons.calendar_today,
              label: 'Tgl Pinjam',
              value: peminjaman.tglPinjam,
            ),
            _InfoItem(
              icon: Icons.event,
              label: 'Rencana Kembali',
              value: peminjaman.tglKembaliRencana,
            ),

            const SizedBox(height: 12),

            // Action Buttons
            Row(
              children: [
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: () =>
                        _handleKeputusan(context, ref, 'tolak'),
                    icon: const Icon(Icons.close, color: Colors.red),
                    label: const Text('Tolak',
                        style: TextStyle(color: Colors.red)),
                    style: OutlinedButton.styleFrom(
                      side: const BorderSide(color: Colors.red),
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: FilledButton.icon(
                    onPressed: () =>
                        _handleKeputusan(context, ref, 'setuju'),
                    icon: const Icon(Icons.check),
                    label: const Text('Setujui'),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

class _InfoItem extends StatelessWidget {
  final IconData icon;
  final String label;
  final String value;

  const _InfoItem({
    required this.icon,
    required this.label,
    required this.value,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 6),
      child: Row(
        children: [
          Icon(icon, size: 16, color: Colors.grey),
          const SizedBox(width: 8),
          Text(
            '$label: ',
            style: Theme.of(context)
                .textTheme
                .bodySmall
                ?.copyWith(color: Colors.grey),
          ),
          Expanded(
            child: Text(
              value,
              style: Theme.of(context)
                  .textTheme
                  .bodySmall
                  ?.copyWith(fontWeight: FontWeight.w500),
            ),
          ),
        ],
      ),
    );
  }
}
