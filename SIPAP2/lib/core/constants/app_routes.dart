class AppRoutes {
  AppRoutes._();

  static const splash = '/';
  static const login = '/login';
  static const home = '/home';

  // Alat routes
  static const alatList = '/alat';
  static const alatDetail = '/alat/:id';

  // Peminjaman routes
  static const peminjamanForm = '/peminjaman/form';
  static const peminjamanList = '/peminjaman';
  static const peminjamanDetail = '/peminjaman/:id';

  // Pengembalian routes
  static const pengembalianForm = '/pengembalian/form';

  // Dosen routes
  static const dosenKonfirmasi = '/dosen/konfirmasi';

  // Notifikasi routes
  static const notifikasiList = '/notifikasi';
}