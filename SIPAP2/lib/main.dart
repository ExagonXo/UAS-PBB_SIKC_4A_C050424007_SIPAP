import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/date_symbol_data_local.dart';

import 'app.dart';
import 'core/config/app_config.dart';
import 'core/services/notification_service.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await AppConfig.load();

  // Initialize locale data for Indonesian date formatting (DateFormat 'id')
  await initializeDateFormatting('id', null);

  // Initialize notification settings and Firebase asynchronously in the background
  // to prevent blocking the runApp boot process (especially on emulators without Play Services)
  NotificationService.instance.initialize();
  
  runApp(const ProviderScope(child: SipapApp()));
}