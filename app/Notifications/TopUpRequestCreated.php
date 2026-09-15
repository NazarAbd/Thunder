<?php

namespace App\Notifications;

use App\Models\TopUpRequest;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

/**
 * Sync notification for admins when a user submits a top-up request.
 *
 * Deliberately does NOT implement ShouldQueue, so it writes to the
 * `notifications` table immediately even when no queue worker is running
 * (QUEUE_CONNECTION=database). The `format => filament` key is required
 * so Filament's admin bell query (`where('data->format', 'filament')`)
 * actually shows it.
 */
class TopUpRequestCreated extends Notification
{
    public function __construct(
        public readonly TopUpRequest $topUpRequest,
        public readonly string $title,
        public readonly string $body,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Deep-link to the exact row in the admin TopUpRequests list.
     * Uses the resource URL with ?highlight={id} so the table can draw
     * a blue square around that row and scroll to it.
     */
    public function targetUrl(): string
    {
        try {
            return \App\Filament\Resources\TopUpRequests\TopUpRequestResource::getUrl('index', [
                'highlight' => $this->topUpRequest->id,
            ]);
        } catch (\Throwable) {
            return url('/admin-panelx2/top-up-requests?highlight=' . $this->topUpRequest->id);
        }
    }

    /**
     * Filament action array rendered inside the admin bell item.
     * shouldMarkAsRead is deliberately false: per product decision the
     * admin notification must stay unread until manually cleared, otherwise
     * a pending request could look handled while still awaiting approve/reject.
     */
    protected function viewAction(): array
    {
        return [
            'name' => 'view-request',
            'label' => 'عرض الطلب',
            'url' => $this->targetUrl(),
            'shouldOpenUrlInNewTab' => false,
            'shouldClose' => true,
            'shouldMarkAsRead' => false,
            'shouldMarkAsUnread' => false,
            'shouldPostToUrl' => false,
            'color' => 'primary',
            'icon' => 'heroicon-o-eye',
            'view' => 'filament::components.link',
        ];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'actions' => [$this->viewAction()],
            'body' => $this->body,
            'color' => null,
            'duration' => 'persistent',
            'icon' => 'heroicon-o-building-library',
            'iconColor' => 'info',
            'status' => 'info',
            'title' => $this->title,
            'view' => null,
            'viewData' => [],
            'format' => 'filament',
            'topup_request_id' => $this->topUpRequest->id,
            'target_url' => $this->targetUrl(),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'actions' => [$this->viewAction()],
            'body' => $this->body,
            'color' => null,
            'duration' => 'persistent',
            'icon' => 'heroicon-o-building-library',
            'iconColor' => 'info',
            'status' => 'info',
            'title' => $this->title,
            'view' => null,
            'viewData' => [],
            'format' => 'filament',
            'topup_request_id' => $this->topUpRequest->id,
            'target_url' => $this->targetUrl(),
        ]);
    }
}
