<?php

namespace Bejao\Core\Table\Domain\Enums;

enum TableStatusEnum: string
{
    case FREE = 'free';
    case OCCUPIED = 'occupied';
    case RESERVED = 'reserved';

}
