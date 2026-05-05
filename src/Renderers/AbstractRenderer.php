<?php

namespace Blockshift\ChatMarkdown\Renderers;

use Blockshift\ChatMarkdown\Renderers\Contracts\RendererContract;
use Blockshift\ChatMarkdown\Support\IntermediateRepresentation;
use Blockshift\ChatMarkdown\Support\TextChunker;

abstract class AbstractRenderer implements RendererContract
{
    public function render(IntermediateRepresentation $ir, ?int $maxLength = null): string|array
    {
        $output = '';

        foreach ($ir->getBlocks() as $block) {
            $output .= $this->renderBlock($block)."\n\n";
        }

        $output = trim($output);

        if ($maxLength !== null) {
            return TextChunker::chunk($output, $maxLength);
        }

        return $output;
    }

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

    protected function renderParagraph(string $content): string
    {
        return $content;
    }

    protected function renderHeader(string $content, int $level): string
    {
        return $content;
    }

    protected function renderCodeBlock(string $content, ?string $lang = null): string
    {
        return $content;
    }

    protected function renderTable(array $data): string
    {
        return '';
    }

    protected function renderBlockquote(string $content): string
    {
        return "> {$content}";
    }

    protected function renderHorizontalRule(): string
    {
        return '---';
    }

    protected function escapeText(string $text): string
    {
        return $text;
    }

    protected function formatBold(string $text): string
    {
        return $text;
    }

    protected function formatItalic(string $text): string
    {
        return $text;
    }

    protected function formatStrike(string $text): string
    {
        return $text;
    }

    protected function formatCode(string $text, ?string $lang = null): string
    {
        return $text;
    }

    protected function formatLink(string $text, string $url): string
    {
        return "{$text} ({$url})";
    }

    protected function formatBlockquote(string $text): string
    {
        return "> {$text}";
    }
}
