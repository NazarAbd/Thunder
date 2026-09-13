<?php

namespace App\Notifications;

use App\Models\TopUpRequest;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class TopUpRequestReviewed extends Notification
{
    public function __construct(
        public readonly TopUpRequest $topUpRequest,
        public readonly string $title,
        public readonly string $body,
    ) {}

    /**
     * Deliver through both channels, same as the Filament notification
     * this replaces: "database" (so it shows on /notifications and the
     * bell dropdown) and "broadcast" (so it appears live via Reverb).
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * This array is what gets saved as JSON in the notifications table's
     * `data` column. `topup_request_id` is the new piece — it's what lets
     * NotificationController::read() redirect straight to this specific
     * request instead of just the generic wallet page.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'topup_request_id' => $this->topUpRequest->id,
        ];
    }

    /**
     * This is what gets pushed live over the private "App.Models.User.{id}"
     * channel. Laravel automatically adds an `id` field (the notification's
     * own UUID) on top of this array before broadcasting — that's the
     * `notification.id` your notification-bell.blade.php JS already reads.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => $this->title,
            'body' => $this->body,
            'topup_request_id' => $this->topUpRequest->id,
        ]);
    }
}
