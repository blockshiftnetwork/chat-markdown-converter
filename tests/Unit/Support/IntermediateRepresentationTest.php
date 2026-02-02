<?php

use Blockshift\ChatMarkdown\Support\IntermediateRepresentation;

it('can create empty ir', function () {
    $ir = IntermediateRepresentation::empty();

    expect($ir->isEmpty())->toBeTrue();
    expect($ir->count())->toBe(0);
});

it('can add blocks to ir', function () {
    $ir = IntermediateRepresentation::empty();
    $ir = $ir->addBlock('paragraph', ['content' => 'Hello']);

    expect($ir->isEmpty())->toBeFalse();
    expect($ir->count())->toBe(1);
    expect($ir->getBlocks())->toHaveCount(1);
});

it('is immutable', function () {
    $ir = IntermediateRepresentation::empty();
    $ir2 = $ir->addBlock('paragraph', ['content' => 'Hello']);

    expect($ir->isEmpty())->toBeTrue();
    expect($ir2->isEmpty())->toBeFalse();
});
