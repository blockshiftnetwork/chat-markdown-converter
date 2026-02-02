<?php

namespace Blockshift\ChatMarkdown\Parsers;

final class LinkParser
{
    private const LINK_PATTERN = '/\[(.*?)\]\((.*?)\)/';

    public function parse(string $text): string
    {
        return preg_replace_callback(self::LINK_PATTERN, function ($matches) {
            $text = $matches[1];
            $url = $matches[2];

            return $this->formatLink($text, $url);
        }, $text);
    }

    private function formatLink(string $text, string $url): string
    {
        return "{$text} ({$url})";
    }
}
