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

    abstract protected function renderBlock(array $block): string;

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
