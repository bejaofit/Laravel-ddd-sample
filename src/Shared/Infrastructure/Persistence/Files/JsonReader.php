<?php

namespace Bejao\Shared\Infrastructure\Persistence\Files;

use RuntimeException;

final class JsonReader
{
    /**
     * @param string $fileName
     * @return string
     */
    public function read(string $fileName): string
    {
        $fileContent = file_get_contents($fileName);
        if (false === $fileContent) {
            throw new RuntimeException('Can\'t open file ' . $fileName);
        }

        json_decode($fileContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Incorrect JSON format: " . $fileName);
        }

        return $fileContent;
    }
}
