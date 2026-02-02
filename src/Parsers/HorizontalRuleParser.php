<?php

namespace Blockshift\ChatMarkdown\Parsers;

final class HorizontalRuleParser
{
    private const HORIZONTAL_RULE_PATTERN = '/^[-*_]{3,}\s*$/';

    public function isHorizontalRule(string $line): bool
    {
        return (bool) preg_match(self::HORIZONTAL_RULE_PATTERN, $line);
    }
}
