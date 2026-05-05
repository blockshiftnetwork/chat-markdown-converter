<?php

use Blockshift\ChatMarkdown\Support\UnicodeStyler;

it('converts lowercase letters to sans-serif bold', function () {
    expect(UnicodeStyler::bold('hello'))->toBe('𝗵𝗲𝗹𝗹𝗼');
});

it('converts uppercase letters to sans-serif bold', function () {
    expect(UnicodeStyler::bold('Hello'))->toBe('𝗛𝗲𝗹𝗹𝗼');
});

it('converts digits to sans-serif bold', function () {
    expect(UnicodeStyler::bold('123'))->toBe('𝟭𝟮𝟯');
});

it('preserves spaces and punctuation in bold', function () {
    expect(UnicodeStyler::bold('Hi, world!'))->toBe('𝗛𝗶, 𝘄𝗼𝗿𝗹𝗱!');
});

it('converts lowercase letters to sans-serif italic', function () {
    expect(UnicodeStyler::italic('abc'))->toBe('𝘢𝘣𝘤');
});

it('converts uppercase letters to sans-serif italic', function () {
    expect(UnicodeStyler::italic('ABC'))->toBe('𝘈𝘉𝘊');
});

it('leaves digits unchanged in italic (no italic digits in Unicode)', function () {
    expect(UnicodeStyler::italic('a1b2'))->toBe('𝘢1𝘣2');
});

it('converts to sans-serif bold italic', function () {
    expect(UnicodeStyler::boldItalic('hi'))->toBe('𝙝𝙞');
});

it('converts to monospace', function () {
    expect(UnicodeStyler::monospace('php'))->toBe('𝚙𝚑𝚙');
});

it('converts digits to monospace', function () {
    expect(UnicodeStyler::monospace('php8'))->toBe('𝚙𝚑𝚙𝟾');
});

it('adds combining stroke overlay for strikethrough', function () {
    $combining = mb_chr(0x0336, 'UTF-8');
    expect(UnicodeStyler::strikethrough('ab'))->toBe('a'.$combining.'b'.$combining);
});

it('does not add combining stroke to spaces', function () {
    $combining = mb_chr(0x0336, 'UTF-8');
    expect(UnicodeStyler::strikethrough('a b'))->toBe('a'.$combining.' b'.$combining);
});

it('preserves accented characters unchanged', function () {
    expect(UnicodeStyler::bold('café'))->toBe('𝗰𝗮𝗳é');
});

it('preserves emoji unchanged', function () {
    expect(UnicodeStyler::bold('🚀 go'))->toBe('🚀 𝗴𝗼');
});

it('preserves non-Latin scripts unchanged', function () {
    expect(UnicodeStyler::bold('中文'))->toBe('中文');
});

it('returns empty string for empty input', function () {
    expect(UnicodeStyler::bold(''))->toBe('');
    expect(UnicodeStyler::italic(''))->toBe('');
    expect(UnicodeStyler::strikethrough(''))->toBe('');
});
