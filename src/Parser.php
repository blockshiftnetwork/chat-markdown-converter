<?php

namespace Blockshift\ChatMarkdown;

use Blockshift\ChatMarkdown\Parsers\BlockquoteParser;
use Blockshift\ChatMarkdown\Parsers\CodeBlockParser;
use Blockshift\ChatMarkdown\Parsers\HeaderParser;
use Blockshift\ChatMarkdown\Parsers\HorizontalRuleParser;
use Blockshift\ChatMarkdown\Parsers\LinkParser;
use Blockshift\ChatMarkdown\Parsers\StyleParser;
use Blockshift\ChatMarkdown\Parsers\TableParser;
use Blockshift\ChatMarkdown\Support\IntermediateRepresentation;

final class Parser
{
    private array $options = [
        'table_mode' => 'bullets',
        'parse_tables' => true,
        'parse_code_blocks' => true,
        'parse_links' => true,
        'parse_styles' => true,
        'parse_blockquotes' => true,
        'parse_horizontal_rules' => true,
        'parse_headers' => true,
    ];

    public function __construct(
        private readonly CodeBlockParser $codeBlockParser,
        private readonly TableParser $tableParser,
        private readonly LinkParser $linkParser,
        private readonly StyleParser $styleParser,
        private readonly BlockquoteParser $blockquoteParser,
        private readonly HorizontalRuleParser $horizontalRuleParser,
        private readonly HeaderParser $headerParser
    ) {}

    public static function new(): self
    {
        return new self(
            new CodeBlockParser,
            new TableParser,
            new LinkParser,
            new StyleParser,
            new BlockquoteParser,
            new HorizontalRuleParser,
            new HeaderParser
        );
    }

    public function parse(string $markdown): IntermediateRepresentation
    {
        $ir = IntermediateRepresentation::empty();
        $this->tableParser->reset();
        $this->blockquoteParser->reset();

        $markdown = $this->normalizeNewlines($markdown);
        $lines = explode("\n", $markdown);
        $buffer = '';
        $inCodeBlock = false;
        $codeBlockLang = '';

        foreach ($lines as $line) {
            if ($this->options['parse_code_blocks']) {
                $codeBlockResult = $this->codeBlockParser->parse($line, $inCodeBlock, $codeBlockLang);

                if ($codeBlockResult['in_code_block'] !== $inCodeBlock) {
                    if ($inCodeBlock) {
                        $content = trim($buffer);
                        if ($content !== '') {
                            $ir = $ir->addBlock('code', ['content' => $content, 'lang' => $codeBlockLang]);
                        }
                        $buffer = '';
                    } else {
                        if ($buffer !== '') {
                            $ir = $this->parseBuffer($ir, $buffer);
                            $buffer = '';
                        }
                    }
                    $inCodeBlock = $codeBlockResult['in_code_block'];
                    $codeBlockLang = $codeBlockResult['lang'] ?? '';

                    continue;
                }

                if ($inCodeBlock) {
                    $buffer .= $line."\n";

                    continue;
                }
            }

            if ($this->options['parse_tables'] && $this->tableParser->isTableLine($line)) {
                if ($buffer !== '') {
                    $ir = $this->parseBuffer($ir, $buffer);
                    $buffer = '';
                }

                $this->tableParser->parseLine($line);

                continue;
            }

            if ($this->tableParser->isInTable()) {
                $tableResult = $this->tableParser->finishTable();
                if ($tableResult !== null) {
                    $ir = $ir->addBlock('table', $tableResult);
                }
                $this->tableParser->reset();
            }

            if ($this->options['parse_blockquotes'] && $this->blockquoteParser->isBlockquoteLine($line)) {
                if ($buffer !== '') {
                    $ir = $this->parseBuffer($ir, $buffer);
                    $buffer = '';
                }

                $blockquoteResult = $this->blockquoteParser->parseLine($line);
                if ($blockquoteResult !== null) {
                    $ir = $ir->addBlock('blockquote', $blockquoteResult);
                }

                continue;
            }

            if ($this->blockquoteParser->isInBlockquote()) {
                $blockquoteResult = $this->blockquoteParser->parseLine($line);
                if ($blockquoteResult !== null) {
                    $ir = $ir->addBlock('blockquote', $blockquoteResult);
                }

                continue;
            }

            if ($this->options['parse_horizontal_rules'] && $this->horizontalRuleParser->isHorizontalRule($line)) {
                if ($buffer !== '') {
                    $ir = $this->parseBuffer($ir, $buffer);
                    $buffer = '';
                }

                $ir = $ir->addBlock('horizontal_rule', []);

                continue;
            }

            if ($this->options['parse_headers'] && $this->headerParser->isHeader($line)) {
                if ($buffer !== '') {
                    $ir = $this->parseBuffer($ir, $buffer);
                    $buffer = '';
                }

                $headerResult = $this->headerParser->parse($line);
                if ($headerResult !== null) {
                    $ir = $ir->addBlock('header', $headerResult);
                }

                continue;
            }

            $buffer .= $line."\n";
        }

        if ($buffer !== '') {
            $ir = $this->parseBuffer($ir, $buffer);
        }

        if ($this->tableParser->isInTable()) {
            $tableResult = $this->tableParser->finishTable();
            if ($tableResult !== null) {
                $ir = $ir->addBlock('table', $tableResult);
            }
            $this->tableParser->reset();
        }

        if ($this->blockquoteParser->isInBlockquote()) {
            $blockquoteResult = $this->blockquoteParser->finishBlockquote();
            if ($blockquoteResult !== null) {
                $ir = $ir->addBlock('blockquote', $blockquoteResult);
            }
            $this->blockquoteParser->reset();
        }

        return $ir;
    }

    public function withOptions(array $options): self
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    private function parseBuffer(IntermediateRepresentation $ir, string $buffer): IntermediateRepresentation
    {
        $buffer = trim($buffer);

        if ($buffer === '') {
            return $ir;
        }

        $paragraphs = preg_split('/\n\s*\n/', $buffer);

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);

            if ($paragraph === '') {
                continue;
            }

            if ($this->options['parse_links']) {
                $paragraph = $this->linkParser->parse($paragraph);
            }

            if ($this->options['parse_styles']) {
                $paragraph = $this->styleParser->parse($paragraph);
            }

            $ir = $ir->addBlock('paragraph', ['content' => $paragraph]);
        }

        return $ir;
    }

    public function getTableParser(): TableParser
    {
        return $this->tableParser;
    }

    public function getBlockquoteParser(): BlockquoteParser
    {
        return $this->blockquoteParser;
    }

    private function normalizeNewlines(string $text): string
    {
        return str_replace(["\r\n", "\r"], "\n", $text);
    }
}
