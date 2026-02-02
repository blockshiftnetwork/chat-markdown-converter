<?php

namespace Blockshift\ChatMarkdown\Renderers;

class DiscordRenderer extends AbstractRenderer
{
    protected function renderBlock(array $block): string
    {
        return match ($block['type']) {
            'paragraph' => $this->renderParagraph($block['content']),
            'header' => $this->renderHeader($block['content'], $block['level'] ?? 1),
            'code' => $this->renderCodeBlock($block['content'], $block['lang'] ?? null),
            'table' => $this->renderTable($block),
            'blockquote' => $this->renderBlockquote($block['content']),
            'horizontal_rule' => $this->renderHorizontalRule(),
            default => '',
        };
    }

    protected function escapeText(string $text): string
    {
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace('*', '\\*', $text);
        $text = str_replace('_', '\\_', $text);
        $text = str_replace('~', '\\~', $text);
        $text = str_replace('`', '\\`', $text);
        $text = str_replace('>', '\\>', $text);

        return $text;
    }

    protected function renderHeader(string $content, int $level): string
    {
        return "**{$content}**";
    }

    protected function renderParagraph(string $content): string
    {
        $content = preg_replace_callback('/__BOLDITALIC__(.+?)__BOLDITALIC__/', fn ($m) => "***{$m[1]}***", $content);

        $content = preg_replace_callback('/__HIGHLIGHT__(.+?)__HIGHLIGHT__/', fn ($m) => "**{$m[1]}**", $content);

        $content = preg_replace('/!/', '', $content);

        $content = $this->convertLinks($content);

        return $content;
    }

    private function convertLinks(string $content): string
    {
        return preg_replace_callback('/(.+?) \((https?:\/\/[^\)]+)\)/', function ($matches) {
            return '['.$matches[1].']('.$matches[2].')';
        }, $content);
    }

    protected function renderCodeBlock(string $content, ?string $lang = null): string
    {
        $lang = $lang ?? '';

        return "```{$lang}\n{$content}\n```";
    }

    protected function renderTable(array $data): string
    {
        $headers = $data['headers'] ?? [];
        $rows = $data['rows'] ?? [];
        $output = '';

        foreach ($rows as $row) {
            foreach ($row as $index => $cell) {
                $header = $headers[$index] ?? '';
                if ($header !== '' && $cell !== '') {
                    $output .= "• **{$header}**: {$cell}\n";
                }
            }
        }

        return trim($output);
    }

    protected function renderBlockquote(string $content): string
    {
        return "> {$content}";
    }

    protected function renderHorizontalRule(): string
    {
        return '---';
    }
}
