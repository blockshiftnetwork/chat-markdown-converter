<?php

use Blockshift\ChatMarkdown\MarkdownConverter;

it('can convert markdown to telegram', function () {
    $markdown = '**Hello** world';
    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->toContain('<b>Hello</b>');
});

it('can convert markdown to whatsapp', function () {
    $markdown = '**Hello** world';
    $result = MarkdownConverter::toWhatsApp($markdown);

    expect($result)->toContain('*Hello*');
});

it('can convert markdown to discord', function () {
    $markdown = '**Hello** world';
    $result = MarkdownConverter::toDiscord($markdown);

    expect($result)->toContain('**Hello**');
});

it('can convert markdown to slack', function () {
    $markdown = '**Hello** world';
    $result = MarkdownConverter::toSlack($markdown);

    expect($result)->toContain('*Hello*');
});

it('supports fluent api', function () {
    $markdown = '**Hello** world';
    $result = MarkdownConverter::parse($markdown)->using(new Blockshift\ChatMarkdown\Renderers\TelegramRenderer)->render();

    expect($result)->toContain('<b>Hello</b>');
});

it('supports options', function () {
    $markdown = "| Header |\n| --- |\n| Content |";
    $result = MarkdownConverter::parse($markdown)->using(new Blockshift\ChatMarkdown\Renderers\WhatsAppRenderer)->render();

    expect($result)->toContain('Header');
    expect($result)->toContain('Content');
});

it('chunks long messages', function () {
    $markdown = str_repeat('a', 5000);
    $result = MarkdownConverter::toTelegram($markdown, maxLength: 1000);

    expect(is_array($result))->toBeTrue();
    expect(count($result))->toBeGreaterThan(1);
});
