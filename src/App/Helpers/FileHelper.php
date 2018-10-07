<?php

declare(strict_types=1);


namespace App\Helpers;


class FileHelper
{
    public static function readJsonFile(string $file)
    {
        if (!file_exists($file)) {
            throw new \DomainException(sprintf('File "%s" does not exist.', $file));
        }

        $stream = file_get_contents($file);

        return json_decode($stream, true);
    }
}