<?php

namespace Blockshift\ChatMarkdown\Renderers\Contracts;

use Blockshift\ChatMarkdown\Support\IntermediateRepresentation;

interface RendererContract
{
    public function render(IntermediateRepresentation $ir, ?int $maxLength = null): string|array;
}
