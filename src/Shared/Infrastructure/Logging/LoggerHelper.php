<?php

namespace Bejao\Shared\Infrastructure\Logging;

use Illuminate\Support\Facades\Log;

final class LoggerHelper
{
    public static function logException(\Throwable $exception): void
    {
        Log::error($exception->getMessage());
        if (app()->bound('sentry')) {
            app('sentry')->captureException($exception);
        }
    }
}
