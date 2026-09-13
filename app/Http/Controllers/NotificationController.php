<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(15);

        return view('notifications.index', ['notifications' => $notifications]);
    }

    public function read(Request $request, string $notification): RedirectResponse
    {
        $notificationModel = $request->user()->notifications()->findOrFail($notification);

        if (is_null($notificationModel->read_at)) {
            $notificationModel->markAsRead();
        }

        $topUpRequestId = $notificationModel->data['topup_request_id'] ?? null;

        if ($topUpRequestId) {
            return redirect()->route('wallet.topup.show', $topUpRequestId);
        }

        return redirect()->route('wallet.index');
    }
}
