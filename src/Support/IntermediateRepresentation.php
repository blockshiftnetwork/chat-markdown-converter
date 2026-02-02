<?php

namespace Blockshift\ChatMarkdown\Support;

final readonly class IntermediateRepresentation
{
    public function __construct(
        public array $blocks
    ) {}

    public static function empty(): self
    {
        return new self([]);
    }

    public function addBlock(string $type, array $data = []): self
    {
        return new self([
            ...$this->blocks,
            ['type' => $type, ...$data],
        ]);
    }

    public function getBlocks(): array
    {
        return $this->blocks;
    }

    public function isEmpty(): bool
    {
        return empty($this->blocks);
    }

    public function count(): int
    {
        return count($this->blocks);
    }
}
