<?php

namespace Blockshift\ChatMarkdown\Parsers;

final class CodeBlockParser
{
    private const CODE_BLOCK_PATTERN = '/^```(\w*)$/';

    public function parse(string $line, bool $inCodeBlock, string $currentLang): array
    {
        $matches = [];

        if (preg_match(self::CODE_BLOCK_PATTERN, $line, $matches)) {
            if ($inCodeBlock) {
                return [
                    'in_code_block' => false,
                    'content' => '',
                    'lang' => '',
                ];
            }

            return [
                'in_code_block' => true,
                'lang' => $matches[1] ?? '',
            ];
        }

        return [
            'in_code_block' => $inCodeBlock,
            'content' => $line,
            'lang' => $currentLang,
        ];
    }
}
