<?php

namespace Bejao\Shared\Domain\Services;

interface LoggerInterface
{
    public function error(string $message): void;

    public function info(string $message): void;

    public function warning(string $message): void;

    public function success(string $string): void;
}
