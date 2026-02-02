<?php

namespace Blockshift\ChatMarkdown\Renderers;

class TelegramRenderer extends AbstractRenderer
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
        return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    protected function renderParagraph(string $content): string
    {
        $content = $this->escapeText($content);
        $content = $this->convertLinks($content);

        $content = preg_replace_callback('/~~(.+?)~~/', fn ($m) => "<s>{$m[1]}</s>", $content);
        $content = preg_replace_callback('/\*\*(.+?)\*\*/', fn ($m) => "<b>{$m[1]}</b>", $content);
        $content = preg_replace_callback('/\*(.+?)\*/', fn ($m) => "<i>{$m[1]}</i>", $content);
        $content = preg_replace_callback('/_(.+?)_/', fn ($m) => "<i>{$m[1]}</i>", $content);
        $content = preg_replace_callback('/`(.+?)`/', fn ($m) => "<code>{$m[1]}</code>", $content);

        return $content;
    }

    private function convertLinks(string $content): string
    {
        return preg_replace_callback('/(.+?) \((https?:\/\/[^\)]+)\)/', function ($matches) {
            return '<a href="'.$matches[2].'">'.$matches[1].'</a>';
        }, $content);
    }

    protected function renderCodeBlock(string $content, ?string $lang = null): string
    {
        $escaped = $this->escapeText($content);

        return "<pre>{$escaped}</pre>";
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
                    $output .= "• {$header}: {$cell}\n";
                }
            }
        }

        return trim($output);
    }

    protected function renderBlockquote(string $content): string
    {
        $content = $this->escapeText($content);

        return "💬 {$content}";
    }

    protected function renderHorizontalRule(): string
    {
        return '---';
    }
}
