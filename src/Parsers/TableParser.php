<?php

namespace Blockshift\ChatMarkdown\Parsers;

final class TableParser
{
    private const TABLE_ROW_PATTERN = '/^\|(.+)\|$/';

    private const TABLE_SEPARATOR_PATTERN = '/^\|(?:[-\s:]+\|)+$/';

    private array $currentTable = [];

    private array $currentHeaders = [];

    private bool $inTable = false;

    private bool $headersParsed = false;

    private array $columnAlignments = [];

    public function isTableLine(string $line): bool
    {
        return (bool) preg_match(self::TABLE_ROW_PATTERN, $line);
    }

    public function isInTable(): bool
    {
        return $this->inTable;
    }

    public function parseLine(string $line): ?array
    {
        if (preg_match(self::TABLE_SEPARATOR_PATTERN, $line)) {
            $this->headersParsed = true;
            $this->parseSeparator($line);

            return null;
        }

        $cells = $this->parseCells($line);

        if (! $this->inTable) {
            $this->inTable = true;
            $this->currentHeaders = $cells;

            return null;
        }

        if (! $this->headersParsed) {
            $this->currentHeaders = $cells;

            return null;
        }

        $this->currentTable[] = $cells;

        return null;
    }

    public function finishTable(): ?array
    {
        if (empty($this->currentTable)) {
            return null;
        }

        return $this->buildTable();
    }

    public function debug(): void
    {
        var_dump([
            'inTable' => $this->inTable,
            'headersParsed' => $this->headersParsed,
            'currentHeaders' => $this->currentHeaders,
            'currentTable' => $this->currentTable,
            'columnAlignments' => $this->columnAlignments,
        ]);
    }

    public function reset(): void
    {
        $this->currentTable = [];
        $this->currentHeaders = [];
        $this->inTable = false;
        $this->headersParsed = false;
        $this->columnAlignments = [];
    }

    private function parseCells(string $line): array
    {
        preg_match(self::TABLE_ROW_PATTERN, $line, $matches);

        $cells = array_map('trim', explode('|', $matches[1]));

        return array_values(array_filter($cells, fn ($cell) => $cell !== ''));
    }

    private function parseSeparator(string $line): void
    {
        preg_match(self::TABLE_SEPARATOR_PATTERN, $line, $matches);
        $separators = array_map('trim', explode('|', $matches[0]));
        $separators = array_values(array_filter($separators, fn ($sep) => $sep !== ''));

        foreach ($separators as $separator) {
            if (str_starts_with($separator, ':') && str_ends_with($separator, ':')) {
                $this->columnAlignments[] = 'center';
            } elseif (str_ends_with($separator, ':')) {
                $this->columnAlignments[] = 'right';
            } else {
                $this->columnAlignments[] = 'left';
            }
        }
    }

    private function buildTable(): array
    {
        return [
            'type' => 'table',
            'headers' => $this->currentHeaders,
            'rows' => $this->currentTable,
            'alignments' => $this->columnAlignments,
        ];
    }
}
