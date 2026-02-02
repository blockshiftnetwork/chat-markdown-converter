<?php

use Blockshift\ChatMarkdown\MarkdownConverter;

it('converts headers to appropriate format for each platform', function () {
    $markdown = "# Executive Summary\n\n## Key Points";

    $telegram = MarkdownConverter::toTelegram($markdown);
    $whatsapp = MarkdownConverter::toWhatsApp($markdown);
    $discord = MarkdownConverter::toDiscord($markdown);
    $slack = MarkdownConverter::toSlack($markdown);

    expect($telegram)->toContain('Executive Summary');
    expect($whatsapp)->toContain('Executive Summary');
    expect($discord)->toContain('Executive Summary');
    expect($slack)->toContain('Executive Summary');
});

it('handles bold text correctly across platforms', function () {
    $markdown = '**Q4 2025** showed **23%** growth';

    $telegram = MarkdownConverter::toTelegram($markdown);
    $whatsapp = MarkdownConverter::toWhatsApp($markdown);
    $discord = MarkdownConverter::toDiscord($markdown);
    $slack = MarkdownConverter::toSlack($markdown);

    expect($telegram)->toContain('<b>Q4 2025</b>');
    expect($whatsapp)->toContain('*Q4 2025*');
    expect($discord)->toContain('**Q4 2025**');
    expect($slack)->toContain('*Q4 2025*');
});

it('handles italic text correctly across platforms', function () {
    $markdown = '*23% YoY* and *quarterly growth*';

    $telegram = MarkdownConverter::toTelegram($markdown);
    $whatsapp = MarkdownConverter::toWhatsApp($markdown);
    $discord = MarkdownConverter::toDiscord($markdown);

    expect($telegram)->toContain('<i>23% YoY</i>');
    expect($whatsapp)->toContain('_23% YoY_');
    expect($discord)->toContain('*23% YoY*');
});

it('handles strikethrough text correctly across platforms', function () {
    $markdown = '~~27% margin~~ was ~~incorrect~~';

    $telegram = MarkdownConverter::toTelegram($markdown);
    $whatsapp = MarkdownConverter::toWhatsApp($markdown);
    $discord = MarkdownConverter::toDiscord($markdown);

    expect($telegram)->toContain('<s>27% margin</s>');
    expect($whatsapp)->toContain('27% margin');
    expect($discord)->toContain('~~27% margin~~');
});

it('handles inline code correctly across platforms', function () {
    $markdown = 'Active customers: `1,247` and `2,500`';

    $telegram = MarkdownConverter::toTelegram($markdown);
    $whatsapp = MarkdownConverter::toWhatsApp($markdown);
    $discord = MarkdownConverter::toDiscord($markdown);

    expect($telegram)->toContain('<code>1,247</code>');
    expect($whatsapp)->toContain('`1,247`');
    expect($discord)->toContain('`1,247`');
});

it('handles blockquotes correctly across platforms', function () {
    $markdown = '> "Growth was driven by geographic expansion" - CFO';

    $telegram = MarkdownConverter::toTelegram($markdown);
    $whatsapp = MarkdownConverter::toWhatsApp($markdown);
    $discord = MarkdownConverter::toDiscord($markdown);

    expect($telegram)->toContain('Growth was driven by geographic expansion');
    expect($whatsapp)->toContain('Growth was driven by geographic expansion');
    expect($discord)->toContain('Growth was driven by geographic expansion');
});

it('handles links correctly across platforms', function () {
    $markdown = 'See [our dashboard](https://example.com/q4-2025) for more details';

    $telegram = MarkdownConverter::toTelegram($markdown);
    $whatsapp = MarkdownConverter::toWhatsApp($markdown);
    $discord = MarkdownConverter::toDiscord($markdown);
    $slack = MarkdownConverter::toSlack($markdown);

    expect($telegram)->toContain('<a href="https://example.com/q4-2025">See our dashboard</a>');
    expect($whatsapp)->toContain('our dashboard: https://example.com/q4-2025');
    expect($discord)->toContain('[See our dashboard](https://example.com/q4-2025)');
    expect($slack)->toContain('<https://example.com/q4-2025|See our dashboard>');
});

it('converts tables to bullet points for non-table platforms', function () {
    $markdown = '| Metric | Q3 | Q4 | Change |
|---------|----|----|--------|
| Revenue | 36.7M | 45.2M | +23% |
| EBITDA  | 9.5M  | 12.1M | +27% |';

    $whatsapp = MarkdownConverter::toWhatsApp($markdown);
    $telegram = MarkdownConverter::toTelegram($markdown);

    expect($whatsapp)->toContain('Metric:');
    expect($whatsapp)->toContain('Revenue');
    expect($telegram)->toContain('Metric:');
    expect($telegram)->toContain('Revenue');
});

it('handles numbered lists correctly', function () {
    $markdown = "1. Expand to LATAM\n2. Hire 15 people in Q1\n3. Launch secret product in March";

    $telegram = MarkdownConverter::toTelegram($markdown);
    $whatsapp = MarkdownConverter::toWhatsApp($markdown);

    expect($telegram)->toContain('1.');
    expect($telegram)->toContain('Expand to LATAM');

    expect($whatsapp)->toContain('1.');
    expect($whatsapp)->toContain('Expand to LATAM');
});

it('handles code blocks with language specification', function () {
    $markdown = '```php
function calculateGrowth($current, $previous) {
    return (($current - $previous) / $previous) * 100;
}
```';

    $telegram = MarkdownConverter::toTelegram($markdown);
    $whatsapp = MarkdownConverter::toWhatsApp($markdown);
    $discord = MarkdownConverter::toDiscord($markdown);

    expect($telegram)->toContain('<pre>');
    expect($telegram)->toContain('function calculateGrowth');

    expect($whatsapp)->toContain('```');
    expect($whatsapp)->toContain('function calculateGrowth');

    expect($discord)->toContain('```');
    expect($discord)->toContain('function calculateGrowth');
});

it('handles complex real-world scenario', function () {
    $markdown = '# Executive Summary

Q4 2025 showed **23%** growth in revenue. Key findings:

- Revenue: $45.2M (*+23% YoY*)
- EBITDA: $12.1M (~27% margin~)
- Active customers: `1,247` (+15%)

> "Growth was driven by geographic expansion" - CFO

More details in [our dashboard](https://example.com/q4-2025).

## Next Steps

1. Expand to LATAM
2. Hire ~~10~~ **15 people** in Q1
3. Launch secret product in March

```php
function calculateGrowth($current, $previous) {
    return (($current - $previous) / $previous) * 100;
}
```

| Metric | Q3 | Q4 | Change |
|---------|----|----|--------|
| Revenue | 36.7M | 45.2M | +23% |
| EBITDA  | 9.5M  | 12.1M | +27% |';

    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->toContain('Executive Summary');
    expect($result)->toContain('<b>23%</b>');
    expect($result)->toContain('Revenue:');
    expect($result)->toContain('Growth was driven by geographic expansion');
    expect($result)->toContain('https://example.com/q4-2025');
    expect($result)->toContain('Next Steps');
    expect($result)->toContain('Expand to LATAM');
    expect($result)->toContain('function calculateGrowth');
});

it('chunks long messages correctly', function () {
    $markdown = str_repeat('This is a very long message that needs to be split into chunks. ', 200);

    $telegram = MarkdownConverter::toTelegram($markdown, maxLength: 4096);

    expect(is_array($telegram))->toBeTrue();
    expect(count($telegram))->toBeGreaterThan(1);
});

it('handles emojis correctly', function () {
    $markdown = '🚀 Rocket launch! 🔥 Hot! ✅ Done!';

    $telegram = MarkdownConverter::toTelegram($markdown);
    $whatsapp = MarkdownConverter::toWhatsApp($markdown);
    $discord = MarkdownConverter::toDiscord($markdown);

    expect($telegram)->toContain('🚀');
    expect($whatsapp)->toContain('🚀');
    expect($discord)->toContain('🚀');
});

it('handles unicode characters correctly', function () {
    $markdown = 'Special characters: café, niño, 中文, 日本語';

    $telegram = MarkdownConverter::toTelegram($markdown);
    $whatsapp = MarkdownConverter::toWhatsApp($markdown);
    $discord = MarkdownConverter::toDiscord($markdown);

    expect($telegram)->toContain('café');
    expect($telegram)->toContain('niño');
    expect($telegram)->toContain('中文');

    expect($whatsapp)->toContain('café');
    expect($whatsapp)->toContain('niño');
    expect($whatsapp)->toContain('中文');
});

it('handles links with special characters', function () {
    $markdown = '[Issue #123: Fix [bug]](https://example.com/issue-123)';

    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->toContain('Issue #123');
    expect($result)->toContain('Fix [bug]');
    expect($result)->toContain('https://example.com/issue-123');
});

it('handles mixed formatting combinations', function () {
    $markdown = '**_bold and italic_** text';

    $telegram = MarkdownConverter::toTelegram($markdown);
    $whatsapp = MarkdownConverter::toWhatsApp($markdown);

    expect($telegram)->toContain('<b>');
    expect($telegram)->toContain('<i>');
    expect($whatsapp)->toContain('*');
});

it('handles multilisted content (bullets + numbered)', function () {
    $markdown = "- First item\n- Second item\n\n1. First numbered\n2. Second numbered";

    $result = MarkdownConverter::toWhatsApp($markdown);

    expect($result)->toContain('First item');
    expect($result)->toContain('First numbered');
});

it('handles all heading levels', function () {
    $markdown = "# H1\n## H2\n### H3\n#### H4\n##### H5\n###### H6";

    $telegram = MarkdownConverter::toTelegram($markdown);
    $whatsapp = MarkdownConverter::toWhatsApp($markdown);
    $discord = MarkdownConverter::toDiscord($markdown);

    expect($telegram)->toContain('H1');
    expect($telegram)->toContain('H2');
    expect($telegram)->toContain('H3');
    expect($telegram)->toContain('H4');
    expect($telegram)->toContain('H5');
    expect($telegram)->toContain('H6');

    expect($whatsapp)->toContain('H1');
    expect($discord)->toContain('H1');
});

it('handles nested formatting in different platforms', function () {
    $markdown = '**bold with _italic_ inside**';

    $telegram = MarkdownConverter::toTelegram($markdown);
    $whatsapp = MarkdownConverter::toWhatsApp($markdown);
    $discord = MarkdownConverter::toDiscord($markdown);

    expect($telegram)->toContain('<b>');
    expect($telegram)->toContain('<i>');

    expect($whatsapp)->toContain('*');
    expect($discord)->toContain('**');
});

it('handles mixed markdown elements', function () {
    $markdown = "# Title\n\n**Bold** text with `code` and *italic*.\n\n> A quote\n\n- List item\n\n[Link](https://example.com)";

    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->toContain('Title');
    expect($result)->toContain('<b>Bold</b>');
    expect($result)->toContain('<code>code</code>');
    expect($result)->toContain('<i>italic</i>');
    expect($result)->toContain('A quote');
    expect($result)->toContain('List item');
    expect($result)->toContain('https://example.com');
});

it('handles inline HTML tags correctly', function () {
    $markdown = '<strong>HTML strong</strong> and <em>HTML em</em>';

    $telegram = MarkdownConverter::toTelegram($markdown);
    $whatsapp = MarkdownConverter::toWhatsApp($markdown);

    expect($telegram)->toContain('HTML strong');
    expect($telegram)->toContain('HTML em');
    expect($whatsapp)->toContain('HTML strong');
});

it('handles multiple consecutive blockquotes', function () {
    $markdown = "> First quote\n\n> Second quote\n\n> Third quote";

    $result = MarkdownConverter::toDiscord($markdown);

    expect($result)->toContain('First quote');
    expect($result)->toContain('Second quote');
    expect($result)->toContain('Third quote');
});

it('handles links with underscores in text', function () {
    $markdown = '[link with underscores](https://example.com)';

    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->toContain('link with underscores');
    expect($result)->toContain('https://example.com');
});

it('handles code with special characters', function () {
    $markdown = '`<html> & <body> "quotes"`';

    $result = MarkdownConverter::toDiscord($markdown);

    expect($result)->toContain('<html>');
    expect($result)->toContain('&');
    expect($result)->toContain('"quotes"');
});

it('handles tables with special characters', function () {
    $markdown = "| Name | Value |\n|------|-------|\n| Test & More | 100% |\n| Special < > | \$50 |";

    $result = MarkdownConverter::toWhatsApp($markdown);

    expect($result)->toContain('Name:');
    expect($result)->toContain('Test & More');
    expect($result)->toContain('100%');
    expect($result)->toContain('Special');
});

it('handles malformed markdown gracefully', function () {
    $markdown = "**Unclosed bold\n_Unclosed italic\n`Unclosed code";

    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->toContain('Unclosed bold');
    expect($result)->toContain('Unclosed italic');
    expect($result)->toContain('Unclosed code');
});

it('handles multiple links in same paragraph', function () {
    $markdown = 'Visit [link1](https://example1.com) and [link2](https://example2.com) for more info';

    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->toContain('https://example1.com');
    expect($result)->toContain('https://example2.com');
});

it('handles line breaks with double space', function () {
    $markdown = "Line 1  \nLine 2  \nLine 3";

    $result = MarkdownConverter::toWhatsApp($markdown);

    expect($result)->toContain('Line 1');
    expect($result)->toContain('Line 2');
    expect($result)->toContain('Line 3');
});

it('handles empty table rows', function () {
    $markdown = "| Header |\n|--------|\n| Value |";

    $result = MarkdownConverter::toWhatsApp($markdown);

    expect($result)->toContain('Header:');
    expect($result)->toContain('Value');
});

it('handles code blocks with special characters', function () {
    $markdown = "```php
\$var = 'value';
\$arr = [1, 2, 3];
echo \$var;
```";

    $result = MarkdownConverter::toDiscord($markdown);

    expect($result)->toContain('$var');
    expect($result)->toContain('echo');
});

it('handles mixed bullet and numbered lists', function () {
    $markdown = "- Bullet 1\n- Bullet 2\n\n1. Number 1\n2. Number 2\n\n- Bullet 3";

    $result = MarkdownConverter::toWhatsApp($markdown);

    expect($result)->toContain('Bullet 1');
    expect($result)->toContain('Number 1');
    expect($result)->toContain('Bullet 3');
});

it('handles whitespace preservation in code', function () {
    $markdown = "```\n    Indented line\nNot indented\n```";

    $result = MarkdownConverter::toDiscord($markdown);

    expect($result)->toContain('Indented line');
    expect($result)->toContain('Not indented');
});

it('handles escaped markdown characters', function () {
    $markdown = '\*\*Not Bold\*\* and \`Not Code\`';

    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->toContain('Not Bold');
    expect($result)->toContain('Not Code');
});

it('handles complex tables with multiple columns', function () {
    $markdown = "| Col1 | Col2 | Col3 | Col4 |\n|------|------|------|------|\n| A1 | B1 | C1 | D1 |\n| A2 | B2 | C2 | D2 |";

    $result = MarkdownConverter::toWhatsApp($markdown);

    expect($result)->toContain('Col1:');
    expect($result)->toContain('A1');
    expect($result)->toContain('B1');
    expect($result)->toContain('C1');
});

it('handles bold with underscores in text', function () {
    $markdown = '**text with underscores**';

    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->toContain('<b>text with underscores</b>');
});

it('handles multiple consecutive spaces', function () {
    $markdown = 'Word1    Word2     Word3';

    $result = MarkdownConverter::toWhatsApp($markdown);

    expect($result)->toContain('Word1');
    expect($result)->toContain('Word2');
    expect($result)->toContain('Word3');
});

it('handles links with unicode characters', function () {
    $markdown = '[中文链接](https://example.com/测试) and [日本語](https://example.com/テスト)';

    $result = MarkdownConverter::toTelegram($markdown);

    expect($result)->toContain('中文链接');
    expect($result)->toContain('日本語');
    expect($result)->toContain('https://example.com/测试');
    expect($result)->toContain('https://example.com/テスト');
});

it('handles images in different platforms', function () {
    $markdown = '![Image](https://example.com/img.png)';

    $telegram = MarkdownConverter::toTelegram($markdown);
    $whatsapp = MarkdownConverter::toWhatsApp($markdown);
    $discord = MarkdownConverter::toDiscord($markdown);

    expect($telegram)->toContain('https://example.com/img.png');
    expect($whatsapp)->toContain('https://example.com/img.png');
    expect($discord)->toContain('https://example.com/img.png');
});
