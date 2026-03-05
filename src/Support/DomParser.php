<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions\Support;

use DOMDocument;
use DOMException;
use DOMXPath;
use Symfony\Component\CssSelector\CssSelectorConverter;

/**
 * @internal
 */
final class DomParser
{
    protected object $root;

    protected object $document;

    public function __construct($html = '')
    {
        if ($html) {
            $this->setContent($html);
        }
    }

    public static function new($html = ''): self
    {
        return new DomParser($html);
    }

    public function setContent($html): void
    {
        if (PHP_VERSION_ID >= 80400) {
            $dom = \Dom\HTMLDocument::createFromString(trim($html), LIBXML_NOERROR, 'UTF-8');
        } else {
            $dom = new DOMDocument;
            $html = '<?xml encoding="UTF-8">'.trim($html);
            $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_COMPACT | LIBXML_HTML_NODEFDTD | LIBXML_NOBLANKS | LIBXML_NOXMLDECL);
        }

        $this->document = $dom;
        $root = $dom->getElementsByTagName('html')->item(0);

        if (is_null($root)) {
            throw new DOMException('No DOM found!');
        }

        $this->setRoot($root);
    }

    public function getElementOfType(string $type, $index = 0): ?object
    {
        return $this->getRoot()->getElementsByTagName($type)->item($index);
    }

    public function getDocument(): object
    {
        return $this->document;
    }

    public function getRoot(): object
    {
        return $this->root;
    }

    public function setRoot(object $root): self
    {
        $this->root = $root;

        return $this;
    }

    public function cloneFromRoot(): self
    {
        return new self($this->getContent());
    }

    public function getAttributeForRoot(string $attribute): string
    {
        return $this->getRoot()->getAttribute($attribute);
    }

    public function hasAttributeForRoot(string $attribute): bool
    {
        return $this->getRoot()->hasAttribute($attribute);
    }

    public function getDocType()
    {
        $documentType = $this->getDocument()->doctype;
        if (! $documentType) {
            return null;
        }

        return $documentType->publicId ? implode(' ',
            [
                $documentType->name,
                $documentType->publicId,
            ]) : $documentType->name;
    }

    public function getContent(): string
    {
        return $this->getRoot()->C14N();
    }

    public function getContentFormatted(): string
    {
        return (new HtmlFormatter)->format($this->getContent());
    }

    public function getAttributeFor($for, string $attribute): string|bool
    {
        $for = is_string($for) ? $this->getElementOfType($for) : $for;

        if (! is_object($for)) {
            return false;
        }

        return $for->getAttribute($attribute);
    }

    public function getType()
    {
        return $this->getRoot()->nodeName;
    }

    public function query($selector): ?object
    {
        if (PHP_VERSION_ID >= 80400) {
            return $this->getRoot()->querySelector($selector);
        }

        return $this->queryAll($selector)->item(0);
    }

    public function queryAll(string $selector)
    {
        if (PHP_VERSION_ID >= 80400) {
            return $this->getRoot()->querySelectorAll($selector);
        }

        return (new DOMXPath($this->cloneFromRoot()->getRoot()->ownerDocument))
            ->query($this->cssSelectorToXpath($selector));
    }

    protected function cssSelectorToXpath($selector)
    {
        $converter = new CssSelectorConverter;
        $xpath = $converter->toXpath($selector);

        return str_replace('\\', '', $xpath);
    }

    public function getText()
    {
        return $this->getRoot()->nodeValue;
    }
}
