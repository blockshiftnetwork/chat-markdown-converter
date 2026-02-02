<?php

namespace Blockshift\ChatMarkdown\Support;

final class TextChunker
{
    private const SAFE_BREAKPOINTS = [' ', "\n", "\t", '.', ',', ';', ':', '-', '!', '?', ')', ']', '}', '"', "'"];

    private const MAX_CHUNK_OVERSHOOT = 50;

    public static function chunk(string $text, int $maxLength, string $encoding = 'UTF-8'): array
    {
        if (self::getLength($text, $encoding) <= $maxLength) {
            return [$text];
        }

        $chunks = [];
        $remaining = $text;

        while (! empty($remaining)) {
            $chunkLength = self::findSafeBreakpoint($remaining, $maxLength, $encoding);
            $chunks[] = mb_substr($remaining, 0, $chunkLength, $encoding);
            $remaining = mb_substr($remaining, $chunkLength, null, $encoding);
            $remaining = ltrim($remaining, " \t\n");
        }

        return $chunks;
    }

    private static function findSafeBreakpoint(string $text, int $maxLength, string $encoding): int
    {
        $textLength = mb_strlen($text, $encoding);

        if ($textLength <= $maxLength) {
            return $textLength;
        }

        $overshoot = min(self::MAX_CHUNK_OVERSHOOT, $textLength - $maxLength);
        $searchEnd = min($maxLength + $overshoot, $textLength);

        for ($i = $maxLength; $i < $searchEnd; $i++) {
            $char = mb_substr($text, $i, 1, $encoding);

            if (in_array($char, self::SAFE_BREAKPOINTS, true)) {
                return $i + 1;
            }
        }

        return $maxLength;
    }

    private static function getLength(string $text, string $encoding): int
    {
        return mb_strlen($text, $encoding);
    }
}
