<?php

namespace Blockshift\ChatMarkdown\Support;

final class UnicodeStyler
{
    private const SANS_BOLD_UPPER = 0x1D5D4;

    private const SANS_BOLD_LOWER = 0x1D5EE;

    private const SANS_BOLD_DIGIT = 0x1D7EC;

    private const SANS_ITALIC_UPPER = 0x1D608;

    private const SANS_ITALIC_LOWER = 0x1D622;

    private const SANS_BOLD_ITALIC_UPPER = 0x1D63C;

    private const SANS_BOLD_ITALIC_LOWER = 0x1D656;

    private const MONO_UPPER = 0x1D670;

    private const MONO_LOWER = 0x1D68A;

    private const MONO_DIGIT = 0x1D7F6;

    private const COMBINING_LONG_STROKE_OVERLAY = 0x0336;

    public static function bold(string $text): string
    {
        return self::transform($text, self::SANS_BOLD_UPPER, self::SANS_BOLD_LOWER, self::SANS_BOLD_DIGIT);
    }

    public static function italic(string $text): string
    {
        return self::transform($text, self::SANS_ITALIC_UPPER, self::SANS_ITALIC_LOWER, null);
    }

    public static function boldItalic(string $text): string
    {
        return self::transform($text, self::SANS_BOLD_ITALIC_UPPER, self::SANS_BOLD_ITALIC_LOWER, null);
    }

    public static function monospace(string $text): string
    {
        return self::transform($text, self::MONO_UPPER, self::MONO_LOWER, self::MONO_DIGIT);
    }

    public static function strikethrough(string $text): string
    {
        $combining = mb_chr(self::COMBINING_LONG_STROKE_OVERLAY, 'UTF-8');
        $output = '';

        foreach (mb_str_split($text, 1, 'UTF-8') as $char) {
            $output .= $char;

            if ($char !== ' ' && $char !== "\n" && $char !== "\t") {
                $output .= $combining;
            }
        }

        return $output;
    }

    private static function transform(string $text, int $upperBase, int $lowerBase, ?int $digitBase): string
    {
        $output = '';

        foreach (mb_str_split($text, 1, 'UTF-8') as $char) {
            if (strlen($char) !== 1) {
                $output .= $char;

                continue;
            }

            $code = ord($char);

            if ($code >= 0x41 && $code <= 0x5A) {
                $output .= mb_chr($upperBase + ($code - 0x41), 'UTF-8');
            } elseif ($code >= 0x61 && $code <= 0x7A) {
                $output .= mb_chr($lowerBase + ($code - 0x61), 'UTF-8');
            } elseif ($digitBase !== null && $code >= 0x30 && $code <= 0x39) {
                $output .= mb_chr($digitBase + ($code - 0x30), 'UTF-8');
            } else {
                $output .= $char;
            }
        }

        return $output;
    }
}
