<?php

namespace Bejao\Shared\Infrastructure\Persistence\Files;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

final class DirectoryFilesScanner
{
    public const BASE_IMPORT_DIRECTORY = 'storage/imports/';

    /**
     * @param string $directoryName
     * @return array<string,mixed>
     */
    public function scan(string $directoryName): array
    {
        $directoryName = self::BASE_IMPORT_DIRECTORY . $directoryName;
        if (!is_dir($directoryName)) {
            throw new RuntimeException("Directory " . $directoryName . " not found");
        }

        return $this->getFiles($directoryName);
    }


    /**
     * @param string $directoryName
     * @return array<string,mixed>
     */
    private function getFiles(string $directoryName): array
    {
        $filesIt = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directoryName, FilesystemIterator::SKIP_DOTS));
        $filesIt->rewind();

        $files = [];
        while ($filesIt->valid()) {
            $files[$filesIt->getSubPath()][] = $filesIt->key();
            $filesIt->next();
        }

        return $files;
    }
}
