<?php

namespace App\Events;

use App\Models\Refaccion;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockBajo
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Refaccion $refaccion,
        public int $stockActual,
        public int $stockMinimo
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
