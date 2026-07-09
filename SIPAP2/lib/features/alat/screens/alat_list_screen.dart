import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../models/alat_model.dart';
import '../providers/alat_provider.dart';

class AlatListScreen extends ConsumerWidget {
  const AlatListScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final alatListAsync = ref.watch(alatListProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Daftar Alat'),
        elevation: 0,
      ),
      body: alatListAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(),
        ),
        error: (error, stackTrace) => Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.error_outline, size: 48, color: Colors.red),
              const SizedBox(height: 16),
              Text('Error: $error'),
              const SizedBox(height: 16),
              ElevatedButton(
                onPressed: () {
                  ref.invalidate(alatListProvider);
                },
                child: const Text('Coba Lagi'),
              ),
            ],
          ),
        ),
        data: (alatList) {
          if (alatList.isEmpty) {
            return const Center(
              child: Text('Tidak ada alat tersedia'),
            );
          }

          return RefreshIndicator(
            onRefresh: () async {
              ref.invalidate(alatListProvider);
              await ref.read(alatListProvider.future);
            },
            child: ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: alatList.length,
              itemBuilder: (context, index) {
                final alat = alatList[index];
                return _AlatCard(alat: alat);
              },
            ),
          );
        },
      ),
    );
  }
}

class _AlatCard extends StatelessWidget {
  final AlatModel alat;

  const _AlatCard({required this.alat});

  @override
  Widget build(BuildContext context) {
    final hasImage = alat.gambar != null && alat.gambar!.isNotEmpty;

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: InkWell(
        onTap: () {
          context.push('/alat/${alat.id}');
        },
        borderRadius: BorderRadius.circular(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Alat Image
            if (hasImage)
              Container(
                width: double.infinity,
                height: 150,
                decoration: BoxDecoration(
                  borderRadius: const BorderRadius.only(
                    topLeft: Radius.circular(16),
                    topRight: Radius.circular(16),
                  ),
                  color: Colors.grey[200],
                ),
                child: Image.network(
                  alat.gambar!,
                  fit: BoxFit.cover,
                  errorBuilder: (context, error, stackTrace) {
                    return Container(
                      color: Colors.grey[300],
                      child: const Icon(Icons.image_not_supported),
                    );
                  },
                ),
              ),
            // Content
            Padding(
              padding: const EdgeInsets.all(12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    alat.namaAlat,
                    style: Theme.of(context).textTheme.titleMedium?.copyWith(
                          fontWeight: FontWeight.bold,
                        ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                  if (alat.deskripsi != null && alat.deskripsi!.isNotEmpty) ...[
                    const SizedBox(height: 8),
                    Text(
                      alat.deskripsi!,
                      style: Theme.of(context).textTheme.bodySmall,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ],
                  const SizedBox(height: 12),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      // Availability Info
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'Stok: ${alat.stok}',
                            style: Theme.of(context).textTheme.labelSmall,
                          ),
                          const SizedBox(height: 4),
                          Text(
                            'Status: ${alat.status}',
                            style: Theme.of(context).textTheme.labelSmall,
                          ),
                        ],
                      ),
                      // Status Badge
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 8,
                          vertical: 4,
                        ),
                        decoration: BoxDecoration(
                          color: alat.isAvailable
                              ? Colors.green.withValues(alpha: 0.2)
                              : Colors.red.withValues(alpha: 0.2),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Text(
                          alat.isAvailable ? 'Tersedia' : 'Habis / Dipinjam',
                          style: Theme.of(context).textTheme.labelSmall?.copyWith(
                                color: alat.isAvailable ? Colors.green : Colors.red,
                                fontWeight: FontWeight.bold,
                              ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
