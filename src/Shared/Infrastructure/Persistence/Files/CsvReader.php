<?php

namespace Bejao\Shared\Infrastructure\Persistence\Files;

use RuntimeException;

final class CsvReader
{
    /**
     * @param string $fileName
     * @param string $separator
     * @return iterable|CsvReaderLine[]
     */
    public function read(string $fileName, string $separator = ';'): iterable
    {

        $file = fopen($fileName, 'rb');
        if ($file === false) {
            throw new RuntimeException('Can\'t open file ' . $fileName);
        }
        $header = null;

        while (($data = fgetcsv($file, 1000, $separator)) !== false) {
            if ($header === null) {
                $header = $this->importHeader($data);
                continue;
            }
            yield new CsvReaderLine($data, $header);
        }
    }

    /**
     * @param array<string> $data
     * @return array<string>
     */
    private function importHeader(array $data): array
    {
        $header = [];
        foreach ($data as $key => $item) {
            $header[strtolower($item)] = $key;
        }
        return $header;
    }


}
