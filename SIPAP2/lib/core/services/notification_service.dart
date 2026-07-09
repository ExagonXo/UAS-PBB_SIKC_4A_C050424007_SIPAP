import 'dart:convert';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:go_router/go_router.dart';
import 'package:sipap_flutter/core/router/app_router.dart';

// Background message handler — must be a top-level function
@pragma('vm:entry-point')
Future<void> _firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  try {
    await Firebase.initializeApp();
  } catch (e) {
    debugPrint('Firebase bg init skipped/failed: $e');
  }
  debugPrint('Handling background message: ${message.messageId}');
}

class NotificationService {
  NotificationService._();
  static final NotificationService instance = NotificationService._();

  // _messaging is intentionally nullable and only assigned AFTER
  // Firebase.initializeApp() succeeds inside initialize().
  // This prevents the 'No Firebase App' crash at constructor time.
  FirebaseMessaging? _messaging;

  final FlutterLocalNotificationsPlugin _localNotificationsPlugin =
      FlutterLocalNotificationsPlugin();

  bool _isFirebaseInitialized = false;

  Future<void> initialize() async {
    try {
      await Firebase.initializeApp();
      // Only set _messaging after Firebase is ready
      _messaging = FirebaseMessaging.instance;
      _isFirebaseInitialized = true;
      debugPrint('Firebase successfully initialized for notifications.');
    } catch (e) {
      debugPrint(
          'Firebase init failed (possibly missing google-services.json): $e');
      _isFirebaseInitialized = false;
    }

    // Initialize Local Notifications (doesn't need Firebase)
    await _initLocalNotifications();

    if (_isFirebaseInitialized && _messaging != null) {
      // Set background handler
      FirebaseMessaging.onBackgroundMessage(
          _firebaseMessagingBackgroundHandler);

      // Request notification permissions
      await requestPermission();

      // Configure foreground notification presentation (iOS)
      await _messaging!.setForegroundNotificationPresentationOptions(
        alert: true,
        badge: true,
        sound: true,
      );

      // Handle message when app is in foreground
      FirebaseMessaging.onMessage.listen((RemoteMessage message) {
        debugPrint('Got a message whilst in the foreground!');
        if (message.notification != null) {
          _showLocalNotification(message);
        }
      });

      // Handle message when app is opened from a background state
      FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
        _handleNotificationClick(message.data);
      });

      // Check if app was opened from a terminated state via notification
      final initialMessage = await _messaging!.getInitialMessage();
      if (initialMessage != null) {
        debugPrint('App opened from terminated state via notification!');
        _handleNotificationClick(initialMessage.data);
      }
    }
  }

  Future<void> _initLocalNotifications() async {
    const AndroidInitializationSettings initializationSettingsAndroid =
        AndroidInitializationSettings('@mipmap/ic_launcher');

    const InitializationSettings initializationSettings =
        InitializationSettings(android: initializationSettingsAndroid);

    await _localNotificationsPlugin.initialize(
      settings: initializationSettings,
      onDidReceiveNotificationResponse: (NotificationResponse details) {
        final payload = details.payload;
        if (payload != null && payload.isNotEmpty) {
          try {
            final data =
                jsonDecode(payload) as Map<String, dynamic>;
            _handleNotificationClick(data);
          } catch (e) {
            debugPrint('Failed to parse local notification payload: $e');
          }
        }
      },
    );

    // Create Android notification channel for Android 8.0+
    final androidImpl = _localNotificationsPlugin
        .resolvePlatformSpecificImplementation<
            AndroidFlutterLocalNotificationsPlugin>();

    if (androidImpl != null) {
      const channel = AndroidNotificationChannel(
        'sipap_high_importance_channel',
        'SIPAP Notifications',
        description: 'This channel is used for SIPAP app notifications.',
        importance: Importance.max,
        playSound: true,
        enableVibration: true,
      );
      await androidImpl.createNotificationChannel(channel);
    }
  }

  Future<void> requestPermission() async {
    if (!_isFirebaseInitialized || _messaging == null) return;
    try {
      final settings = await _messaging!.requestPermission(
        alert: true,
        badge: true,
        sound: true,
      );
      debugPrint(
          'User granted permission: ${settings.authorizationStatus}');
    } catch (e) {
      debugPrint('Failed to request notification permission: $e');
    }
  }

  Future<String?> getFcmToken() async {
    if (!_isFirebaseInitialized || _messaging == null) return null;
    try {
      return await _messaging!.getToken();
    } catch (e) {
      debugPrint('Failed to get FCM token: $e');
      return null;
    }
  }

  void _showLocalNotification(RemoteMessage message) async {
    final notification = message.notification;
    final android = message.notification?.android;

    if (notification != null) {
      await _localNotificationsPlugin.show(
        id: notification.hashCode,
        title: notification.title,
        body: notification.body,
        notificationDetails: NotificationDetails(
          android: AndroidNotificationDetails(
            'sipap_high_importance_channel',
            'SIPAP Notifications',
            channelDescription:
                'This channel is used for SIPAP app notifications.',
            importance: Importance.max,
            priority: Priority.high,
            icon: android?.smallIcon ?? '@mipmap/ic_launcher',
          ),
        ),
        payload: jsonEncode(message.data),
      );
    }
  }

  void _handleNotificationClick(Map<String, dynamic> data) {
    debugPrint('Notification clicked with data: $data');
    final peminjamanId =
        data['peminjaman_id']?.toString() ?? data['id']?.toString();

    if (peminjamanId != null) {
      Future.delayed(const Duration(milliseconds: 500), () {
        try {
          final context = rootNavigatorKey.currentContext;
          if (context != null && context.mounted) {
            context.push('/peminjaman/$peminjamanId');
          }
        } catch (e) {
          debugPrint('Error navigating to peminjaman detail: $e');
        }
      });
    }
  }
}
