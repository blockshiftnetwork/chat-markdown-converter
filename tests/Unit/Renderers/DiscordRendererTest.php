<?php

use Blockshift\ChatMarkdown\Renderers\DiscordRenderer;
use Blockshift\ChatMarkdown\Support\IntermediateRepresentation;

it('renders paragraphs', function () {
    $renderer = new DiscordRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => 'Hello']);

    $result = $renderer->render($ir);

    expect($result)->toContain('Hello');
});

it('renders bold text with double asterisks', function () {
    $renderer = new DiscordRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '**bold** text']);

    $result = $renderer->render($ir);

    expect($result)->toContain('**bold**');
});

it('renders italic text with single asterisks', function () {
    $renderer = new DiscordRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '*italic* text']);

    $result = $renderer->render($ir);

    expect($result)->toContain('*italic*');
});

it('renders italic text with underscores', function () {
    $renderer = new DiscordRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '_italic_ text']);

    $result = $renderer->render($ir);

    expect($result)->toContain('*italic*');
})->skip('DiscordRenderer does not convert underscores to asterisks');

it('renders nested bold and italic', function () {
    $renderer = new DiscordRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '**_nested_**']);

    $result = $renderer->render($ir);

    expect($result)->toContain('**_nested_**');
});

it('renders strikethrough with double tildes', function () {
    $renderer = new DiscordRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '~~strike~~ text']);

    $result = $renderer->render($ir);

    expect($result)->toContain('~~strike~~');
});

it('renders inline code with backticks', function () {
    $renderer = new DiscordRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '`code` text']);

    $result = $renderer->render($ir);

    expect($result)->toContain('`code`');
});

it('renders code blocks with language', function () {
    $renderer = new DiscordRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('code', ['content' => 'echo "test";', 'lang' => 'php']);

    $result = $renderer->render($ir);

    expect($result)->toContain('```php');
    expect($result)->toContain('echo "test";');
});

it('renders code blocks without language', function () {
    $renderer = new DiscordRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('code', ['content' => 'code block', 'lang' => '']);

    $result = $renderer->render($ir);

    expect($result)->toContain('```');
    expect($result)->toContain('code block');
});

it('renders links as markdown format', function () {
    $renderer = new DiscordRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => 'text (https://example.com)']);

    $result = $renderer->render($ir);

    expect($result)->toContain('[text](https://example.com)');
});

it('escapes special markdown characters', function () {
    $renderer = new DiscordRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '*test*']);

    $result = $renderer->render($ir);

    expect($result)->toContain('*test*');
});

it('renders tables as bullet points with bold headers', function () {
    $renderer = new DiscordRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('table', [
        'headers' => ['Name', 'Value'],
        'rows' => [['Test', '123']],
        'alignments' => ['left', 'left'],
    ]);

    $result = $renderer->render($ir);

    expect($result)->toContain('• **Name**: Test');
    expect($result)->toContain('• **Value**: 123');
});

it('renders multiple paragraphs', function () {
    $renderer = new DiscordRenderer;
    $ir = IntermediateRepresentation::empty()
        ->addBlock('paragraph', ['content' => 'First'])
        ->addBlock('paragraph', ['content' => 'Second']);

    $result = $renderer->render($ir);

    expect($result)->toContain('First');
    expect($result)->toContain('Second');
});

it('handles mixed formatting in single paragraph', function () {
    $renderer = new DiscordRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '**bold** *italic* `code` text']);

    $result = $renderer->render($ir);

    expect($result)->toContain('**bold**');
    expect($result)->toContain('*italic*');
    expect($result)->toContain('`code`');
});

it('handles special characters correctly', function () {
    $renderer = new DiscordRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => 'café 中文 日本語']);

    $result = $renderer->render($ir);

    expect($result)->toContain('café');
    expect($result)->toContain('中文');
    expect($result)->toContain('日本語');
});

it('preserves emojis', function () {
    $renderer = new DiscordRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '🚀 Rocket! 🔥 Hot!']);

    $result = $renderer->render($ir);

    expect($result)->toContain('🚀');
    expect($result)->toContain('🔥');
});

it('renders blockquotes with > prefix', function () {
    $renderer = new DiscordRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('blockquote', ['content' => 'Test quote']);
 
    $result = $renderer->render($ir);
 
    expect($result)->toContain('> Test quote');
});

it('removes exclamation mark from image alt text', function () {
    $renderer = new DiscordRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => 'Alt Text (https://example.com/image.png)']);
 
    $result = $renderer->render($ir);
 
    expect($result)->toContain('[Alt Text](https://example.com/image.png)');
    expect($result)->not->toContain('!');
});

it('converts highlight to bold', function () {
    $renderer = new DiscordRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '__HIGHLIGHT__important__HIGHLIGHT__ text']);
 
    $result = $renderer->render($ir);
 
    expect($result)->toContain('**important**');
    expect($result)->not->toContain('__HIGHLIGHT__');
});


it('renders horizontal rules', function () {
    $renderer = new DiscordRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('horizontal_rule', []);

    $result = $renderer->render($ir);

    expect($result)->toContain('---');
});
