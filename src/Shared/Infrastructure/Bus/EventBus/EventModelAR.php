<?php

namespace Bejao\Shared\Infrastructure\Bus\EventBus;

use Bejao\Shared\Domain\Events\DomainEvent;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @property int $id
 * @property string $eventName
 * @property string $relatedId
 * @property ?string $initiatorId
 * @property ?int $userId
 * @property ?string $stream
 * @property int $occurredOn
 * @property array|string $data
 */
final class EventModelAR extends Model
{
    protected $table = 'event';
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        'id',
        'className',
        'relatedId',
        'stream',
        'initiatorId',
        'userId',
        'occurredOn',
        'data',

    ];

    /**
     * @param DomainEvent $domainEvent
     * @return EventModelAR
     */
    public static function create(DomainEvent $domainEvent): self
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $instance = new self();
        $instance->eventName = $domainEvent->getName();
        $instance->data = $serializer->serialize($domainEvent, 'json');
        $instance->relatedId = $domainEvent->relatedId;
        $instance->initiatorId = $domainEvent->initiator;
        $instance->userId = $domainEvent->userId;
        $instance->stream = $domainEvent->stream;
        $instance->occurredOn = $domainEvent->occurredOn;
        return $instance;
    }


}
