import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import 'package:sipap_flutter/features/alat/models/alat_model.dart';
import 'package:sipap_flutter/features/alat/providers/alat_provider.dart';
import 'package:sipap_flutter/features/auth/models/user_model.dart';
import 'package:sipap_flutter/features/auth/providers/auth_provider.dart';

import '../providers/peminjaman_provider.dart';

enum JenisPeminjaman { satuHari, lama }

class PeminjamanFormScreen extends ConsumerStatefulWidget {
  /// Jika diberikan dari halaman detail alat, alat tersebut langsung dipilih.
  final int? alatId;

  const PeminjamanFormScreen({super.key, this.alatId});

  @override
  ConsumerState<PeminjamanFormScreen> createState() =>
      _PeminjamanFormScreenState();
}

class _PeminjamanFormScreenState extends ConsumerState<PeminjamanFormScreen> {
  // Identity fields
  late TextEditingController _namaController;
  late TextEditingController _nimNipController;
  late TextEditingController _mataKuliahController;
  UserModel? _selectedDosen;

  // Multi-select alat
  final Set<int> _selectedAlatIds = {};
  final Map<int, AlatModel> _selectedAlatMap = {};

  // Jenis peminjaman
  JenisPeminjaman _jenisPeminjaman = JenisPeminjaman.satuHari;

  // Peminjaman 1 hari
  DateTime? _tanggalSatuHari;
  TimeOfDay? _jamMulai;
  TimeOfDay? _jamSelesai;

  // Peminjaman lama
  DateTime? _tanggalMulai;
  DateTime? _tanggalKembali;

  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    final user = ref.read(userProvider);
    _namaController = TextEditingController(text: user?.name ?? '');
    _nimNipController = TextEditingController(text: user?.identifier ?? '');
    _mataKuliahController = TextEditingController();
    _tanggalSatuHari = DateTime.now();

    // Jika navigasi dari halaman detail alat, auto-select alat tersebut
    if (widget.alatId != null) {
      WidgetsBinding.instance.addPostFrameCallback((_) {
        final alatAsync = ref.read(alatListProvider);
        alatAsync.whenData((list) {
          final alat = list.where((a) => a.id == widget.alatId).firstOrNull;
          if (alat != null && mounted) {
            setState(() {
              _selectedAlatIds.add(alat.id);
              _selectedAlatMap[alat.id] = alat;
            });
          }
        });
      });
    }
  }

  @override
  void dispose() {
    _namaController.dispose();
    _nimNipController.dispose();
    _mataKuliahController.dispose();
    super.dispose();
  }

  // ─── Alat Multi-Picker ───────────────────────────────────────────────────────

  void _openAlatPicker(List<AlatModel> alatList) async {
    final tempSelected = Set<int>.from(_selectedAlatIds);

    await showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      useSafeArea: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      builder: (ctx) => _AlatPickerSheet(
        alatList: alatList,
        initialSelected: tempSelected,
        onConfirm: (selected, mapAlat) {
          setState(() {
            _selectedAlatIds
              ..clear()
              ..addAll(selected);
            _selectedAlatMap
              ..clear()
              ..addAll(mapAlat);
          });
        },
      ),
    );
  }

  // ─── Date / Time Pickers ─────────────────────────────────────────────────────

  Future<void> _pickJamMulai() async {
    final picked = await showTimePicker(
      context: context,
      initialTime: _jamMulai ?? const TimeOfDay(hour: 8, minute: 0),
      helpText: 'Pilih Jam Mulai Peminjaman',
    );
    if (picked != null) setState(() => _jamMulai = picked);
  }

  Future<void> _pickJamSelesai() async {
    final picked = await showTimePicker(
      context: context,
      initialTime: _jamSelesai ?? const TimeOfDay(hour: 9, minute: 0),
      helpText: 'Pilih Jam Selesai Peminjaman',
    );
    if (picked != null) setState(() => _jamSelesai = picked);
  }

  Future<void> _pickTanggalMulai() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );
    if (picked != null) {
      setState(() {
        _tanggalMulai = picked;
        if (_tanggalKembali != null && !_tanggalKembali!.isAfter(picked)) {
          _tanggalKembali = null;
        }
      });
    }
  }

  Future<void> _pickTanggalKembali() async {
    final firstAllowed = _tanggalMulai != null
        ? _tanggalMulai!.add(const Duration(days: 1))
        : DateTime.now().add(const Duration(days: 1));

    final picked = await showDatePicker(
      context: context,
      initialDate: firstAllowed,
      firstDate: firstAllowed,
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );
    if (picked != null) setState(() => _tanggalKembali = picked);
  }

  // ─── Helpers ──────────────────────────────────────────────────────────────────

  String _formatDate(DateTime dt) => DateFormat('dd MMMM yyyy', 'id').format(dt);
  String _formatTime(TimeOfDay t) =>
      '${t.hour.toString().padLeft(2, '0')}:${t.minute.toString().padLeft(2, '0')}';
  int _timeToMinutes(TimeOfDay t) => t.hour * 60 + t.minute;

  void _showError(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
      content: Text(msg),
      backgroundColor: Colors.red.shade600,
      behavior: SnackBarBehavior.floating,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
    ));
  }

  void _showSuccess(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
      content: Text(msg),
      backgroundColor: Colors.green.shade600,
      behavior: SnackBarBehavior.floating,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
    ));
  }

  // ─── Validation & Submit ──────────────────────────────────────────────────────

  Future<void> _submitForm() async {
    final user = ref.read(userProvider);
    if (user == null) return;

    final isDosen = user.role.toLowerCase() == 'dosen';

    // Validate: minimal 1 alat dipilih
    if (_selectedAlatIds.isEmpty) {
      _showError('Pilih minimal 1 alat yang ingin dipinjam.');
      return;
    }

    if (_namaController.text.trim().isEmpty ||
        _nimNipController.text.trim().isEmpty) {
      _showError('Nama dan NIM/NIP wajib diisi.');
      return;
    }

    if (!isDosen &&
        (_mataKuliahController.text.trim().isEmpty ||
            _selectedDosen == null)) {
      _showError('Mata kuliah dan Dosen pengampu wajib diisi.');
      return;
    }

    // Build tanggal strings
    String tglPinjam;
    String tglKembaliRencana;

    if (_jenisPeminjaman == JenisPeminjaman.satuHari) {
      if (_tanggalSatuHari == null ||
          _jamMulai == null ||
          _jamSelesai == null) {
        _showError('Pilih tanggal, jam mulai, dan jam selesai peminjaman.');
        return;
      }
      final selisih =
          _timeToMinutes(_jamSelesai!) - _timeToMinutes(_jamMulai!);
      if (selisih < 60) {
        _showError(
            'Jam selesai harus minimal 1 jam setelah jam mulai.\nContoh: mulai 08:00 → selesai minimal 09:00.');
        return;
      }
      final dateStr = DateFormat('yyyy-MM-dd').format(_tanggalSatuHari!);
      tglPinjam = '$dateStr ${_formatTime(_jamMulai!)}:00';
      tglKembaliRencana = '$dateStr ${_formatTime(_jamSelesai!)}:00';
    } else {
      if (_tanggalMulai == null || _tanggalKembali == null) {
        _showError('Pilih tanggal peminjaman dan tanggal rencana kembali.');
        return;
      }
      final mulaiStr = DateFormat('yyyy-MM-dd').format(_tanggalMulai!);
      final kembaliStr = DateFormat('yyyy-MM-dd').format(_tanggalKembali!);
      if (mulaiStr == kembaliStr) {
        _showError(
            'Tanggal kembali harus berbeda dari tanggal mulai.\nUntuk peminjaman sehari, pilih "Peminjaman 1 Hari".');
        return;
      }
      tglPinjam = mulaiStr;
      tglKembaliRencana = kembaliStr;
    }

    setState(() => _isLoading = true);

    try {
      final alatIdsList = _selectedAlatIds.toList();
      await ref
          .read(peminjamanNotifierProvider.notifier)
          .createMultiplePeminjaman(alatIdsList, tglPinjam, tglKembaliRencana);

      if (!mounted) return;

      final jumlah = alatIdsList.length;
      _showSuccess(
          '$jumlah peminjaman berhasil diajukan! 🎉');
      ref.invalidate(peminjamanListProvider);
      context.go('/home');
    } catch (e) {
      if (!mounted) return;
      final msg = e.toString().replaceFirst('Exception: ', '');
      _showError('Gagal: $msg');
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  // ─── Build ────────────────────────────────────────────────────────────────────

  @override
  Widget build(BuildContext context) {
    final user = ref.watch(userProvider);
    final alatListAsync = ref.watch(alatListProvider);
    final dosenListAsync = ref.watch(dosenListProvider);

    if (user == null) {
      return const Scaffold(
        body: Center(child: Text('User tidak terautentikasi')),
      );
    }

    final isDosen = user.role.toLowerCase() == 'dosen';
    final colorScheme = Theme.of(context).colorScheme;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Ajukan Peminjaman'),
        elevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // ── Info Role ──────────────────────────────────────────────────
            _InfoBanner(isDosen: isDosen, colorScheme: colorScheme),
            const SizedBox(height: 24),

            // ══════════════════════════════════════════════════════════════
            // ── PILIH ALAT (multi-select) ─────────────────────────────────
            // ══════════════════════════════════════════════════════════════
            _SectionDivider(label: 'Pilih Alat'),
            const SizedBox(height: 12),

            alatListAsync.when(
              loading: () => const Center(
                child: Padding(
                  padding: EdgeInsets.all(16),
                  child: CircularProgressIndicator(),
                ),
              ),
              error: (e, _) => _ErrorRetryWidget(
                onRetry: () => ref.invalidate(alatListProvider),
              ),
              data: (alatList) {
                return _AlatPickerTrigger(
                  selectedAlat: _selectedAlatMap.values.toList(),
                  onTap: _isLoading
                      ? null
                      : () => _openAlatPicker(alatList),
                  onRemove: (id) => setState(() {
                    _selectedAlatIds.remove(id);
                    _selectedAlatMap.remove(id);
                  }),
                  colorScheme: colorScheme,
                );
              },
            ),

            const SizedBox(height: 20),

            // ── Nama Peminjam ──────────────────────────────────────────────
            _FieldLabel('Nama Peminjam *'),
            const SizedBox(height: 8),
            TextField(
              controller: _namaController,
              decoration: const InputDecoration(
                hintText: 'Masukkan Nama Lengkap',
                prefixIcon: Icon(Icons.badge_outlined),
              ),
              enabled: !_isLoading,
            ),
            const SizedBox(height: 16),

            // ── NIM / NIP ──────────────────────────────────────────────────
            _FieldLabel(isDosen ? 'NIP *' : 'NIM *'),
            const SizedBox(height: 8),
            TextField(
              controller: _nimNipController,
              decoration: InputDecoration(
                hintText: isDosen ? 'Masukkan NIP' : 'Masukkan NIM',
                prefixIcon: const Icon(Icons.credit_card),
              ),
              enabled: !_isLoading,
            ),
            const SizedBox(height: 16),

            // ── Field Khusus Mahasiswa ─────────────────────────────────────
            if (!isDosen) ...[
              _FieldLabel('Mata Kuliah *'),
              const SizedBox(height: 8),
              TextField(
                controller: _mataKuliahController,
                decoration: const InputDecoration(
                  hintText: 'Masukkan Nama Mata Kuliah',
                  prefixIcon: Icon(Icons.book_outlined),
                ),
                enabled: !_isLoading,
              ),
              const SizedBox(height: 16),
              _FieldLabel('Dosen Pengampu *'),
              const SizedBox(height: 8),
              dosenListAsync.when(
                loading: () => const Center(
                  child: Padding(
                    padding: EdgeInsets.all(8),
                    child: CircularProgressIndicator(),
                  ),
                ),
                error: (e, _) => DropdownButtonFormField<UserModel>(
                  decoration: const InputDecoration(
                    prefixIcon: Icon(Icons.person_pin_outlined),
                    hintText: 'Gagal memuat daftar dosen',
                  ),
                  items: const [],
                  onChanged: null,
                ),
                data: (dosenList) => DropdownButtonFormField<UserModel>(
                  decoration: const InputDecoration(
                    prefixIcon: Icon(Icons.person_pin_outlined),
                    hintText: 'Pilih Dosen Pengampu',
                  ),
                  initialValue: _selectedDosen,
                  items: dosenList.map((d) {
                    return DropdownMenuItem(value: d, child: Text(d.name));
                  }).toList(),
                  onChanged: _isLoading
                      ? null
                      : (val) => setState(() => _selectedDosen = val),
                ),
              ),
              const SizedBox(height: 16),
            ],

            // ══════════════════════════════════════════════════════════════
            // ── Jenis Peminjaman ──────────────────────────────────────────
            // ══════════════════════════════════════════════════════════════
            _SectionDivider(label: 'Jenis Peminjaman'),
            const SizedBox(height: 12),

            Row(
              children: [
                Expanded(
                  child: _JenisCard(
                    icon: Icons.access_time_rounded,
                    title: 'Peminjaman\n1 Hari',
                    subtitle: 'Pinjam & kembali\nhari yang sama',
                    selected: _jenisPeminjaman == JenisPeminjaman.satuHari,
                    onTap: () => setState(
                        () => _jenisPeminjaman = JenisPeminjaman.satuHari),
                    colorScheme: colorScheme,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: _JenisCard(
                    icon: Icons.date_range_rounded,
                    title: 'Peminjaman\nLama',
                    subtitle: 'Lebih dari\nsatu hari',
                    selected: _jenisPeminjaman == JenisPeminjaman.lama,
                    onTap: () => setState(
                        () => _jenisPeminjaman = JenisPeminjaman.lama),
                    colorScheme: colorScheme,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 20),

            // Peminjaman 1 Hari
            if (_jenisPeminjaman == JenisPeminjaman.satuHari) ...[
              _FieldLabel('Tanggal Peminjaman'),
              const SizedBox(height: 8),
              _LockedDateField(date: _tanggalSatuHari!),
              const SizedBox(height: 16),
              Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _FieldLabel('Jam Mulai *'),
                        const SizedBox(height: 8),
                        _ReadonlyField(
                          icon: Icons.schedule,
                          text: _jamMulai != null ? _formatTime(_jamMulai!) : null,
                          hint: '08:00',
                          onTap: _isLoading ? null : _pickJamMulai,
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _FieldLabel('Jam Selesai *'),
                        const SizedBox(height: 8),
                        _ReadonlyField(
                          icon: Icons.schedule_outlined,
                          text: _jamSelesai != null ? _formatTime(_jamSelesai!) : null,
                          hint: '10:00',
                          onTap: _isLoading ? null : _pickJamSelesai,
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              if (_jamMulai != null && _jamSelesai != null) ...[
                const SizedBox(height: 8),
                _DurationHint(mulai: _jamMulai!, selesai: _jamSelesai!),
              ],
            ],

            // Peminjaman Lama
            if (_jenisPeminjaman == JenisPeminjaman.lama) ...[
              _FieldLabel('Tanggal Peminjaman *'),
              const SizedBox(height: 8),
              _ReadonlyField(
                icon: Icons.calendar_today,
                text: _tanggalMulai != null ? _formatDate(_tanggalMulai!) : null,
                hint: 'Pilih tanggal mulai',
                onTap: _isLoading ? null : _pickTanggalMulai,
              ),
              const SizedBox(height: 16),
              _FieldLabel('Tanggal Rencana Kembali *'),
              const SizedBox(height: 8),
              _ReadonlyField(
                icon: Icons.event,
                text: _tanggalKembali != null ? _formatDate(_tanggalKembali!) : null,
                hint: _tanggalMulai != null
                    ? 'Minimal ${_formatDate(_tanggalMulai!.add(const Duration(days: 1)))}'
                    : 'Pilih tanggal kembali',
                onTap: _isLoading ? null : _pickTanggalKembali,
              ),
              if (_tanggalMulai != null && _tanggalKembali != null) ...[
                const SizedBox(height: 8),
                _MultiDayHint(mulai: _tanggalMulai!, kembali: _tanggalKembali!),
              ],
            ],

            const SizedBox(height: 32),

            // ── Submit Button ──────────────────────────────────────────────
            SizedBox(
              width: double.infinity,
              child: FilledButton.icon(
                onPressed: _isLoading ? null : _submitForm,
                icon: _isLoading
                    ? const SizedBox(
                        height: 18,
                        width: 18,
                        child: CircularProgressIndicator(
                            strokeWidth: 2, color: Colors.white),
                      )
                    : const Icon(Icons.send_rounded),
                label: Text(_isLoading ? 'Mengajukan...' : 'Kirim Pengajuan'),
                style: FilledButton.styleFrom(
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  textStyle: const TextStyle(
                      fontSize: 16, fontWeight: FontWeight.bold),
                ),
              ),
            ),
            const SizedBox(height: 24),
          ],
        ),
      ),
    );
  }
}

// ─── Alat Picker Trigger Widget ───────────────────────────────────────────────

class _AlatPickerTrigger extends StatelessWidget {
  final List<AlatModel> selectedAlat;
  final VoidCallback? onTap;
  final void Function(int id) onRemove;
  final ColorScheme colorScheme;

  const _AlatPickerTrigger({
    required this.selectedAlat,
    required this.onTap,
    required this.onRemove,
    required this.colorScheme,
  });

  @override
  Widget build(BuildContext context) {
    final hasSelected = selectedAlat.isNotEmpty;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // Trigger Button
        InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(14),
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 200),
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
            decoration: BoxDecoration(
              border: Border.all(
                color: hasSelected ? colorScheme.primary : colorScheme.outline,
                width: hasSelected ? 1.8 : 1,
              ),
              borderRadius: BorderRadius.circular(14),
              color: hasSelected
                  ? colorScheme.primaryContainer.withValues(alpha: 0.18)
                  : Colors.transparent,
            ),
            child: Row(
              children: [
                Icon(
                  Icons.inventory_2_outlined,
                  size: 20,
                  color: hasSelected
                      ? colorScheme.primary
                      : colorScheme.onSurfaceVariant,
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Text(
                    hasSelected
                        ? '${selectedAlat.length} alat dipilih — ketuk untuk ubah'
                        : 'Ketuk untuk memilih alat',
                    style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                          color: hasSelected
                              ? colorScheme.onSurface
                              : colorScheme.onSurfaceVariant,
                          fontWeight: hasSelected
                              ? FontWeight.w600
                              : FontWeight.normal,
                        ),
                  ),
                ),
                Icon(
                  Icons.arrow_drop_down_rounded,
                  color: hasSelected
                      ? colorScheme.primary
                      : colorScheme.onSurfaceVariant,
                ),
              ],
            ),
          ),
        ),

        // Chip list selected alat
        if (hasSelected) ...[
          const SizedBox(height: 10),
          Wrap(
            spacing: 8,
            runSpacing: 6,
            children: selectedAlat.map((alat) {
              return Chip(
                avatar: Icon(Icons.videocam_outlined,
                    size: 16, color: colorScheme.primary),
                label: Text(
                  alat.namaAlat,
                  style: Theme.of(context).textTheme.labelMedium?.copyWith(
                        color: colorScheme.onPrimaryContainer,
                        fontWeight: FontWeight.w600,
                      ),
                ),
                deleteIcon: Icon(Icons.close_rounded,
                    size: 16, color: colorScheme.onPrimaryContainer),
                onDeleted: () => onRemove(alat.id),
                backgroundColor: colorScheme.primaryContainer,
                side: BorderSide.none,
                padding:
                    const EdgeInsets.symmetric(horizontal: 4, vertical: 2),
              );
            }).toList(),
          ),
        ],

        if (!hasSelected) ...[
          const SizedBox(height: 6),
          Text(
            'Kamu bisa memilih lebih dari 1 alat sekaligus',
            style: Theme.of(context).textTheme.bodySmall?.copyWith(
                  color: colorScheme.onSurfaceVariant,
                ),
          ),
        ],
      ],
    );
  }
}

// ─── Bottom Sheet: Alat Picker ────────────────────────────────────────────────

class _AlatPickerSheet extends StatefulWidget {
  final List<AlatModel> alatList;
  final Set<int> initialSelected;
  final void Function(Set<int> selected, Map<int, AlatModel> mapAlat) onConfirm;

  const _AlatPickerSheet({
    required this.alatList,
    required this.initialSelected,
    required this.onConfirm,
  });

  @override
  State<_AlatPickerSheet> createState() => _AlatPickerSheetState();
}

class _AlatPickerSheetState extends State<_AlatPickerSheet> {
  late Set<int> _selected;
  late Map<int, AlatModel> _selectedMap;
  String _search = '';

  @override
  void initState() {
    super.initState();
    _selected = Set.from(widget.initialSelected);
    _selectedMap = {
      for (final a in widget.alatList)
        if (widget.initialSelected.contains(a.id)) a.id: a,
    };
  }

  List<AlatModel> get _filtered {
    if (_search.isEmpty) return widget.alatList;
    final q = _search.toLowerCase();
    return widget.alatList
        .where((a) => a.namaAlat.toLowerCase().contains(q))
        .toList();
  }

  void _toggle(AlatModel alat) {
    setState(() {
      if (_selected.contains(alat.id)) {
        _selected.remove(alat.id);
        _selectedMap.remove(alat.id);
      } else {
        _selected.add(alat.id);
        _selectedMap[alat.id] = alat;
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final filtered = _filtered;

    return DraggableScrollableSheet(
      initialChildSize: 0.85,
      minChildSize: 0.5,
      maxChildSize: 0.95,
      expand: false,
      builder: (_, scrollCtrl) {
        return Column(
          children: [
            // Handle bar
            Container(
              margin: const EdgeInsets.only(top: 12, bottom: 6),
              width: 40,
              height: 4,
              decoration: BoxDecoration(
                color: colorScheme.outlineVariant,
                borderRadius: BorderRadius.circular(2),
              ),
            ),

            // Header
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
              child: Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Pilih Alat',
                          style: Theme.of(context)
                              .textTheme
                              .titleLarge
                              ?.copyWith(fontWeight: FontWeight.bold),
                        ),
                        Text(
                          '${_selected.length} alat dipilih',
                          style:
                              Theme.of(context).textTheme.bodySmall?.copyWith(
                                    color: colorScheme.primary,
                                    fontWeight: FontWeight.w600,
                                  ),
                        ),
                      ],
                    ),
                  ),
                  FilledButton(
                    onPressed: () {
                      widget.onConfirm(_selected, _selectedMap);
                      Navigator.pop(context);
                    },
                    style: FilledButton.styleFrom(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 20, vertical: 10),
                    ),
                    child: Text(
                      _selected.isEmpty ? 'Batal' : 'Pilih (${_selected.length})',
                    ),
                  ),
                ],
              ),
            ),

            // Search bar
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
              child: TextField(
                onChanged: (v) => setState(() => _search = v),
                decoration: InputDecoration(
                  hintText: 'Cari nama alat...',
                  prefixIcon: const Icon(Icons.search),
                  filled: true,
                  fillColor: colorScheme.surfaceContainerHighest,
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(12),
                    borderSide: BorderSide.none,
                  ),
                  contentPadding:
                      const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                ),
              ),
            ),

            const Divider(height: 1),

            // List Alat
            Expanded(
              child: filtered.isEmpty
                  ? Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(Icons.inventory_2_outlined,
                              size: 48, color: colorScheme.outlineVariant),
                          const SizedBox(height: 12),
                          Text(
                            widget.alatList.isEmpty
                                ? 'Tidak ada alat tersedia'
                                : 'Alat tidak ditemukan',
                            style: Theme.of(context)
                                .textTheme
                                .bodyMedium
                                ?.copyWith(
                                    color: colorScheme.onSurfaceVariant),
                          ),
                        ],
                      ),
                    )
                  : ListView.builder(
                      controller: scrollCtrl,
                      itemCount: filtered.length,
                      padding: const EdgeInsets.symmetric(vertical: 8),
                      itemBuilder: (ctx, i) {
                        final alat = filtered[i];
                        final isSelected = _selected.contains(alat.id);
                        return _AlatPickerItem(
                          alat: alat,
                          isSelected: isSelected,
                          onTap: () => _toggle(alat),
                          colorScheme: colorScheme,
                        );
                      },
                    ),
            ),
          ],
        );
      },
    );
  }
}

class _AlatPickerItem extends StatelessWidget {
  final AlatModel alat;
  final bool isSelected;
  final VoidCallback onTap;
  final ColorScheme colorScheme;

  const _AlatPickerItem({
    required this.alat,
    required this.isSelected,
    required this.onTap,
    required this.colorScheme,
  });

  @override
  Widget build(BuildContext context) {
    final available = alat.isAvailable;

    // Warna badge status
    MaterialColor statusMaterial;
    String statusLabel;
    switch (alat.status) {
      case 'tersedia':
        statusMaterial = Colors.green;
        statusLabel = 'Tersedia';
        break;
      case 'dipinjam':
        statusMaterial = Colors.orange;
        statusLabel = 'Sedang Dipinjam';
        break;
      default:
        statusMaterial = Colors.red;
        statusLabel = 'Rusak';
    }
    if (alat.stok <= 0) {
      statusMaterial = Colors.red;
      statusLabel = 'Stok Habis';
    }

    return InkWell(
      onTap: available ? onTap : null,
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 150),
        margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: !available
              ? colorScheme.surfaceContainerHighest.withValues(alpha: 0.4)
              : isSelected
                  ? colorScheme.primaryContainer.withValues(alpha: 0.5)
                  : Colors.transparent,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: isSelected ? colorScheme.primary : Colors.transparent,
            width: 1.5,
          ),
        ),
        child: Opacity(
          opacity: available ? 1.0 : 0.5,
          child: Row(
            children: [
              // Thumbnail / placeholder
              ClipRRect(
                borderRadius: BorderRadius.circular(8),
                child: alat.gambar != null && alat.gambar!.isNotEmpty
                    ? Image.network(
                        alat.gambar!,
                        width: 52,
                        height: 52,
                        fit: BoxFit.cover,
                        errorBuilder: (context, error, stack) =>
                            _PlaceholderImage(colorScheme: colorScheme),
                      )
                    : _PlaceholderImage(colorScheme: colorScheme),
              ),
              const SizedBox(width: 12),

              // Info
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      alat.namaAlat,
                      style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                            fontWeight: FontWeight.w600,
                            color: isSelected
                                ? colorScheme.primary
                                : colorScheme.onSurface,
                          ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 6, vertical: 2),
                          decoration: BoxDecoration(
                            color: Colors.blue.withValues(alpha: 0.1),
                            borderRadius: BorderRadius.circular(4),
                          ),
                          child: Text(
                            'Stok: ${alat.stok}',
                            style: Theme.of(context)
                                .textTheme
                                .labelSmall
                                ?.copyWith(
                                  color: Colors.blue.shade700,
                                  fontWeight: FontWeight.w600,
                                ),
                          ),
                        ),
                        const SizedBox(width: 6),
                        Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 6, vertical: 2),
                          decoration: BoxDecoration(
                            color: statusMaterial.withValues(alpha: 0.12),
                            borderRadius: BorderRadius.circular(4),
                          ),
                          child: Text(
                            statusLabel,
                            style: Theme.of(context)
                                .textTheme
                                .labelSmall
                                ?.copyWith(
                                  color: statusMaterial.shade700,
                                  fontWeight: FontWeight.w600,
                                ),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              // Checkbox / lock icon
              available
                  ? AnimatedSwitcher(
                      duration: const Duration(milliseconds: 200),
                      child: isSelected
                          ? Icon(Icons.check_circle_rounded,
                              key: const ValueKey('checked'),
                              color: colorScheme.primary,
                              size: 26)
                          : Icon(Icons.circle_outlined,
                              key: const ValueKey('unchecked'),
                              color: colorScheme.outlineVariant,
                              size: 26),
                    )
                  : Icon(Icons.lock_outline_rounded,
                      color: colorScheme.outlineVariant,
                      size: 22),
            ],
          ),
        ),
      ),
    );
  }
}

class _PlaceholderImage extends StatelessWidget {
  final ColorScheme colorScheme;
  const _PlaceholderImage({required this.colorScheme});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: 52,
      height: 52,
      decoration: BoxDecoration(
        color: colorScheme.surfaceContainerHighest,
        borderRadius: BorderRadius.circular(8),
      ),
      child: Icon(Icons.videocam_outlined,
          size: 24, color: colorScheme.onSurfaceVariant),
    );
  }
}

// ─── Helper Widgets ────────────────────────────────────────────────────────────

class _FieldLabel extends StatelessWidget {
  final String text;
  const _FieldLabel(this.text);

  @override
  Widget build(BuildContext context) {
    return Text(
      text,
      style: Theme.of(context)
          .textTheme
          .labelLarge
          ?.copyWith(fontWeight: FontWeight.w600),
    );
  }
}

class _LockedDateField extends StatelessWidget {
  final DateTime date;
  const _LockedDateField({required this.date});

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final dateStr = DateFormat('dd MMMM yyyy', 'id').format(date);
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 14),
      decoration: BoxDecoration(
        color: colorScheme.secondaryContainer.withValues(alpha: 0.4),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: colorScheme.secondary.withValues(alpha: 0.5),
          width: 1.5,
        ),
      ),
      child: Row(
        children: [
          Icon(Icons.today_rounded, size: 20, color: colorScheme.secondary),
          const SizedBox(width: 12),
          Expanded(
            child: Text(
              dateStr,
              style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                    color: colorScheme.onSurface,
                    fontWeight: FontWeight.w600,
                  ),
            ),
          ),
          Icon(Icons.lock_outline_rounded,
              size: 16, color: colorScheme.secondary),
          const SizedBox(width: 4),
          Text(
            'Hari ini',
            style: Theme.of(context)
                .textTheme
                .labelSmall
                ?.copyWith(color: colorScheme.secondary),
          ),
        ],
      ),
    );
  }
}

class _SectionDivider extends StatelessWidget {
  final String label;
  const _SectionDivider({required this.label});

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    return Row(
      children: [
        Expanded(child: Divider(color: colorScheme.outlineVariant)),
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 12),
          child: Text(
            label,
            style: Theme.of(context).textTheme.labelMedium?.copyWith(
                  color: colorScheme.primary,
                  fontWeight: FontWeight.bold,
                ),
          ),
        ),
        Expanded(child: Divider(color: colorScheme.outlineVariant)),
      ],
    );
  }
}

class _ReadonlyField extends StatelessWidget {
  final IconData icon;
  final String? text;
  final String hint;
  final VoidCallback? onTap;

  const _ReadonlyField({
    required this.icon,
    required this.text,
    required this.hint,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    final hasValue = text != null && text!.isNotEmpty;
    final colorScheme = Theme.of(context).colorScheme;
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(12),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 14),
        decoration: BoxDecoration(
          border: Border.all(
            color: hasValue ? colorScheme.primary : colorScheme.outline,
            width: hasValue ? 1.5 : 1,
          ),
          borderRadius: BorderRadius.circular(12),
          color: hasValue
              ? colorScheme.primaryContainer.withValues(alpha: 0.2)
              : Colors.transparent,
        ),
        child: Row(
          children: [
            Icon(icon,
                size: 20,
                color: hasValue
                    ? colorScheme.primary
                    : colorScheme.onSurfaceVariant),
            const SizedBox(width: 12),
            Text(
              hasValue ? text! : hint,
              style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                    color: hasValue
                        ? colorScheme.onSurface
                        : colorScheme.onSurfaceVariant,
                    fontWeight:
                        hasValue ? FontWeight.w600 : FontWeight.normal,
                  ),
            ),
          ],
        ),
      ),
    );
  }
}

class _JenisCard extends StatelessWidget {
  final IconData icon;
  final String title;
  final String subtitle;
  final bool selected;
  final VoidCallback onTap;
  final ColorScheme colorScheme;

  const _JenisCard({
    required this.icon,
    required this.title,
    required this.subtitle,
    required this.selected,
    required this.onTap,
    required this.colorScheme,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: selected
              ? colorScheme.primaryContainer
              : colorScheme.surfaceContainerHighest,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(
            color: selected ? colorScheme.primary : Colors.transparent,
            width: 2,
          ),
          boxShadow: selected
              ? [
                  BoxShadow(
                    color: colorScheme.primary.withValues(alpha: 0.15),
                    blurRadius: 8,
                    offset: const Offset(0, 4),
                  )
                ]
              : [],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Icon(icon,
                    color: selected
                        ? colorScheme.primary
                        : colorScheme.onSurfaceVariant,
                    size: 26),
                if (selected)
                  Icon(Icons.check_circle_rounded,
                      color: colorScheme.primary, size: 20),
              ],
            ),
            const SizedBox(height: 8),
            Text(
              title,
              style: Theme.of(context).textTheme.labelLarge?.copyWith(
                    fontWeight: FontWeight.bold,
                    color: selected
                        ? colorScheme.onPrimaryContainer
                        : colorScheme.onSurface,
                  ),
            ),
            const SizedBox(height: 4),
            Text(
              subtitle,
              style: Theme.of(context).textTheme.bodySmall?.copyWith(
                    color: selected
                        ? colorScheme.onPrimaryContainer.withValues(alpha: 0.75)
                        : colorScheme.onSurfaceVariant,
                  ),
            ),
          ],
        ),
      ),
    );
  }
}

class _DurationHint extends StatelessWidget {
  final TimeOfDay mulai;
  final TimeOfDay selesai;
  const _DurationHint({required this.mulai, required this.selesai});

  @override
  Widget build(BuildContext context) {
    final mulaiMenit = mulai.hour * 60 + mulai.minute;
    final selesaiMenit = selesai.hour * 60 + selesai.minute;
    final selisihMenit = selesaiMenit - mulaiMenit;
    final valid = selisihMenit >= 60;
    final durasi = selisihMenit > 0
        ? '${(selisihMenit ~/ 60)}j ${(selisihMenit % 60)}m'
        : '—';

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
      decoration: BoxDecoration(
        color: (valid ? Colors.green : Colors.red).withValues(alpha: 0.1),
        borderRadius: BorderRadius.circular(8),
        border: Border.all(
          color: (valid ? Colors.green : Colors.red).withValues(alpha: 0.3),
        ),
      ),
      child: Row(
        children: [
          Icon(
            valid ? Icons.check_circle_outline : Icons.warning_amber_rounded,
            size: 16,
            color: valid ? Colors.green.shade700 : Colors.red.shade700,
          ),
          const SizedBox(width: 8),
          Text(
            valid
                ? 'Durasi: $durasi ✓'
                : selisihMenit <= 0
                    ? 'Jam selesai harus setelah jam mulai'
                    : 'Minimal 1 jam (sekarang $durasi)',
            style: Theme.of(context).textTheme.bodySmall?.copyWith(
                  color: valid ? Colors.green.shade700 : Colors.red.shade700,
                  fontWeight: FontWeight.w600,
                ),
          ),
        ],
      ),
    );
  }
}

class _MultiDayHint extends StatelessWidget {
  final DateTime mulai;
  final DateTime kembali;
  const _MultiDayHint({required this.mulai, required this.kembali});

  @override
  Widget build(BuildContext context) {
    final diff = kembali.difference(mulai).inDays;
    final valid = diff > 0;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
      decoration: BoxDecoration(
        color: (valid ? Colors.blue : Colors.red).withValues(alpha: 0.1),
        borderRadius: BorderRadius.circular(8),
        border: Border.all(
          color: (valid ? Colors.blue : Colors.red).withValues(alpha: 0.3),
        ),
      ),
      child: Row(
        children: [
          Icon(
            valid ? Icons.info_outline : Icons.warning_amber_rounded,
            size: 16,
            color: valid ? Colors.blue.shade700 : Colors.red.shade700,
          ),
          const SizedBox(width: 8),
          Text(
            valid ? 'Total durasi: $diff hari' : 'Tanggal kembali tidak valid',
            style: Theme.of(context).textTheme.bodySmall?.copyWith(
                  color: valid ? Colors.blue.shade700 : Colors.red.shade700,
                  fontWeight: FontWeight.w600,
                ),
          ),
        ],
      ),
    );
  }
}

class _InfoBanner extends StatelessWidget {
  final bool isDosen;
  final ColorScheme colorScheme;
  const _InfoBanner({required this.isDosen, required this.colorScheme});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            colorScheme.primaryContainer,
            colorScheme.secondaryContainer,
          ],
        ),
        borderRadius: BorderRadius.circular(14),
      ),
      child: Row(
        children: [
          Icon(
            isDosen ? Icons.school_rounded : Icons.person_rounded,
            size: 28,
            color: colorScheme.primary,
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  isDosen ? 'Form Peminjaman Dosen' : 'Form Peminjaman Mahasiswa',
                  style: Theme.of(context).textTheme.labelLarge?.copyWith(
                        fontWeight: FontWeight.bold,
                        color: colorScheme.onPrimaryContainer,
                      ),
                ),
                Text(
                  isDosen
                      ? 'Sebagai dosen, tidak perlu mengisi mata kuliah.'
                      : 'Isi mata kuliah dan dosen pengampu.',
                  style: Theme.of(context).textTheme.bodySmall?.copyWith(
                        color: colorScheme.onSecondaryContainer,
                      ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _ErrorRetryWidget extends StatelessWidget {
  final VoidCallback onRetry;
  const _ErrorRetryWidget({required this.onRetry});

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Icon(Icons.error_outline, color: Colors.red, size: 32),
          const SizedBox(height: 8),
          const Text('Gagal memuat daftar alat'),
          TextButton(onPressed: onRetry, child: const Text('Coba Lagi')),
        ],
      ),
    );
  }
}
