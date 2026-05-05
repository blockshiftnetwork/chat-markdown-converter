<?php

use Blockshift\ChatMarkdown\MarkdownConverter;
use Blockshift\ChatMarkdown\Renderers\InstagramRenderer;
use Blockshift\ChatMarkdown\Support\IntermediateRepresentation;
use Blockshift\ChatMarkdown\Support\UnicodeStyler;

it('renders paragraphs as plain text', function () {
    $renderer = new InstagramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => 'Hello world']);

    expect($renderer->render($ir))->toBe('Hello world');
});

it('renders bold text using sans-serif bold Unicode', function () {
    $renderer = new InstagramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '**Bold** text']);

    $result = $renderer->render($ir);

    expect($result)->toContain(UnicodeStyler::bold('Bold'));
    expect($result)->not->toContain('**');
});

it('renders italic text using sans-serif italic Unicode', function () {
    $renderer = new InstagramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '*italic* text']);

    $result = $renderer->render($ir);

    expect($result)->toContain(UnicodeStyler::italic('italic'));
});

it('renders bold-italic marker using sans-serif bold-italic Unicode', function () {
    $renderer = new InstagramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '__BOLDITALIC__nested__BOLDITALIC__']);

    $result = $renderer->render($ir);

    expect($result)->toContain(UnicodeStyler::boldItalic('nested'));
    expect($result)->not->toContain('__BOLDITALIC__');
});

it('renders strikethrough with combining stroke overlay', function () {
    $renderer = new InstagramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '~~strike~~ text']);

    $result = $renderer->render($ir);
    $combining = mb_chr(0x0336, 'UTF-8');

    expect($result)->toContain('s'.$combining.'t'.$combining.'r'.$combining);
    expect($result)->not->toContain('~~');
});

it('renders inline code as monospace Unicode', function () {
    $renderer = new InstagramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '`code` text']);

    $result = $renderer->render($ir);

    expect($result)->toContain(UnicodeStyler::monospace('code'));
    expect($result)->not->toContain('`');
});

it('renders code blocks wrapped in heavy horizontal rules with monospace body', function () {
    $renderer = new InstagramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('code', ['content' => 'echo "test";', 'lang' => 'php']);

    $result = $renderer->render($ir);

    expect($result)->toContain('━━━━━━━━━━━━━━━━');
    expect($result)->toContain(UnicodeStyler::monospace('echo'));
    expect($result)->not->toContain('```');
});

it('renders links as text colon url', function () {
    $renderer = new InstagramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => 'Site (https://example.com)']);

    $result = $renderer->render($ir);

    expect($result)->toContain('Site: https://example.com');
});

it('renders parser-emitted image as plain link without exclamation mark', function () {
    $renderer = new InstagramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => 'Alt (https://example.com/img.png)']);

    $result = $renderer->render($ir);

    expect($result)->toContain('Alt: https://example.com/img.png');
    expect($result)->not->toContain('!');
});

it('preserves natural exclamation marks in prose', function () {
    $result = MarkdownConverter::toInstagram('Hello world!');

    expect($result)->toContain('Hello world!');
});

it('renders headers as sans-serif bold regardless of level', function () {
    $renderer = new InstagramRenderer;
    $ir = IntermediateRepresentation::empty()
        ->addBlock('header', ['content' => 'Title', 'level' => 1])
        ->addBlock('header', ['content' => 'Sub', 'level' => 3]);

    $result = $renderer->render($ir);

    expect($result)->toContain(UnicodeStyler::bold('Title'));
    expect($result)->toContain(UnicodeStyler::bold('Sub'));
});

it('converts tables to bullet list with bold headers', function () {
    $renderer = new InstagramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('table', [
        'headers' => ['Name', 'Value'],
        'rows' => [['Test', '123']],
        'alignments' => ['left', 'left'],
    ]);

    $result = $renderer->render($ir);

    expect($result)->toContain('• '.UnicodeStyler::bold('Name').': Test');
    expect($result)->toContain('• '.UnicodeStyler::bold('Value').': 123');
});

it('renders blockquote with typographic quotation marks', function () {
    $renderer = new InstagramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('blockquote', ['content' => 'A quote']);

    $result = $renderer->render($ir);

    expect($result)->toContain('❝ A quote ❞');
});

it('renders horizontal rule as heavy box-drawing line', function () {
    $renderer = new InstagramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('horizontal_rule', []);

    expect($renderer->render($ir))->toBe('━━━━━━━━━━━━━━━━');
});

it('converts completed task lists to checkmark emoji', function () {
    $renderer = new InstagramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '- [x] Done']);

    $result = $renderer->render($ir);

    expect($result)->toContain('✅');
    expect($result)->toContain('Done');
    expect($result)->not->toContain('[x]');
});

it('converts pending task lists to white square emoji', function () {
    $renderer = new InstagramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '- [ ] Todo']);

    $result = $renderer->render($ir);

    expect($result)->toContain('⬜');
    expect($result)->toContain('Todo');
    expect($result)->not->toContain('[ ]');
});

it('renders highlight marker as bold Unicode', function () {
    $renderer = new InstagramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '__HIGHLIGHT__important__HIGHLIGHT__ text']);

    $result = $renderer->render($ir);

    expect($result)->toContain(UnicodeStyler::bold('important'));
    expect($result)->not->toContain('__HIGHLIGHT__');
});

it('handles mixed formatting in single paragraph', function () {
    $renderer = new InstagramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '**bold** *italic* `code` plain']);

    $result = $renderer->render($ir);

    expect($result)->toContain(UnicodeStyler::bold('bold'));
    expect($result)->toContain(UnicodeStyler::italic('italic'));
    expect($result)->toContain(UnicodeStyler::monospace('code'));
    expect($result)->toContain('plain');
});

it('preserves emoji and non-Latin characters in paragraphs', function () {
    $renderer = new InstagramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => '🚀 café 中文 日本語']);

    $result = $renderer->render($ir);

    expect($result)->toContain('🚀');
    expect($result)->toContain('café');
    expect($result)->toContain('中文');
    expect($result)->toContain('日本語');
});

it('renders multiple paragraphs separated by blank lines', function () {
    $renderer = new InstagramRenderer;
    $ir = IntermediateRepresentation::empty()
        ->addBlock('paragraph', ['content' => 'First'])
        ->addBlock('paragraph', ['content' => 'Second']);

    $result = $renderer->render($ir);

    expect($result)->toContain('First');
    expect($result)->toContain('Second');
    expect($result)->toContain("\n\n");
});

it('replaces dash bullet markers with bullet character', function () {
    $renderer = new InstagramRenderer;
    $ir = IntermediateRepresentation::empty()->addBlock('paragraph', ['content' => "- one\n- two"]);

    $result = $renderer->render($ir);

    expect($result)->toContain('• one');
    expect($result)->toContain('• two');
});

it('exposes the toInstagram facade returning a string', function () {
    $result = MarkdownConverter::toInstagram('**Bold** message');

    expect($result)->toBeString();
    expect($result)->toContain(UnicodeStyler::bold('Bold'));
});

it('returns array when chunking exceeds the caption limit', function () {
    $longText = str_repeat('Lorem ipsum dolor sit amet. ', 100);

    $result = MarkdownConverter::toInstagram($longText, 200);

    expect($result)->toBeArray();
    expect(count($result))->toBeGreaterThan(1);
});
