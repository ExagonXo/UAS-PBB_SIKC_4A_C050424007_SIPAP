<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class FirebaseService
{
    public static function sendNotification(User $user, $title, $body)
    {
        // Simpan ke database lokal dulu
        Notifikasi::create([
            'user_id' => $user->id,
            'judul' => $title,
            'pesan' => $body,
            'is_read' => false
        ]);

        // Kirim ke FCM jika token ada
        if ($user->fcm_token) {
            // Catatan: Ini menggunakan pola FCM HTTP v1 API (Simulasi payload)
            // Anda memerlukan Service Account Key dari Firebase Console untuk implementasi penuh
            // Http::withToken('YOUR_FCM_BEARER_TOKEN')
            //     ->post('https://fcm.googleapis.com/v1/projects/YOUR_PROJECT_ID/messages:send', [
            //         'message' => [
            //             'token' => $user->fcm_token,
            //             'notification' => [
            //                 'title' => $title,
            //                 'body' => $body,
            //             ],
            //         ],
            //     ]);
        }
    }
}