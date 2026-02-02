<?php

use Blockshift\ChatMarkdown\Parsers\LinkParser;

it('parses markdown links', function () {
    $parser = new LinkParser;
    $result = $parser->parse('[Link](https://example.com)');

    expect($result)->toContain('(https://example.com)');
});

it('leaves text without links unchanged', function () {
    $parser = new LinkParser;
    $result = $parser->parse('Just text');

    expect($result)->toBe('Just text');
});
