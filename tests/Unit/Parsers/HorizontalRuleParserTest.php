<?php

use Blockshift\ChatMarkdown\Parsers\HorizontalRuleParser;

it('detects horizontal rules with dashes', function () {
    $parser = new HorizontalRuleParser;

    expect($parser->isHorizontalRule('---'))->toBeTrue();
    expect($parser->isHorizontalRule('----'))->toBeTrue();
    expect($parser->isHorizontalRule('-----'))->toBeTrue();
});

it('detects horizontal rules with asterisks', function () {
    $parser = new HorizontalRuleParser;

    expect($parser->isHorizontalRule('***'))->toBeTrue();
    expect($parser->isHorizontalRule('****'))->toBeTrue();
    expect($parser->isHorizontalRule('*****'))->toBeTrue();
});

it('detects horizontal rules with underscores', function () {
    $parser = new HorizontalRuleParser;

    expect($parser->isHorizontalRule('___'))->toBeTrue();
    expect($parser->isHorizontalRule('____'))->toBeTrue();
    expect($parser->isHorizontalRule('_____'))->toBeTrue();
});

it('rejects lines with insufficient characters', function () {
    $parser = new HorizontalRuleParser;

    expect($parser->isHorizontalRule('--'))->toBeFalse();
    expect($parser->isHorizontalRule('**'))->toBeFalse();
    expect($parser->isHorizontalRule('__'))->toBeFalse();
});

it('rejects regular text', function () {
    $parser = new HorizontalRuleParser;

    expect($parser->isHorizontalRule('regular text'))->toBeFalse();
    expect($parser->isHorizontalRule('---text'))->toBeFalse();
});
