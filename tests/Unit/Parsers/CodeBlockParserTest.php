<?php

use Blockshift\ChatMarkdown\Parsers\CodeBlockParser;

it('detects code block start', function () {
    $parser = new CodeBlockParser;
    $result = $parser->parse('```php', false, '');

    expect($result['in_code_block'])->toBeTrue();
    expect($result['lang'])->toBe('php');
});

it('detects code block end', function () {
    $parser = new CodeBlockParser;
    $result = $parser->parse('```', true, 'php');

    expect($result['in_code_block'])->toBeFalse();
});

it('handles code block content', function () {
    $parser = new CodeBlockParser;
    $result = $parser->parse('echo "Hello";', true, 'php');

    expect($result['in_code_block'])->toBeTrue();
    expect($result['content'])->toBe('echo "Hello";');
});
