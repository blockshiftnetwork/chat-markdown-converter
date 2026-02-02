<?php

namespace Blockshift\ChatMarkdown\Renderers;

class WhatsAppRenderer extends AbstractRenderer
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

    protected function renderParagraph(string $content): string
    {
        $content = preg_replace_callback('/~~(.+?)~~/', fn ($m) => $m[1], $content);

        $content = preg_replace_callback('/\*\*(.+?)\*\*/', fn ($matches) => "__BOLD__".$matches[1]."__BOLD__", $content);

        $content = preg_replace_callback('/_(.+?)_/', fn ($matches) => "__ITALIC__".$matches[1]."__ITALIC__", $content);
        $content = preg_replace_callback('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/', fn ($matches) => "_{$matches[1]}_", $content);

        $content = str_replace('__ITALIC__', '_', $content);
        return str_replace('__BOLD__', '*', $content);
    }

    protected function renderCodeBlock(string $content, ?string $lang = null): string
    {
        return "```\n{$content}\n```";
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
        return "💬 {$content}";
    }

    protected function renderHorizontalRule(): string
    {
        return '---';
    }
}
