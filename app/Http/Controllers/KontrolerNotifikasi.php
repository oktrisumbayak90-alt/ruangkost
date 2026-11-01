<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class KontrolerNotifikasi extends Controller
{
    public function index()
    {
        $notifications = AppNotification::where('pengguna_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(20);

        // Tandai semua sebagai sudah dibaca
        AppNotification::where('pengguna_id', auth()->id())
            ->where('is_dibaca', false)
            ->update(['is_dibaca' => true, 'dibaca_pada' => now()]);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(AppNotification $notification)
    {
        if ($notification->pengguna_id !== auth()->id() && $notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        if ($notification->url) {
            return redirect($notification->url);
        }

        return back();
    }

    public function markAllRead()
    {
        AppNotification::where('pengguna_id', auth()->id())
            ->where('is_dibaca', false)
            ->update(['is_dibaca' => true, 'dibaca_pada' => now()]);

        return back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    }

    public function getUnreadCount()
    {
        $count = AppNotification::where('pengguna_id', auth()->id())
            ->where('is_dibaca', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    public function getRecent()
    {
        $notifications = AppNotification::where('pengguna_id', auth()->id())
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($notif) {
                return [
                    'id'         => $notif->id,
                    'judul'      => $notif->judul,
                    'pesan'      => $notif->pesan,
                    'tipe'       => $notif->tipe,
                    'url'        => $notif->url,
                    'is_dibaca'  => $notif->is_dibaca,
                    'waktu'      => $notif->created_at->diffForHumans(),
                    'tanggal'    => $notif->created_at->format('d M Y, H:i'),
                ];
            });

        $unreadCount = AppNotification::where('pengguna_id', auth()->id())
            ->where('is_dibaca', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    public function markReadJson(AppNotification $notification)
    {
        if ($notification->pengguna_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }
}
