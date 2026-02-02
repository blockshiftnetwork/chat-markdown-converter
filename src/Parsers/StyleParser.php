<?php

namespace Blockshift\ChatMarkdown\Parsers;

final class StyleParser
{
    private const BOLD_PATTERN = '/\*\*(.*?)\*\*/';

    private const ITALIC_PATTERN = '/\*(.*?)\*/';

    private const STRIKE_PATTERN = '/~~(.*?)~~/';

    private const CODE_PATTERN = '/`(.*?)`/';

    private const HIGHLIGHT_PATTERN = '/==(.*?)==/';

    public function parse(string $text): string
    {
        $text = $this->parseBoldItalic($text);
        $text = $this->parseBold($text);
        $text = $this->parseHighlight($text);
        $text = $this->parseItalic($text);
        $text = $this->parseStrike($text);
        $text = $this->parseInlineCode($text);

        return $text;
    }

    private function parseBoldItalic(string $text): string
    {
        $pattern = '/\*\*\*(.+?)\*\*\*/';

        return preg_replace_callback($pattern, fn ($matches) => $this->formatBoldItalic($matches[1]), $text);
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

    private function parseHighlight(string $text): string
    {
        return preg_replace_callback(self::HIGHLIGHT_PATTERN, fn ($matches) => $this->formatHighlight($matches[1]), $text);
    }

    private function formatBoldItalic(string $text): string
    {
        return "__BOLDITALIC__{$text}__BOLDITALIC__";
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

    private function formatHighlight(string $text): string
    {
        return "__HIGHLIGHT__{$text}__HIGHLIGHT__";
    }
}
