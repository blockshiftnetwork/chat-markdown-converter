<?php

namespace Blockshift\ChatMarkdown\Parsers;

final class HeaderParser
{
    private const HEADER_PATTERN = '/^(#{1,6})\s+(.+)$/';

    public function parse(string $line): ?array
    {
        $matches = [];

        if (preg_match(self::HEADER_PATTERN, $line, $matches)) {
            $level = strlen($matches[1]);

            return [
                'level' => $level,
                'content' => trim($matches[2]),
            ];
        }

        return null;
    }

    public function isHeader(string $line): bool
    {
        return (bool) preg_match(self::HEADER_PATTERN, $line);
    }
}
