<?php

namespace Blockshift\ChatMarkdown\Renderers;

use Blockshift\ChatMarkdown\Support\UnicodeStyler;

class InstagramRenderer extends AbstractRenderer
{
    private const HORIZONTAL_RULE = '━━━━━━━━━━━━━━━━';

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

    protected function renderHeader(string $content, int $level): string
    {
        return UnicodeStyler::bold($content);
    }

    protected function renderParagraph(string $content): string
    {
        $content = preg_replace_callback(
            '/__BOLDITALIC__(.+?)__BOLDITALIC__/',
            fn ($m) => UnicodeStyler::boldItalic($m[1]),
            $content
        );

        $content = preg_replace_callback(
            '/__HIGHLIGHT__(.+?)__HIGHLIGHT__/',
            fn ($m) => UnicodeStyler::bold($m[1]),
            $content
        );

        $content = preg_replace_callback(
            '/\*\*(.+?)\*\*/',
            fn ($m) => UnicodeStyler::bold($m[1]),
            $content
        );

        $content = preg_replace_callback(
            '/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/',
            fn ($m) => UnicodeStyler::italic($m[1]),
            $content
        );

        $content = preg_replace_callback(
            '/~~(.+?)~~/',
            fn ($m) => UnicodeStyler::strikethrough($m[1]),
            $content
        );

        $content = preg_replace_callback(
            '/`(.+?)`/',
            fn ($m) => UnicodeStyler::monospace($m[1]),
            $content
        );

        $content = preg_replace_callback(
            '/(.+?) \((https?:\/\/[^\)]+)\)/',
            fn ($m) => "{$m[1]}: {$m[2]}",
            $content
        );

        $content = preg_replace('/!/', '', $content);

        $content = preg_replace('/-\s+\[x\]\s*(.*)/', '✅ $1', $content);
        $content = preg_replace('/-\s+\[\s\]\s*(.*)/', '⬜ $1', $content);

        $content = preg_replace('/^-\s+/m', '• ', $content);

        return $content;
    }

    protected function renderCodeBlock(string $content, ?string $lang = null): string
    {
        $lines = explode("\n", $content);
        $monospace = implode("\n", array_map(fn ($line) => UnicodeStyler::monospace($line), $lines));

        return self::HORIZONTAL_RULE."\n".$monospace."\n".self::HORIZONTAL_RULE;
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
                    $boldHeader = UnicodeStyler::bold($header);
                    $output .= "• {$boldHeader}: {$cell}\n";
                }
            }
        }

        return trim($output);
    }

    protected function renderBlockquote(string $content): string
    {
        return "❝ {$content} ❞";
    }

    protected function renderHorizontalRule(): string
    {
        return self::HORIZONTAL_RULE;
    }
}
