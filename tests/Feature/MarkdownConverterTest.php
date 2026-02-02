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

it('handles bullet lists', function () {
    $markdown = "- First item\n- Second item\n- Third item";
    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->toContain('First item');
    expect($result)->toContain('Second item');
});

it('handles multiple code blocks', function () {
    $markdown = "```php\necho 'Hello';\n```\n\n```javascript\nconsole.log('World');\n```";
    $result = MarkdownConverter::toDiscord($markdown);

    expect($result)->toContain('echo');
    expect($result)->toContain('console.log');
});

it('handles horizontal rules', function () {
    $markdown = "Content above\n---\nContent below";
    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->toContain('Content above');
    expect($result)->toContain('Content below');
});

it('handles empty markdown', function () {
    $result = MarkdownConverter::toTelegram('');

    expect($result)->toBe('');
});

it('handles markdown with only whitespace', function () {
    $result = MarkdownConverter::toWhatsApp("   \n\n   ");

    expect($result)->toBe('');
});

it('handles code block without language', function () {
    $markdown = "```\nconsole.log('no language');\n```";
    $result = MarkdownConverter::toDiscord($markdown);

    expect($result)->toContain('```');
    expect($result)->toContain("console.log('no language')");
});

it('handles task lists', function () {
    $markdown = "- [x] Completed task\n- [ ] Pending task";
    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->toContain('Completed task');
    expect($result)->toContain('Pending task');
});

it('handles nested lists', function () {
    $markdown = "- Parent item\n  - Child item\n  - Another child";
    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->toContain('Parent item');
    expect($result)->toContain('Child item');
});

it('handles multiple paragraphs', function () {
    $markdown = "First paragraph.\n\nSecond paragraph.\n\nThird paragraph.";
    $result = MarkdownConverter::toWhatsApp($markdown);

    expect($result)->toContain('First paragraph');
    expect($result)->toContain('Second paragraph');
    expect($result)->toContain('Third paragraph');
});

it('handles images', function () {
    $markdown = '![Alt text](https://example.com/image.png)';
    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->toContain('https://example.com/image.png');
});

it('handles escaped characters', function () {
    $markdown = '\\*not bold\\* and \\_not italic\\_';
    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->not->toContain('<b>');
});

it('handles inline HTML', function () {
    $markdown = '<p>HTML content</p>';
    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->toContain('HTML content');
});

it('handles combined bold and code', function () {
    $markdown = '**`code in bold`**';
    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->toContain('<b>');
    expect($result)->toContain('<code>');
});

it('handles long words without spaces', function () {
    $markdown = str_repeat('a', 500);
    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->toContain(str_repeat('a', 500));
});

it('handles special characters in links', function () {
    $markdown = '[Link with & and < >](https://example.com?param=1&other=2)';
    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->toContain('https://example.com');
});

it('handles consecutive formatting', function () {
    $markdown = '**bold**_italic_`code`';
    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->toContain('<b>');
    expect($result)->toContain('<i>');
    expect($result)->toContain('<code>');
});

it('handles multiline code blocks', function () {
    $markdown = "```python
def hello():
    print('World')
    return True
```";
    $result = MarkdownConverter::toDiscord($markdown);

    expect($result)->toContain('def hello():');
    expect($result)->toContain('print');
});

it('handles deeply nested formatting', function () {
    $markdown = '***bold and italic***';
    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->toContain('<b>');
    expect($result)->toContain('<i>');
});

it('handles tables with empty cells', function () {
    $markdown = "| Name | Value |\n|------|-------|\n| A | |\n| B | test |";
    $result = MarkdownConverter::toWhatsApp($markdown);

    expect($result)->toContain('Name:');
    expect($result)->toContain('A');
});

it('handles markdown with unicode numbers', function () {
    $markdown = '١٢٣٤٥ (Arabic) １２３ (Fullwidth)';
    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->toContain('١٢٣٤٥');
    expect($result)->toContain('１２３');
});
