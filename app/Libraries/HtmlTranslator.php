<?php

namespace App\Libraries;

class HtmlTranslator
{
    private const SKIP_TAGS = ['script', 'style', 'textarea', 'code', 'pre'];
    private const DATA_TAGS = ['td'];

    /**
     * Traduz translate.
     */
    public static function translate(string $buffer): string
    {
        if (! self::shouldTranslate($buffer)) {
            return $buffer;
        }

        $locale = service('request')->getLocale();

        if ($locale === 'pt-BR') {
            return $buffer;
        }

        $exact = lang('Ui.auto', [], $locale);
        $fragments = lang('Ui.fragments', [], $locale);

        if (! is_array($exact)) {
            $exact = [];
        }

        if (! is_array($fragments)) {
            $fragments = [];
        }

        return self::translateHtml($buffer, $exact, $fragments);
    }

    /**
     * Informa se a resposta atual pode ser traduzida.
     */
    private static function shouldTranslate(string $buffer): bool
    {
        if ($buffer === '' || stripos($buffer, '<html') === false) {
            return false;
        }

        $contentType = service('response')->getHeaderLine('Content-Type');

        return $contentType === ''
            || stripos($contentType, 'html') !== false
            || stripos($contentType, 'text/plain') !== false;
    }

    /**
     * Traduz html.
     */
    private static function translateHtml(string $html, array $exact, array $fragments): string
    {
        $html = self::translateScriptBlocks($html, $exact, $fragments);
        $parts = preg_split('/(<[^>]+>)/u', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

        if (! is_array($parts)) {
            return $html;
        }

        $stack = [];
        $translated = '';

        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }

            if ($part[0] === '<') {
                if (self::hasAnyTag($stack, self::SKIP_TAGS) && ! self::isClosingSkippedTag($part)) {
                    $translated .= $part;

                    continue;
                }

                $translated .= self::translateTagAttributes($part, $exact, $fragments);
                self::updateStack($part, $stack);

                continue;
            }

            if (self::hasAnyTag($stack, self::SKIP_TAGS)) {
                $translated .= $part;

                continue;
            }

            if (self::hasAnyTag($stack, self::DATA_TAGS)) {
                $translated .= self::translateText($part, $exact, []);

                continue;
            }

            $translated .= self::translateText($part, $exact, $fragments);
        }

        return $translated;
    }

    /**
     * Atualiza a pilha de tags usada durante a traducao do HTML.
     */
    private static function updateStack(string $tag, array &$stack): void
    {
        if (preg_match('/^<\s*\/\s*([a-z0-9]+)/i', $tag, $match) === 1) {
            $closing = strtolower($match[1]);

            for ($index = count($stack) - 1; $index >= 0; $index--) {
                if ($stack[$index] === $closing) {
                    array_splice($stack, $index, 1);
                    break;
                }
            }

            return;
        }

        if (
            preg_match('/^<\s*([a-z0-9]+)/i', $tag, $match) !== 1
            || preg_match('/\/\s*>$/', $tag) === 1
            || preg_match('/^<\s*(area|base|br|col|embed|hr|img|input|link|meta|param|source|track|wbr)\b/i', $tag) === 1
        ) {
            return;
        }

        $stack[] = strtolower($match[1]);
    }

    /**
     * Informa se atende a regra de closing skipped tag.
     */
    private static function isClosingSkippedTag(string $tag): bool
    {
        return preg_match('/^<\s*\/\s*([a-z0-9]+)/i', $tag, $match) === 1
            && in_array(strtolower($match[1]), self::SKIP_TAGS, true);
    }

    /**
     * Traduz tag attributes.
     */
    private static function translateTagAttributes(string $tag, array $exact, array $fragments): string
    {
        $tag = preg_replace_callback(
            '/\b(placeholder|title|aria-label|alt)\s*=\s*(["\'])(.*?)\2/isu',
            static function (array $match) use ($exact, $fragments): string {
                return $match[1] . '=' . $match[2] . self::translateText($match[3], $exact, $fragments) . $match[2];
            },
            $tag
        ) ?? $tag;

        if (preg_match('/^<\s*input\b/i', $tag) === 1 && preg_match('/\btype\s*=\s*(["\']?)(submit|button|reset)\1/i', $tag) === 1) {
            $tag = preg_replace_callback(
                '/\bvalue\s*=\s*(["\'])(.*?)\1/isu',
                static function (array $match) use ($exact, $fragments): string {
                    return 'value=' . $match[1] . self::translateText($match[2], $exact, $fragments) . $match[1];
                },
                $tag
            ) ?? $tag;
        }

        return $tag;
    }

    /**
     * Traduz script blocks.
     */
    private static function translateScriptBlocks(string $html, array $exact, array $fragments): string
    {
        return preg_replace_callback(
            '/(<script\b[^>]*>)(.*?)(<\/script>)/isu',
            static function (array $match) use ($exact, $fragments): string {
                return $match[1] . self::translateScriptText($match[2], $exact, $fragments) . $match[3];
            },
            $html
        ) ?? $html;
    }

    /**
     * Traduz script text.
     */
    private static function translateScriptText(string $script, array $exact, array $fragments): string
    {
        return preg_replace_callback(
            '/(["\'`])((?:\\\\.|(?!\1).)*)\1/su',
            static function (array $match) use ($exact, $fragments): string {
                $quote = $match[1];
                $body = $match[2];

                if (! self::shouldTranslateScriptString($body, $exact, $fragments)) {
                    return $match[0];
                }

                $translated = str_contains($body, '<')
                    ? self::translateHtml($body, $exact, $fragments)
                    : self::translateText($body, $exact, $fragments);

                return $quote . self::escapeScriptString($translated, $quote) . $quote;
            },
            $script
        ) ?? $script;
    }

    /**
     * Informa se um texto de script pode ser traduzido com seguranca.
     */
    private static function shouldTranslateScriptString(string $text, array $exact, array $fragments): bool
    {
        $trimmed = trim(strip_tags($text));

        if ($trimmed === '') {
            return false;
        }

        if (
            preg_match('#^(?:https?:)?//#i', $trimmed) === 1
            || preg_match('#^/[A-Za-z0-9_./?=&%-]*$#', $trimmed) === 1
            || preg_match('/^[#.][A-Za-z0-9_.:#[\]-]+$/', $trimmed) === 1
        ) {
            return false;
        }

        if (array_key_exists($trimmed, $exact)) {
            return true;
        }

        foreach ($fragments as $source => $target) {
            if ($source !== '' && str_contains($trimmed, (string) $source)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Escapa o conteudo de script string.
     */
    private static function escapeScriptString(string $text, string $quote): string
    {
        return str_replace($quote, '\\' . $quote, $text);
    }

    /**
     * Traduz text.
     */
    private static function translateText(string $text, array $exact, array $fragments): string
    {
        if (trim($text) === '') {
            return $text;
        }

        preg_match('/^(\s*)(.*?)(\s*)$/us', $text, $match);
        $leading = $match[1] ?? '';
        $body = $match[2] ?? $text;
        $trailing = $match[3] ?? '';

        if (array_key_exists($body, $exact)) {
            return $leading . $exact[$body] . $trailing;
        }

        foreach (self::longestFirst($fragments) as $source => $target) {
            $body = str_replace($source, $target, $body);
        }

        return $leading . $body . $trailing;
    }

    /**
     * Ordena traducoes da mais longa para a mais curta para evitar substituicoes parciais.
     */
    private static function longestFirst(array $translations): array
    {
        uksort($translations, static fn ($left, $right): int => strlen($right) <=> strlen($left));

        return $translations;
    }

    /**
     * Informa se possui any tag.
     */
    private static function hasAnyTag(array $stack, array $tags): bool
    {
        return array_intersect($stack, $tags) !== [];
    }
}
