<?php

use Blockshift\ChatMarkdown\Support\TextChunker;

it('does not chunk short text', function () {
    $text = 'Short text';
    $result = TextChunker::chunk($text, 100);

    expect($result)->toBe([$text]);
});

it('chunks long text', function () {
    $text = str_repeat('a b c d e f ', 100);
    $result = TextChunker::chunk($text, 100);

    expect(is_array($result))->toBeTrue();
    expect(count($result))->toBeGreaterThan(1);
});

it('chunks at safe breakpoints', function () {
    $text = 'Hello world. This is a test.';
    $result = TextChunker::chunk($text, 20);

    expect($result[0])->not->toEndWith(' wo');
});
