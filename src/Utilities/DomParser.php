<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions\Utilities;

use DOMDocument;
use DOMElement;
use DOMException;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use Symfony\Component\CssSelector\CssSelectorConverter;

final class DomParser
{
    protected DOMElement $root;

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
        $dom = new DOMDocument();

        $html = '<?xml encoding="UTF-8">'.trim($html);
        $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_COMPACT | LIBXML_HTML_NODEFDTD | LIBXML_NOBLANKS | LIBXML_NOXMLDECL);
        $root = $dom->getElementsByTagName('body')->item(0);

        if (is_null($root)) {
            throw new DOMException('No body element found!');
        }

        $this->setRoot($root);
    }

    public function getElementOfType(string $type, $index = 0): ?DOMNode
    {
        return $this->getRoot()->getElementsByTagName($type)->item($index);
    }

    public function getRoot(): DOMElement
    {
        return $this->root;
    }

    public function setRoot(DOMElement $root): self
    {
        $this->root = $root;

        return $this;
    }

    public function cloneFromRoot(): self
    {
        return new self($this->getContent());
    }

    public function getAttributeForRoot(string $attribute): string|bool
    {
        return $this->getRoot()->getAttribute($attribute);
    }

    public function hasAttributeForRoot(string $attribute): string|bool
    {
        return $this->getRoot()->hasAttribute($attribute);
    }

    public function getContent(): string
    {
        return $this->getRoot()->C14N();
    }

    public function getAttributeFor($for, string $attribute): string|bool
    {
        $for = is_string($for) ? $this->getElementOfType($for) : $for;

        if (! $for instanceof DOMElement) {
            return false;
        }

        return $for->getAttribute($attribute);
    }

    public function getType()
    {
        return $this->getRoot()->nodeName;
    }

    public function query($selector): DOMNode|null
    {
        return $this->queryAll($selector)->item(0);
    }

    public function queryAll(string $selector): DOMNodeList
    {
        $converter = new CssSelectorConverter();
        $parser = $this->cloneFromRoot();

        return (new DOMXPath($parser->getRoot()->ownerDocument))->query($converter->toXpath($selector));
    }

    public function getText()
    {
        return $this->getRoot()->nodeValue;
    }
}
