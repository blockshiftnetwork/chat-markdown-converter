<?php

namespace Blockshift\ChatMarkdown;

use Blockshift\ChatMarkdown\Renderers\Contracts\RendererContract;
use Blockshift\ChatMarkdown\Renderers\DiscordRenderer;
use Blockshift\ChatMarkdown\Renderers\SlackRenderer;
use Blockshift\ChatMarkdown\Renderers\TelegramRenderer;
use Blockshift\ChatMarkdown\Renderers\WhatsAppRenderer;

final class MarkdownConverter
{
    private ?Parser $parser = null;

    private ?RendererContract $renderer = null;

    private array $options = [];

    private ?int $maxLength = null;

    public static function parse(string $markdown): self
    {
        return new self($markdown);
    }

    public static function toTelegram(string $markdown, ?int $maxLength = null): string|array
    {
        return self::parse($markdown)->using(new TelegramRenderer)->render($maxLength);
    }

    public static function toWhatsApp(string $markdown, ?int $maxLength = null): string|array
    {
        return self::parse($markdown)->using(new WhatsAppRenderer)->render($maxLength);
    }

    public static function toDiscord(string $markdown, ?int $maxLength = null): string|array
    {
        return self::parse($markdown)->using(new DiscordRenderer)->render($maxLength);
    }

    public static function toSlack(string $markdown, ?int $maxLength = null): string|array
    {
        return self::parse($markdown)->using(new SlackRenderer)->render($maxLength);
    }

    private function __construct(
        private readonly string $markdown
    ) {}

    public function withOptions(array $options): self
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    public function using(RendererContract $renderer): self
    {
        $this->renderer = $renderer;

        return $this;
    }

    public function render(?int $maxLength = null): string|array
    {
        $ir = $this->getParser()->parse($this->markdown);
        $renderer = $this->getRenderer();

        return $renderer->render($ir, $maxLength ?? $this->maxLength);
    }

    private function getParser(): Parser
    {
        if ($this->parser === null) {
            $this->parser = Parser::new()->withOptions($this->options);
        }

        return $this->parser;
    }

    private function getRenderer(): RendererContract
    {
        if ($this->renderer === null) {
            $this->renderer = new TelegramRenderer;
        }

        return $this->renderer;
    }
}
