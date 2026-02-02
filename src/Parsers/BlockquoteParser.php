<?php

namespace Blockshift\ChatMarkdown\Parsers;

final class BlockquoteParser
{
    private const BLOCKQUOTE_PATTERN = '/^\>\s*(.+)/';

    private bool $inBlockquote = false;

    private array $lines = [];

    public function isBlockquoteLine(string $line): bool
    {
        return (bool) preg_match(self::BLOCKQUOTE_PATTERN, $line);
    }

    public function parseLine(string $line): ?array
    {
        $matches = [];
        if (preg_match(self::BLOCKQUOTE_PATTERN, $line, $matches)) {
            $this->inBlockquote = true;
            $this->lines[] = $matches[1];

            return null;
        }

        if ($this->inBlockquote) {
            if (trim($line) === '' || trim($line) === '') {
                return $this->buildBlockquote();
            }

            $this->lines[] = trim($line);

            return null;
        }

        return null;
    }

    public function finishBlockquote(): ?array
    {
        if (empty($this->lines)) {
            return null;
        }

        $content = implode(' ', $this->lines);
        $this->reset();

        return [
            'type' => 'blockquote',
            'content' => $content,
        ];
    }

    public function isInBlockquote(): bool
    {
        return $this->inBlockquote;
    }

    public function reset(): void
    {
        $this->inBlockquote = false;
        $this->lines = [];
    }

    private function buildBlockquote(): ?array
    {
        if (empty($this->lines)) {
            return null;
        }

        $content = implode(' ', $this->lines);
        $this->reset();

        return [
            'type' => 'blockquote',
            'content' => $content,
        ];
    }
}
