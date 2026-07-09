import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:sipap_flutter/features/peminjaman/models/peminjaman_model.dart';
import 'package:sipap_flutter/features/peminjaman/providers/peminjaman_provider.dart';

class DetailPeminjamanScreen extends ConsumerWidget {
  final int peminjamanId;

  const DetailPeminjamanScreen({super.key, required this.peminjamanId});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final peminjamanListAsync = ref.watch(peminjamanListProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Detail Peminjaman'),
        elevation: 0,
      ),
      body: peminjamanListAsync.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (error, _) => Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.error_outline, size: 64, color: Colors.red),
              const SizedBox(height: 16),
              Text('Error: $error'),
              const SizedBox(height: 16),
              FilledButton(
                onPressed: () => ref.invalidate(peminjamanListProvider),
                child: const Text('Coba Lagi'),
              ),
            ],
          ),
        ),
        data: (list) {
          final peminjaman = list.cast<PeminjamanModel?>().firstWhere(
                (p) => p?.id == peminjamanId,
                orElse: () => null,
              );

          if (peminjaman == null) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.search_off, size: 64, color: Colors.grey[400]),
                  const SizedBox(height: 16),
                  const Text('Data peminjaman tidak ditemukan'),
                  const SizedBox(height: 16),
                  FilledButton(
                    onPressed: () => context.pop(),
                    child: const Text('Kembali'),
                  ),
                ],
              ),
            );
          }

          return _DetailPeminjamanBody(peminjaman: peminjaman);
        },
      ),
    );
  }
}

class _DetailPeminjamanBody extends ConsumerWidget {
  final PeminjamanModel peminjaman;

  const _DetailPeminjamanBody({required this.peminjaman});

  Color _statusColor(String status) {
    switch (status) {
      case 'pending':
      case 'menunggu_kembali':
        return Colors.orange;
      case 'approved':
      case 'disetujui':
        return Colors.green;
      case 'rejected':
      case 'ditolak':
        return Colors.red;
      case 'returned':
      case 'selesai':
        return Colors.blue;
      default:
        return Colors.grey;
    }
  }

  IconData _statusIcon(String status) {
    switch (status) {
      case 'pending':
      case 'menunggu_kembali':
        return Icons.hourglass_empty;
      case 'approved':
      case 'disetujui':
        return Icons.check_circle;
      case 'rejected':
      case 'ditolak':
        return Icons.cancel;
      case 'returned':
      case 'selesai':
        return Icons.inventory_2;
      default:
        return Icons.info;
    }
  }

  bool get _canCancel {
    return peminjaman.status == 'pending';
  }

  bool get _canReturn {
    return peminjaman.status == 'approved' || peminjaman.status == 'disetujui';
  }

  Future<void> _handleCancel(BuildContext context, WidgetRef ref) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Batalkan Peminjaman'),
        content: const Text(
          'Apakah Anda yakin ingin membatalkan peminjaman ini?',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(false),
            child: const Text('Tidak'),
          ),
          FilledButton(
            onPressed: () => Navigator.of(ctx).pop(true),
            style: FilledButton.styleFrom(backgroundColor: Colors.red),
            child: const Text('Ya, Batalkan'),
          ),
        ],
      ),
    );

    if (confirmed != true || !context.mounted) return;

    try {
      await ref
          .read(peminjamanNotifierProvider.notifier)
          .updateStatus(peminjaman.id, 'ditolak');

      if (!context.mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Peminjaman berhasil dibatalkan'),
          backgroundColor: Colors.green,
        ),
      );
      context.go('/peminjaman');
    } catch (e) {
      if (!context.mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Gagal membatalkan: $e'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final statusColor = _statusColor(peminjaman.status);
    final colorScheme = Theme.of(context).colorScheme;

    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Status Header Card
          Container(
            width: double.infinity,
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [
                  statusColor.withValues(alpha: 0.15),
                  statusColor.withValues(alpha: 0.05),
                ],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: statusColor.withValues(alpha: 0.4)),
            ),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: statusColor.withValues(alpha: 0.2),
                    shape: BoxShape.circle,
                  ),
                  child: Icon(_statusIcon(peminjaman.status),
                      color: statusColor, size: 32),
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        peminjaman.statusText,
                        style:
                            Theme.of(context).textTheme.titleMedium?.copyWith(
                                  fontWeight: FontWeight.bold,
                                  color: statusColor,
                                ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        'ID Peminjaman: #${peminjaman.id}',
                        style: Theme.of(context).textTheme.bodySmall?.copyWith(
                              color: Colors.grey[600],
                            ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 20),

          // Detail Alat Card
          Card(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Icon(Icons.inventory_2_outlined,
                          color: colorScheme.primary, size: 20),
                      const SizedBox(width: 8),
                      Text(
                        'Informasi Alat',
                        style:
                            Theme.of(context).textTheme.titleSmall?.copyWith(
                                  fontWeight: FontWeight.bold,
                                ),
                      ),
                    ],
                  ),
                  const Divider(height: 20),
                  _InfoRow(label: 'Nama Alat', value: peminjaman.alatNama),
                  const SizedBox(height: 8),
                  _InfoRow(
                    label: 'Tanggal Pinjam',
                    value: peminjaman.tglPinjam,
                  ),
                  const SizedBox(height: 8),
                  _InfoRow(
                    label: 'Rencana Kembali',
                    value: peminjaman.tglKembaliRencana,
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 12),

          // Timeline Status
          Text(
            'Timeline Status',
            style: Theme.of(context)
                .textTheme
                .titleSmall
                ?.copyWith(fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 12),
          _StatusTimeline(currentStatus: peminjaman.status),

          const SizedBox(height: 32),

          // Action Buttons
          if (_canCancel)
            SizedBox(
              width: double.infinity,
              child: OutlinedButton.icon(
                onPressed: () => _handleCancel(context, ref),
                icon: const Icon(Icons.cancel_outlined, color: Colors.red),
                label: const Text('Batalkan Peminjaman',
                    style: TextStyle(color: Colors.red)),
                style: OutlinedButton.styleFrom(
                  side: const BorderSide(color: Colors.red),
                ),
              ),
            ),

          if (_canReturn) ...[
            const SizedBox(height: 12),
            SizedBox(
              width: double.infinity,
              child: FilledButton.icon(
                onPressed: () {
                  context.push(
                    '/pengembalian/form?peminjamanId=${peminjaman.id}',
                  );
                },
                icon: const Icon(Icons.assignment_return_outlined),
                label: const Text('Ajukan Pengembalian'),
              ),
            ),
          ],
        ],
      ),
    );
  }
}

// Widget Timeline Status
class _StatusTimeline extends StatelessWidget {
  final String currentStatus;

  const _StatusTimeline({required this.currentStatus});

  @override
  Widget build(BuildContext context) {
    final steps = [
      _TimelineStep(
        title: 'Pengajuan Dikirim',
        subtitle: 'Peminjaman berhasil diajukan',
        icon: Icons.send,
        statuses: ['pending', 'approved', 'disetujui', 'rejected', 'ditolak', 'returned', 'selesai'],
      ),
      _TimelineStep(
        title: 'Menunggu Persetujuan',
        subtitle: 'Admin sedang memproses pengajuan',
        icon: Icons.hourglass_top,
        statuses: ['pending'],
      ),
      _TimelineStep(
        title: 'Disetujui',
        subtitle: 'Alat siap diambil di ruang peralatan',
        icon: Icons.check_circle,
        statuses: ['approved', 'disetujui', 'returned', 'selesai'],
      ),
      _TimelineStep(
        title: 'Alat Dikembalikan',
        subtitle: 'Peminjaman selesai',
        icon: Icons.inventory_2,
        statuses: ['returned', 'selesai'],
      ),
    ];

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: steps.asMap().entries.map((entry) {
            final i = entry.key;
            final step = entry.value;
            final isActive = step.statuses.contains(currentStatus);
            final isLast = i == steps.length - 1;

            return Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Column(
                  children: [
                    Container(
                      width: 36,
                      height: 36,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: isActive
                            ? Theme.of(context).colorScheme.primary
                            : Colors.grey[300],
                      ),
                      child: Icon(
                        step.icon,
                        size: 18,
                        color: isActive ? Colors.white : Colors.grey,
                      ),
                    ),
                    if (!isLast)
                      Container(
                        width: 2,
                        height: 40,
                        color: isActive
                            ? Theme.of(context).colorScheme.primary.withValues(alpha: 0.3)
                            : Colors.grey[300],
                      ),
                  ],
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Padding(
                    padding: const EdgeInsets.only(top: 6, bottom: 28),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          step.title,
                          style: Theme.of(context)
                              .textTheme
                              .bodyMedium
                              ?.copyWith(
                                fontWeight: isActive
                                    ? FontWeight.bold
                                    : FontWeight.normal,
                                color: isActive ? null : Colors.grey,
                              ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          step.subtitle,
                          style: Theme.of(context)
                              .textTheme
                              .bodySmall
                              ?.copyWith(color: Colors.grey),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            );
          }).toList(),
        ),
      ),
    );
  }
}

class _TimelineStep {
  final String title;
  final String subtitle;
  final IconData icon;
  final List<String> statuses;

  _TimelineStep({
    required this.title,
    required this.subtitle,
    required this.icon,
    required this.statuses,
  });
}

class _InfoRow extends StatelessWidget {
  final String label;
  final String value;

  const _InfoRow({required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Expanded(
          flex: 2,
          child: Text(
            label,
            style: Theme.of(context).textTheme.bodySmall?.copyWith(
                  color: Colors.grey[600],
                ),
          ),
        ),
        Expanded(
          flex: 3,
          child: Text(
            value,
            style: Theme.of(context).textTheme.bodySmall?.copyWith(
                  fontWeight: FontWeight.w600,
                ),
            textAlign: TextAlign.end,
          ),
        ),
      ],
    );
  }
}
