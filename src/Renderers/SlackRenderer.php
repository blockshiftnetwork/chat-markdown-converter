<?php

namespace Blockshift\ChatMarkdown\Renderers;

class SlackRenderer extends AbstractRenderer
{
    protected function renderBlock(array $block): string
    {
        return match ($block['type']) {
            'paragraph' => $this->renderParagraph($block['content']),
            'code' => $this->renderCodeBlock($block['content'], $block['lang'] ?? null),
            'table' => $this->renderTable($block),
            'blockquote' => $this->renderBlockquote($block['content']),
            'horizontal_rule' => $this->renderHorizontalRule(),
            default => '',
        };
    }

    protected function escapeText(string $text): string
    {
        $text = str_replace('&', '&amp;', $text);
        $text = str_replace('<', '&lt;', $text);
        $text = str_replace('>', '&gt;', $text);

        return $text;
    }

    protected function renderParagraph(string $content): string
    {
        $content = $this->convertLinks($content);
        $content = preg_replace_callback('/~~(.+?)~~/', fn ($m) => "~{$m[1]}~", $content);
        $content = preg_replace_callback('/\*\*(.+?)\*\*/', fn ($m) => "*{$m[1]}*", $content);

        return $content;
    }

    private function convertLinks(string $content): string
    {
        return preg_replace_callback('/(.+?) \((https?:\/\/[^\)]+)\)/', function ($matches) {
            return '<'.$matches[2].'|'.$matches[1].'>';
        }, $content);
    }

    protected function renderCodeBlock(string $content, ?string $lang = null): string
    {
        $escaped = $this->escapeText($content);
        $lang = $lang ?? '';

        return "```{$lang}\n{$escaped}\n```";
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
                    $output .= "• *{$header}*: {$cell}\n";
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
