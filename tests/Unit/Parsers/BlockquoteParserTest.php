<?php

use Blockshift\ChatMarkdown\Parsers\BlockquoteParser;

it('detects blockquote lines', function () {
    $parser = new BlockquoteParser;

    expect($parser->isBlockquoteLine('> Test quote'))->toBeTrue();
    expect($parser->isBlockquoteLine('>Another quote'))->toBeTrue();
    expect($parser->isBlockquoteLine('Not a quote'))->toBeFalse();
});

it('parses single line blockquotes', function () {
    $parser = new BlockquoteParser;
    $result = $parser->parseLine('> Test quote');

    expect($result)->toBeNull();
    expect($parser->isInBlockquote())->toBeTrue();

    $result = $parser->finishBlockquote();

    expect($result['type'])->toBe('blockquote');
    expect($result['content'])->toBe('Test quote');
});

it('parses multi-line blockquotes', function () {
    $parser = new BlockquoteParser;
    $parser->parseLine('> First line');
    $parser->parseLine('Second line');
    $parser->parseLine('> Third line');
    $result = $parser->finishBlockquote();

    expect($result['content'])->toBe('First line Second line Third line');
});

it('finishes blockquote on empty line', function () {
    $parser = new BlockquoteParser;
    $result1 = $parser->parseLine('> Test quote');
    $result2 = $parser->parseLine('');

    expect($result1)->toBeNull();
    expect($result2)->not->toBeNull();
    expect($result2['type'])->toBe('blockquote');
    expect($result2['content'])->toBe('Test quote');
    expect($parser->isInBlockquote())->toBeFalse();
});

it('resets parser state', function () {
    $parser = new BlockquoteParser;
    $parser->parseLine('> Test quote');
    expect($parser->isInBlockquote())->toBeTrue();

    $parser->reset();
    expect($parser->isInBlockquote())->toBeFalse();

    $result = $parser->finishBlockquote();
    expect($result)->toBeNull();
});
