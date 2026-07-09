// This is a basic Flutter widget test.
//
// To perform an interaction with a widget in your test, use the WidgetTester
// utility in the flutter_test package. For example, you can send tap and scroll
// gestures. You can also use WidgetTester to find child widgets in the widget
// tree, read text, and verify that the values of widget properties are correct.

import 'package:flutter_test/flutter_test.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import 'package:sipap_flutter/app.dart';
import 'package:sipap_flutter/core/config/app_config.dart';

void main() {
  testWidgets('SIPAP app can be instantiated', (WidgetTester tester) async {
    // Initialize DotEnv before running the test
    await AppConfig.load();

    await tester.pumpWidget(
      const ProviderScope(child: SipapApp()),
    );

    await tester.pumpAndSettle();

    // If we get here without crashing, the test passes
    expect(find.byType(SipapApp), findsOneWidget);
  });
}
