<?php

use Blockshift\ChatMarkdown\Parsers\StyleParser;

it('parses bold text', function () {
    $parser = new StyleParser;
    $result = $parser->parse('**bold**');

    expect($result)->toContain('**bold**');
});

it('parses italic text', function () {
    $parser = new StyleParser;
    $result = $parser->parse('*italic*');

    expect($result)->toContain('*italic*');
});

it('parses strikethrough text', function () {
    $parser = new StyleParser;
    $result = $parser->parse('~~strike~~');

    expect($result)->toContain('~~strike~~');
});

it('parses inline code', function () {
    $parser = new StyleParser;
    $result = $parser->parse('`code`');

    expect($result)->toContain('`code`');
});
