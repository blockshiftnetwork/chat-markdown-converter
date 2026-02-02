<?php

namespace Blockshift\ChatMarkdown\Parsers;

final class StyleParser
{
    private const BOLD_PATTERN = '/\*\*(.*?)\*\*/';

    private const ITALIC_PATTERN = '/\*(.*?)\*/';

    private const STRIKE_PATTERN = '/~~(.*?)~~/';

    private const CODE_PATTERN = '/`(.*?)`/';

    public function parse(string $text): string
    {
        $text = $this->parseBold($text);
        $text = $this->parseItalic($text);
        $text = $this->parseStrike($text);
        $text = $this->parseInlineCode($text);

        return $text;
    }

    private function parseBold(string $text): string
    {
        return preg_replace_callback(self::BOLD_PATTERN, fn ($matches) => $this->formatBold($matches[1]), $text);
    }

    private function parseItalic(string $text): string
    {
        return preg_replace_callback(self::ITALIC_PATTERN, fn ($matches) => $this->formatItalic($matches[1]), $text);
    }

    private function parseStrike(string $text): string
    {
        return preg_replace_callback(self::STRIKE_PATTERN, fn ($matches) => $this->formatStrike($matches[1]), $text);
    }

    private function parseInlineCode(string $text): string
    {
        return preg_replace_callback(self::CODE_PATTERN, fn ($matches) => $this->formatInlineCode($matches[1]), $text);
    }

    private function formatBold(string $text): string
    {
        return "**{$text}**";
    }

    private function formatItalic(string $text): string
    {
        return "*{$text}*";
    }

    private function formatStrike(string $text): string
    {
        return "~~{$text}~~";
    }

    private function formatInlineCode(string $text): string
    {
        return "`{$text}`";
    }
}
