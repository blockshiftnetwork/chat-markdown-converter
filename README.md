# Chat Markdown Converter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/blockshiftnetwork/chat-markdown-converter.svg?style=flat-square)](https://packagist.org/packages/blockshiftnetwork/chat-markdown-converter)
[![Tests](https://img.shields.io/github/actions/workflow/status/blockshiftnetwork/chat-markdown-converter/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/blockshiftnetwork/chat-markdown-converter/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/blockshiftnetwork/chat-markdown-converter.svg?style=flat-square)](https://packagist.org/packages/blockshiftnetwork/chat-markdown-converter)

Convert AI-generated Markdown to WhatsApp, Telegram, Discord and Slack compatible formats using an Intermediate Representation (IR). Perfect for converting LLM responses to chat-friendly formats.

## Features

- ✨ **Elegant Fluent API** - Simple, chainable method calls
- 🏗️ **Clean Architecture** - Intermediate Representation pattern for extensibility
- 🧪 **Comprehensive Tests** - 39 passing tests with Pest
- 🔌 **Platform-Specific Rendering** - Optimized output for each chat platform
- ✂️ **Smart Message Chunking** - Automatic text splitting at safe breakpoints
- 📦 **Zero Dependencies** - Lightweight, no external dependencies

## Installation

You can install the package via composer:

```bash
composer require blockshiftnetwork/chat-markdown-converter
```

## Usage

### Quick Conversion

```php
use Blockshift\ChatMarkdown\MarkdownConverter;

// Telegram (HTML format)
$telegram = MarkdownConverter::toTelegram($markdown);

// WhatsApp (Markdown format)
$whatsapp = MarkdownConverter::toWhatsApp($markdown);

// Discord (Markdown format)
$discord = MarkdownConverter::toDiscord($markdown);

// Slack (mrkdwn format)
$slack = MarkdownConverter::toSlack($markdown);
```

### Fluent API

```php
use Blockshift\ChatMarkdown\MarkdownConverter;
use Blockshift\ChatMarkdown\Renderers\TelegramRenderer;

$result = MarkdownConverter::parse($markdown)
    ->withOptions([
        'table_mode' => 'bullets',
        'parse_tables' => true,
    ])
    ->using(new TelegramRenderer)
    ->render();
```

### With Message Chunking

```php
use Blockshift\ChatMarkdown\MarkdownConverter;

$longText = str_repeat('This is a long message. ', 500);

$chunks = MarkdownConverter::toTelegram($longText, maxLength: 4096);
// Returns array of chunks, each under 4096 characters
```

### Custom Renderer

```php
use Blockshift\ChatMarkdown\MarkdownConverter;
use Blockshift\ChatMarkdown\Renderers\AbstractRenderer;
use Blockshift\ChatMarkdown\Support\IntermediateRepresentation;

class CustomRenderer extends AbstractRenderer
{
    protected function renderBlock(array $block): string
    {
        return match ($block['type']) {
            'paragraph' => 'PARA: '.$block['content'],
            'code' => 'CODE: '.$block['content'],
            default => '',
        };
    }
}

$result = MarkdownConverter::parse($markdown)
    ->using(new CustomRenderer)
    ->render();
```

### Parser Options

```php
MarkdownConverter::parse($markdown)->withOptions([
    'table_mode' => 'bullets',  // 'bullets' (default) or 'off'
    'parse_tables' => true,
    'parse_code_blocks' => true,
    'parse_links' => true,
    'parse_styles' => true,
]);
```

## Supported Features

### Currently Supported ✅

- **Text Formatting**: Bold, italic, strikethrough, inline code, highlight
- **Headers**: Markdown headings (# ## ###)
- **Code Blocks**: Code blocks with language preservation
- **Task Lists**: Checkbox support with emoji conversion (WhatsApp)
- **Links**: Markdown link formatting
- **Images**: Image formatting
- **Lists**: Numbered and bullet lists
- **Blockquotes**: Quote blocks
- **Tables**: Auto-converted to bullet lists for non-table platforms
- **Message Chunking**: Smart text splitting with safe breakpoints
- **Unicode Support**: Full UTF-8 support including emojis
- **Multiple Platforms**: Telegram, WhatsApp, Discord, Slack

### Platform-Specific Features

#### Telegram (HTML Mode)

- Bold: `<b>text</b>`
- Italic: `<i>text</i>`
- Strikethrough: `<s>text</s>`
- Highlight: `<b>text</b>`
- Inline Code: `<code>text</code>`
- Code Blocks: `<pre>code</pre>`
- Headers: `<b>text</b>` (bold)
- Links: `<a href="url">text</a>`
- Images: `<a href="url">text</a>` (without !)
- Task Lists: `- [x]` and `- [ ]` (native support)

#### WhatsApp

- Bold: `*text*` (single asterisk)
- Italic: `_text_` (underscore)
- Strikethrough: `~text~` (single tilde)
- Highlight: `*text*` (bold)
- Inline Code: `` `text` `` (backticks)
- Code Blocks: Triple backticks
- Headers: `*text*` (bold, replaces # syntax)
- Links: `text: url` (colon separator)
- Images: `text: url` (without !)
- Task Lists: `✅ task` (completed) and `⬜ task` (pending) with emojis

#### Discord

- Bold: `**text**`
- Italic: `*text*`
- Strikethrough: `~~text~~`
- Highlight: `**text**` (bold)
- Inline Code: `` `text` `` (backticks)
- Code Blocks: Triple backticks with language
- Headers: `**text**` (bold, replaces # syntax)
- Links: `[text](url)` (markdown format)
- Images: `[text](url)` (without !)
- Task Lists: `- [x]` and `- [ ]` (native support)

#### Slack

- Bold: `*text*` (single asterisk)
- Strikethrough: `~text~` (single tilde)
- Highlight: `*text*` (bold)
- Inline Code: `` `text` `` (backticks)
- Code Blocks: Triple backticks with language
- Headers: `*text*` (bold, replaces # syntax)
- Links: `<url|text>` (Slack format)
- Images: `<url|text>` (without !)
- Task Lists: `- [x]` and `- [ ]` (native support)

### Roadmap 🚧

See [TODO.md](TODO.md) for a comprehensive list of planned features including:

**Completed ✅**

- Platform-specific link formatting
- Table to bullet conversion
- Headers support
- Highlight syntax
- Image formatting improvements
- Nested/complex formatting

**Planned 📋**

- Telegram MarkdownV2 support
- Additional platforms (Teams, Mattermost, Signal)

## Testing

```bash
composer test
```

```bash
composer test-coverage
```

**Current Test Status**: 168 passed, 1 skipped

## Architecture

The package uses an Intermediate Representation (IR) pattern:

```
Markdown → Parser → IR → Renderer → Platform-Specific Format
```

### Components

- **Parser**: Converts Markdown to IR
- **HeaderParser**: Detects and parses markdown headers (# ## ###)
- **Parsers**: Specialized parsers for code blocks, tables, links, styles, blockquotes, horizontal rules
- **Renderers**: Platform-specific renderers (Telegram, WhatsApp, Discord, Slack)
- **Support**: IR, TextChunker, Utf16Helper

## Contributing

Contributions are welcome! Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

### Development Setup

```bash
# Install dependencies
composer install

# Run tests
composer test

# Format code
composer format
```

## Credits

- [Blockshift Network](https://github.com/blockshiftnetwork)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
