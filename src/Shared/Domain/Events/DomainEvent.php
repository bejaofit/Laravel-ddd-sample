<?php

namespace Bejao\Shared\Domain\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

abstract class DomainEvent
{
    use Dispatchable, InteractsWithSockets;

    public ?int $eventId;
    public string $relatedId;
    public int $occurredOn;
    public ?int $userId;
    public ?string $stream;
    public ?string $initiator;
    /** @var array<string,mixed> */
    public array $data = [];

    final protected function __construct(string $relatedId, ?int $userId = null, ?string $initiator = null)
    {
        $this->relatedId = $relatedId;
        $this->userId = $userId;
        $this->occurredOn = time();
        $this->stream = null;
        $this->initiator = $initiator;
    }

    public function getName(): string
    {
        return str_replace('/', '.', static::class);
    }

    /**
     * @param string $relatedId
     * @param int|null $userId
     * @param string|null $initiator
     * @param array<string,mixed> $data
     * @return DomainEvent|static
     */
    public static function hydrate(string $relatedId, ?int $userId, ?string $initiator, array $data): self
    {
        $event = new static($relatedId, $userId, $initiator);
        $event->data = $data;
        return $event;
    }


}
