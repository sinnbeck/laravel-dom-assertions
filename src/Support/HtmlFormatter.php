<?php

namespace Sinnbeck\DomAssertions\Support;

/**
 * @internal
 */
class HtmlFormatter
{
    public function format($html)
    {
        if (! $html) {
            return '';
        }

        $dom = $this->convertToDomDocument($this->cleanupHtmlString($html));

        return $this->formatHtml($dom->saveXML($dom->documentElement, LIBXML_NOEMPTYTAG));
    }

    protected function convertToDomDocument($html): \DOMDocument
    {
        $dom = new \DOMDocument;

        $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_COMPACT | LIBXML_HTML_NODEFDTD | LIBXML_NOBLANKS | LIBXML_NOXMLDECL);
        $dom->formatOutput = true;
        // remove <html><body></body></html>
        $dom->replaceChild($dom->firstChild->firstChild, $dom->firstChild);

        return $dom;
    }

    protected function formatHtml(bool|string $html): string|array|null
    {
        if ($html === false) {
            return '';
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $html = $this->normalizeNewlines($html, "\r\n");
            $html = str_replace('&#13;', '', $html);
        }

        $html = $this->closeElementWithoutClosingElement($html);
        $html = $this->removeBody($html);

        return preg_replace('/^[ ]+(?=<)/m', '$0$0', $html);
    }

    private function closeElementWithoutClosingElement($html)
    {
        return preg_replace(
            '~></(?:area|base|br|col|command|embed|hr|img|input|keygen|link|meta|param|source|track|wbr)>~',
            ' />',
            $html
        );
    }

    protected function normalizeNewlines(bool|string $html, $replacement): string|array|null
    {
        return preg_replace('~\R~u', $replacement, $html);
    }

    protected function cleanupHtmlString($html): string
    {
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $html = $this->normalizeNewlines($html, "\n");

        return preg_replace('~>[[:space:]]++<~m', '><', $html);
    }

    private function removeBody(string $html)
    {
        $linebreak = PHP_EOL;
        $html = str_replace(["<body>{$linebreak}", "{$linebreak}</body>"], ['', ''], $html);
        $html = preg_replace('/^[ ]{2}/m', '', $html);

        return $html;
    }
}
