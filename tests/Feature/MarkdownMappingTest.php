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
    $markdown = "**Q4 2025** showed **23%** growth";

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
    $markdown = "*23% YoY* and *quarterly growth*";

    $telegram = MarkdownConverter::toTelegram($markdown);
    $whatsapp = MarkdownConverter::toWhatsApp($markdown);
    $discord = MarkdownConverter::toDiscord($markdown);

    expect($telegram)->toContain('<i>23% YoY</i>');
    expect($whatsapp)->toContain('_23% YoY_');
    expect($discord)->toContain('*23% YoY*');
});

it('handles strikethrough text correctly across platforms', function () {
    $markdown = "~~27% margin~~ was ~~incorrect~~";

    $telegram = MarkdownConverter::toTelegram($markdown);
    $whatsapp = MarkdownConverter::toWhatsApp($markdown);
    $discord = MarkdownConverter::toDiscord($markdown);

    expect($telegram)->toContain('<s>27% margin</s>');
    expect($whatsapp)->toContain('27% margin');
    expect($discord)->toContain('~~27% margin~~');
});

it('handles inline code correctly across platforms', function () {
    $markdown = "Active customers: `1,247` and `2,500`";

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
    expect($whatsapp)->toContain('our dashboard (https://example.com/q4-2025)');
    expect($discord)->toContain('[See our dashboard](https://example.com/q4-2025)');
    expect($slack)->toContain('<https://example.com/q4-2025|See our dashboard>');
});

it('converts tables to bullet points for non-table platforms', function () {
    $markdown = "| Metric | Q3 | Q4 | Change |
|---------|----|----|--------|
| Revenue | 36.7M | 45.2M | +23% |
| EBITDA  | 9.5M  | 12.1M | +27% |";

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
    $markdown = "```php
function calculateGrowth(\$current, \$previous) {
    return ((\$current - \$previous) / \$previous) * 100;
}
```";

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
    $markdown = "# Executive Summary

Q4 2025 showed **23%** growth in revenue. Key findings:

- Revenue: \$45.2M (*+23% YoY*)
- EBITDA: \$12.1M (~27% margin~)
- Active customers: `1,247` (+15%)

> \"Growth was driven by geographic expansion\" - CFO

More details in [our dashboard](https://example.com/q4-2025).

## Next Steps

1. Expand to LATAM
2. Hire ~~10~~ **15 people** in Q1
3. Launch secret product in March

```php
function calculateGrowth(\$current, \$previous) {
    return ((\$current - \$previous) / \$previous) * 100;
}
```

| Metric | Q3 | Q4 | Change |
|---------|----|----|--------|
| Revenue | 36.7M | 45.2M | +23% |
| EBITDA  | 9.5M  | 12.1M | +27% |";

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
    $markdown = "🚀 Rocket launch! 🔥 Hot! ✅ Done!";

    $telegram = MarkdownConverter::toTelegram($markdown);
    $whatsapp = MarkdownConverter::toWhatsApp($markdown);
    $discord = MarkdownConverter::toDiscord($markdown);

    expect($telegram)->toContain('🚀');
    expect($whatsapp)->toContain('🚀');
    expect($discord)->toContain('🚀');
});

it('handles unicode characters correctly', function () {
    $markdown = "Special characters: café, niño, 中文, 日本語";

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
