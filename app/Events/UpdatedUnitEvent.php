<?php

namespace App\Events;

use App\Contracts\ILog;
use App\Unit;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UpdatedUnitEvent extends LogEvent implements ILog
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $tipoLog = 7;

    public function __construct(Unit $object)
    {
        $this->object = $object;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }

    public function getTipoLog(){
        return $this->tipoLog;
    }
}
