<?php

namespace Blockshift\ChatMarkdown\Renderers;

class WhatsAppRenderer extends AbstractRenderer
{
    protected function renderHeader(string $content, int $level): string
    {
        return "*{$content}*";
    }

    protected function renderParagraph(string $content): string
    {
        $content = preg_replace_callback('/~~(.+?)~~/', fn ($m) => "~{$m[1]}~", $content);

        $content = preg_replace_callback('/\*\*(.+?)\*\*/', fn ($matches) => '__BOLD__'.$matches[1].'__BOLD__', $content);

        $content = preg_replace_callback('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/', fn ($matches) => '__ITALIC__'.$matches[1].'__ITALIC__', $content);

        $content = preg_replace_callback('/__BOLDITALIC__(.+?)__BOLDITALIC__/', fn ($matches) => "_*{$matches[1]}*_", $content);

        $content = preg_replace_callback('/__HIGHLIGHT__(.+?)__HIGHLIGHT__/', fn ($matches) => "*{$matches[1]}*", $content);

        $content = str_replace('__ITALIC__', '_', $content);
        $content = str_replace('__BOLD__', '*', $content);

        $content = preg_replace_callback('/(.+?) \((https?:\/\/[^\)]+)\)/', fn ($matches) => "{$matches[1]}: {$matches[2]}", $content);

        $content = preg_replace('/-\s+\[x\]\s*(.*)/', '✅ $1', $content);
        $content = preg_replace('/-\s+\[\s\]\s*(.*)/', '⬜ $1', $content);

        return $content;
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
        return '💬 '.$this->renderParagraph($content);
    }

    protected function renderHorizontalRule(): string
    {
        return '---';
    }
}
