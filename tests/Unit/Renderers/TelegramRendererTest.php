<?php

use Blockshift\ChatMarkdown\Renderers\TelegramRenderer;
use Blockshift\ChatMarkdown\Support\IntermediateRepresentation;

it('renders paragraphs', function () {
    $renderer = new TelegramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => 'Hello']);

    $result = $renderer->render($ir);

    expect($result)->toContain('Hello');
});

it('renders bold text with HTML tags', function () {
    $renderer = new TelegramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '**bold** text']);

    $result = $renderer->render($ir);

    expect($result)->toContain('<b>bold</b>');
});

it('renders italic text with HTML tags', function () {
    $renderer = new TelegramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '*italic* text']);

    $result = $renderer->render($ir);

    expect($result)->toContain('<i>italic</i>');
});

it('renders italic text with underscores', function () {
    $renderer = new TelegramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '_italic_ text']);

    $result = $renderer->render($ir);

    expect($result)->toContain('<i>italic</i>');
});

it('renders nested bold and italic', function () {
    $renderer = new TelegramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '**_nested_**']);

    $result = $renderer->render($ir);

    expect($result)->toContain('<b>');
    expect($result)->toContain('<i>nested</i>');
});

it('renders strikethrough with HTML tags', function () {
    $renderer = new TelegramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '~~strike~~ text']);

    $result = $renderer->render($ir);

    expect($result)->toContain('<s>strike</s>');
});

it('renders inline code with HTML tags', function () {
    $renderer = new TelegramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '`code` text']);

    $result = $renderer->render($ir);

    expect($result)->toContain('<code>code</code>');
});

it('renders code blocks', function () {
    $renderer = new TelegramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('code', ['content' => 'echo "test";', 'lang' => 'php']);

    $result = $renderer->render($ir);

    expect($result)->toContain('<pre>');
    expect($result)->toContain('echo &quot;test&quot;;');
});

it('renders code blocks without language', function () {
    $renderer = new TelegramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('code', ['content' => 'code block', 'lang' => '']);

    $result = $renderer->render($ir);

    expect($result)->toContain('<pre>');
    expect($result)->toContain('code block');
});

it('renders links as HTML anchor tags', function () {
    $renderer = new TelegramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => 'text (https://example.com)']);

    $result = $renderer->render($ir);

    expect($result)->toContain('<a href="https://example.com">text</a>');
});

it('escapes HTML entities in text', function () {
    $renderer = new TelegramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '<script>alert("test")</script>']);

    $result = $renderer->render($ir);

    expect($result)->toContain('&lt;script&gt;');
    expect($result)->toContain('&quot;test&quot;');
});

it('renders tables as bullet points', function () {
    $renderer = new TelegramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('table', [
        'headers' => ['Name', 'Value'],
        'rows' => [['Test', '123']],
        'alignments' => ['left', 'left'],
    ]);

    $result = $renderer->render($ir);

    expect($result)->toContain('• Name: Test');
    expect($result)->toContain('• Value: 123');
});

it('renders multiple paragraphs', function () {
    $renderer = new TelegramRenderer;
    $ir = IntermediateRepresentation::empty()
        ->addBlock('paragraph', ['content' => 'First'])
        ->addBlock('paragraph', ['content' => 'Second']);

    $result = $renderer->render($ir);

    expect($result)->toContain('First');
    expect($result)->toContain('Second');
});

it('handles mixed formatting in single paragraph', function () {
    $renderer = new TelegramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '**bold** *italic* `code` text']);

    $result = $renderer->render($ir);

    expect($result)->toContain('<b>bold</b>');
    expect($result)->toContain('<i>italic</i>');
    expect($result)->toContain('<code>code</code>');
});

it('handles special characters correctly', function () {
    $renderer = new TelegramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => 'café 中文 日本語']);

    $result = $renderer->render($ir);

    expect($result)->toContain('café');
    expect($result)->toContain('中文');
    expect($result)->toContain('日本語');
});

it('renders blockquotes with emoji', function () {
    $renderer = new TelegramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('blockquote', ['content' => 'Test quote']);
 
    $result = $renderer->render($ir);
 
    expect($result)->toContain('💬');
    expect($result)->toContain('Test quote');
});

it('removes exclamation mark from image alt text', function () {
    $renderer = new TelegramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => 'Alt Text (https://example.com/image.png)']);
 
    $result = $renderer->render($ir);
 
    expect($result)->toContain('Alt Text');
    expect($result)->toContain('https://example.com/image.png');
    expect($result)->not->toContain('!');
});

it('converts highlight to bold', function () {
    $renderer = new TelegramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '__HIGHLIGHT__important__HIGHLIGHT__ text']);
 
    $result = $renderer->render($ir);
 
    expect($result)->toContain('<b>important</b>');
    expect($result)->not->toContain('__HIGHLIGHT__');
});


it('renders horizontal rules', function () {
    $renderer = new TelegramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('horizontal_rule', []);

    $result = $renderer->render($ir);

    expect($result)->toContain('---');
});
