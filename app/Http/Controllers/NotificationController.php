<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
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

    /**
     * Mark a notification as read, then send the user to the relevant page.
     *
     * Plain form posts get a normal redirect. The notification bell and the
     * notifications page use axios, which follows redirects silently without
     * navigating — so for JSON/XHR requests we return the target URL and let
     * the Alpine handlers do "window.location.href = redirect".
     */
    public function read(Request $request, string $notification): RedirectResponse|JsonResponse
    {
        $notificationModel = $request->user()->notifications()->findOrFail($notification);

        if (is_null($notificationModel->read_at)) {
            $notificationModel->markAsRead();
        }

        $topUpRequestId = $notificationModel->data['topup_request_id'] ?? null;

        $target = $topUpRequestId
            ? route('wallet.topup.show', $topUpRequestId)
            : route('wallet.index');

        if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['redirect' => $target]);
        }

        return redirect($target);
    }

    /**
     * Mark all unread notifications as read (nothing deleted).
     */
    public function readAll(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    /**
     * Clear = remove from the bell dropdown (delete rows).
     * Only ever called manually via the "مسح" button.
     */
    public function clear(Request $request): JsonResponse
    {
        $request->user()->notifications()->delete();

        return response()->json(['ok' => true]);
    }
}
