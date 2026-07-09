import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:sipap_flutter/features/peminjaman/models/peminjaman_model.dart';
import 'package:sipap_flutter/features/peminjaman/providers/peminjaman_provider.dart';

class RiwayatPeminjamanScreen extends ConsumerStatefulWidget {
  const RiwayatPeminjamanScreen({super.key});

  @override
  ConsumerState<RiwayatPeminjamanScreen> createState() =>
      _RiwayatPeminjamanScreenState();
}

class _RiwayatPeminjamanScreenState
    extends ConsumerState<RiwayatPeminjamanScreen> {
  String? selectedStatus;

  @override
  Widget build(BuildContext context) {
    final peminjamanListAsync = ref.watch(peminjamanListProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Riwayat Peminjaman'),
        elevation: 0,
      ),
      body: Column(
        children: [
          // Status Filter Chips
          Container(
            padding: const EdgeInsets.all(12),
            child: SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              child: Row(
                children: [
                  _FilterChip(
                    label: 'Semua',
                    isSelected: selectedStatus == null,
                    onTap: () => setState(() => selectedStatus = null),
                  ),
                  const SizedBox(width: 8),
                  _FilterChip(
                    label: 'Menunggu',
                    isSelected: selectedStatus == 'pending',
                    onTap: () => setState(() => selectedStatus = 'pending'),
                  ),
                  const SizedBox(width: 8),
                  _FilterChip(
                    label: 'Disetujui',
                    isSelected: selectedStatus == 'approved',
                    onTap: () => setState(() => selectedStatus = 'approved'),
                  ),
                  const SizedBox(width: 8),
                  _FilterChip(
                    label: 'Ditolak',
                    isSelected: selectedStatus == 'rejected',
                    onTap: () => setState(() => selectedStatus = 'rejected'),
                  ),
                  const SizedBox(width: 8),
                  _FilterChip(
                    label: 'Selesai',
                    isSelected: selectedStatus == 'selesai',
                    onTap: () => setState(() => selectedStatus = 'selesai'),
                  ),
                ],
              ),
            ),
          ),
          const Divider(height: 0),
          // List Content
          Expanded(
            child: peminjamanListAsync.when(
              loading: () => const Center(
                child: CircularProgressIndicator(),
              ),
              error: (error, stackTrace) => Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(Icons.error_outline,
                        size: 48, color: Colors.red),
                    const SizedBox(height: 16),
                    Text('Error: $error'),
                    const SizedBox(height: 16),
                    ElevatedButton(
                      onPressed: () {
                        ref.invalidate(peminjamanListProvider);
                      },
                      child: const Text('Coba Lagi'),
                    ),
                  ],
                ),
              ),
              data: (peminjamanList) {
                // Filter by selected status
                final filtered = selectedStatus == null
                    ? peminjamanList
                    : peminjamanList
                        .where((p) => p.status == selectedStatus)
                        .toList();

                if (filtered.isEmpty) {
                  return Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(
                          Icons.inbox_outlined,
                          size: 64,
                          color: Colors.grey[400],
                        ),
                        const SizedBox(height: 16),
                        Text(
                          'Tidak ada peminjaman',
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
                    itemCount: filtered.length,
                    itemBuilder: (context, index) {
                      final peminjaman = filtered[index];
                      return _PeminjamanCard(
                        peminjaman: peminjaman,
                        onTap: () =>
                            context.push('/peminjaman/${peminjaman.id}'),
                      );
                    },
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}

class _FilterChip extends StatelessWidget {
  final String label;
  final bool isSelected;
  final VoidCallback onTap;

  const _FilterChip({
    required this.label,
    required this.isSelected,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return FilterChip(
      label: Text(label),
      selected: isSelected,
      onSelected: (_) => onTap(),
      selectedColor: colorScheme.primaryContainer,
      labelStyle: TextStyle(
        color: isSelected ? colorScheme.onPrimaryContainer : null,
        fontWeight: isSelected ? FontWeight.w600 : null,
      ),
    );
  }
}

class _PeminjamanCard extends StatelessWidget {
  final PeminjamanModel peminjaman;
  final VoidCallback? onTap;

  const _PeminjamanCard({required this.peminjaman, this.onTap});

  Color _getStatusColor(String status) {
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

  @override
  Widget build(BuildContext context) {
    final statusColor = _getStatusColor(peminjaman.status);

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header with title and status badge
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          peminjaman.alatNama,
                          style:
                              Theme.of(context).textTheme.titleMedium?.copyWith(
                                    fontWeight: FontWeight.bold,
                                  ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                        const SizedBox(height: 4),
                        Text(
                          'ID: ${peminjaman.id}',
                          style:
                              Theme.of(context).textTheme.labelSmall?.copyWith(
                                    color: Colors.grey,
                                  ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 8),
                  // Status Badge
                  Container(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 12, vertical: 6),
                    decoration: BoxDecoration(
                      color: statusColor.withValues(alpha: 0.2),
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(color: statusColor),
                    ),
                    child: Text(
                      peminjaman.statusText,
                      style:
                          Theme.of(context).textTheme.labelSmall?.copyWith(
                                color: statusColor,
                                fontWeight: FontWeight.bold,
                              ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              const Divider(),
              const SizedBox(height: 8),
              // Details
              _DetailRow(
                label: 'Tgl Pinjam',
                value: peminjaman.tglPinjam,
              ),
              const SizedBox(height: 6),
              _DetailRow(
                label: 'Rencana Kembali',
                value: peminjaman.tglKembaliRencana,
              ),
              // Tap hint
              const SizedBox(height: 8),
              Row(
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  Text(
                    'Lihat Detail',
                    style: Theme.of(context).textTheme.labelSmall?.copyWith(
                          color: Theme.of(context).colorScheme.primary,
                        ),
                  ),
                  Icon(
                    Icons.chevron_right,
                    size: 16,
                    color: Theme.of(context).colorScheme.primary,
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _DetailRow extends StatelessWidget {
  final String label;
  final String value;

  const _DetailRow({required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Expanded(
          flex: 2,
          child: Text(
            label,
            style: Theme.of(context).textTheme.labelSmall?.copyWith(
                  color: Colors.grey,
                ),
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          flex: 3,
          child: Text(
            value,
            style: Theme.of(context).textTheme.bodySmall?.copyWith(
                  fontWeight: FontWeight.w500,
                ),
            textAlign: TextAlign.end,
          ),
        ),
      ],
    );
  }
}
