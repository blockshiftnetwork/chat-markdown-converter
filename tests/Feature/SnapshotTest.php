<?php

use Blockshift\ChatMarkdown\MarkdownConverter;

/**
 * Snapshot tests pin the rendered output of a representative LLM-generated
 * Markdown document for every supported platform. They serve as a safety net
 * for cross-cutting refactors: any unexpected change in any renderer will
 * surface here as a clear, byte-level diff. To deliberately update a snapshot,
 * regenerate it with:
 *
 *     php -r 'require "vendor/autoload.php"; \
 *         use Blockshift\ChatMarkdown\MarkdownConverter; \
 *         $md = file_get_contents("tests/Fixtures/llm-document.md"); \
 *         file_put_contents("tests/Fixtures/llm-document.<platform>.txt", \
 *             MarkdownConverter::to<Platform>($md));'
 */
function fixture(string $name): string
{
    return file_get_contents(__DIR__.'/../Fixtures/'.$name);
}

it('renders the LLM document snapshot for Telegram', function () {
    expect(MarkdownConverter::toTelegram(fixture('llm-document.md')))
        ->toBe(fixture('llm-document.telegram.txt'));
});

it('renders the LLM document snapshot for WhatsApp', function () {
    expect(MarkdownConverter::toWhatsApp(fixture('llm-document.md')))
        ->toBe(fixture('llm-document.whatsapp.txt'));
});

it('renders the LLM document snapshot for Discord', function () {
    expect(MarkdownConverter::toDiscord(fixture('llm-document.md')))
        ->toBe(fixture('llm-document.discord.txt'));
});

it('renders the LLM document snapshot for Slack', function () {
    expect(MarkdownConverter::toSlack(fixture('llm-document.md')))
        ->toBe(fixture('llm-document.slack.txt'));
});

it('renders the LLM document snapshot for Instagram', function () {
    expect(MarkdownConverter::toInstagram(fixture('llm-document.md')))
        ->toBe(fixture('llm-document.instagram.txt'));
});
