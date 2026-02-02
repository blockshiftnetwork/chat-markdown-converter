<?php

use Blockshift\ChatMarkdown\Renderers\WhatsAppRenderer;
use Blockshift\ChatMarkdown\Support\IntermediateRepresentation;

it('renders paragraphs', function () {
    $renderer = new WhatsAppRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => 'Hello']);

    $result = $renderer->render($ir);

    expect($result)->toContain('Hello');
});

it('renders bold text with asterisks', function () {
    $renderer = new WhatsAppRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '**bold** text']);

    $result = $renderer->render($ir);

    expect($result)->toContain('*bold*');
});

it('renders italic text with underscores', function () {
    $renderer = new WhatsAppRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '*italic* text']);

    $result = $renderer->render($ir);

    expect($result)->toContain('_italic_');
});

it('renders italic text with underscores directly', function () {
    $renderer = new WhatsAppRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '_italic_ text']);

    $result = $renderer->render($ir);

    expect($result)->toContain('_italic_');
});

it('renders nested bold and italic', function () {
    $renderer = new WhatsAppRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '__BOLDITALIC__nested__BOLDITALIC__']);

    $result = $renderer->render($ir);

    expect($result)->toContain('_*nested*_');
});

it('renders strikethrough with single tilde', function () {
    $renderer = new WhatsAppRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '~~strike~~ text']);

    $result = $renderer->render($ir);

    expect($result)->toContain('~strike~');
    expect($result)->not->toContain('~~');
});

it('renders inline code with backticks', function () {
    $renderer = new WhatsAppRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '`code` text']);

    $result = $renderer->render($ir);

    expect($result)->toContain('`code`');
});

it('renders code blocks', function () {
    $renderer = new WhatsAppRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('code', ['content' => 'echo "test";', 'lang' => 'php']);

    $result = $renderer->render($ir);

    expect($result)->toContain('```');
    expect($result)->toContain('echo "test";');
});

it('renders code blocks without language', function () {
    $renderer = new WhatsAppRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('code', ['content' => 'code block', 'lang' => '']);

    $result = $renderer->render($ir);

    expect($result)->toContain('```');
    expect($result)->toContain('code block');
});

it('renders links as plain text with URL', function () {
    $renderer = new WhatsAppRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => 'text (https://example.com)']);

    $result = $renderer->render($ir);

    expect($result)->toContain('text: https://example.com');
    expect($result)->not->toContain('[');
    expect($result)->not->toContain('<');
});

it('renders tables as bullet points', function () {
    $renderer = new WhatsAppRenderer;
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
    $renderer = new WhatsAppRenderer;
    $ir = IntermediateRepresentation::empty()
        ->addBlock('paragraph', ['content' => 'First'])
        ->addBlock('paragraph', ['content' => 'Second']);

    $result = $renderer->render($ir);

    expect($result)->toContain('First');
    expect($result)->toContain('Second');
});

it('handles mixed formatting in single paragraph', function () {
    $renderer = new WhatsAppRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '**bold** *italic* `code` text']);

    $result = $renderer->render($ir);

    expect($result)->toContain('*bold*');
    expect($result)->toContain('_italic_');
    expect($result)->toContain('`code`');
});

it('handles special characters correctly', function () {
    $renderer = new WhatsAppRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => 'café 中文 日本語']);

    $result = $renderer->render($ir);

    expect($result)->toContain('café');
    expect($result)->toContain('中文');
    expect($result)->toContain('日本語');
});

it('preserves emojis', function () {
    $renderer = new WhatsAppRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '🚀 Rocket! 🔥 Hot!']);

    $result = $renderer->render($ir);

    expect($result)->toContain('🚀');
    expect($result)->toContain('🔥');
});

it('renders blockquotes with emoji', function () {
    $renderer = new WhatsAppRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('blockquote', ['content' => 'Test quote']);

    $result = $renderer->render($ir);

    expect($result)->toContain('💬');
    expect($result)->toContain('Test quote');
});

it('renders horizontal rules', function () {
    $renderer = new WhatsAppRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('horizontal_rule', []);

    $result = $renderer->render($ir);

    expect($result)->toContain('---');
});

it('converts completed task lists to checkmark emoji', function () {
    $renderer = new WhatsAppRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '- [x] Completed task']);

    $result = $renderer->render($ir);

    expect($result)->toContain('✅');
    expect($result)->toContain('Completed task');
    expect($result)->not->toContain('[x]');
    expect($result)->not->toContain('- [x]');
});

it('converts pending task lists to white circle emoji', function () {
    $renderer = new WhatsAppRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '- [ ] Pending task']);

    $result = $renderer->render($ir);

    expect($result)->toContain('⬜');
    expect($result)->toContain('Pending task');
    expect($result)->not->toContain('[ ]');
    expect($result)->not->toContain('- [ ]');
});

it('removes exclamation mark from image alt text', function () {
    $renderer = new WhatsAppRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => 'Alt Text (https://example.com/image.png)']);

    $result = $renderer->render($ir);

    expect($result)->toContain('Alt Text: https://example.com/image.png');
    expect($result)->not->toContain('!');
});

it('converts highlight to bold', function () {
    $renderer = new WhatsAppRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '__HIGHLIGHT__important__HIGHLIGHT__ text']);

    $result = $renderer->render($ir);

    expect($result)->toContain('*important*');
    expect($result)->not->toContain('__HIGHLIGHT__');
});
