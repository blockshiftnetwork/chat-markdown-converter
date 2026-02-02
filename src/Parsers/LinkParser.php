<?php

namespace Blockshift\ChatMarkdown\Parsers;

final class LinkParser
{
    private const LINK_PATTERN = '/!\[(.*?)\]\((.*?)\)|\[(.*?)\]\((.*?)\)/';

    public function parse(string $text): string
    {
        return preg_replace_callback(self::LINK_PATTERN, function ($matches) {
            if ($matches[1] !== '') {
                return $this->formatImage($matches[1], $matches[2]);
            }

            return $this->formatLink($matches[3], $matches[4]);
        }, $text);
    }

    private function formatLink(string $text, string $url): string
    {
        return "{$text} ({$url})";
    }

    private function formatImage(string $alt, string $url): string
    {
        return "!{$alt} ({$url})";
    }
}
