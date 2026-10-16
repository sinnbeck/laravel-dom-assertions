<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions\Support;

use DOMDocument;
use DOMElement;
use DOMException;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use Symfony\Component\CssSelector\CssSelectorConverter;

/**
 * @internal
 */
final class DomParser
{
    private static ?CssSelectorConverter $converter = null;

    /**
     * @var array<string, string>
     */
    private static array $xpathCache = [];

    protected DOMElement $root;

    protected DOMDocument $document;

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

    public static function fromElement(DOMElement $element): self
    {
        $parser = new self;

        $document = $element->ownerDocument;

        if ($document instanceof DOMDocument) {
            $parser->document = $document;
        }

        return $parser->setRoot($element);
    }

    public function setContent($html): void
    {
        $dom = new DOMDocument;

        $html = '<?xml encoding="UTF-8">'.trim($html);
        $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_COMPACT | LIBXML_HTML_NODEFDTD | LIBXML_NOBLANKS);
        $this->document = $dom;
        $root = $dom->getElementsByTagName('html')->item(0);

        if (is_null($root)) {
            throw new DOMException('No DOM found!');
        }

        $this->setRoot($root);
    }

    public function getElementOfType(string $type, $index = 0): ?DOMNode
    {
        return $this->root->getElementsByTagName($type)->item($index);
    }

    public function getDocument(): DOMDocument
    {
        return $this->document;
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

    public function getAttributeForRoot(string $attribute): string
    {
        return $this->root->getAttribute($attribute);
    }

    public function hasAttributeForRoot(string $attribute): bool
    {
        return $this->root->hasAttribute($attribute);
    }

    public function getDocType(): ?string
    {
        $documentType = $this->document->doctype;
        if (! $documentType instanceof \DOMDocumentType) {
            return null;
        }

        return $documentType->publicId !== '' && $documentType->publicId !== '0' ? implode(' ',
            [
                $documentType->name,
                $documentType->publicId,
            ]) : $documentType->name;
    }

    public function getContent(): string
    {
        return $this->root->C14N();
    }

    public function getContentFormatted(): string
    {
        return (new HtmlFormatter)->format($this->getContent());
    }

    public function getAttributeFor($for, string $attribute): string|bool
    {
        $for = is_string($for) ? $this->getElementOfType($for) : $for;

        if (! $for instanceof DOMElement) {
            return false;
        }

        return $for->getAttribute($attribute);
    }

    public function getType(): string
    {
        return $this->root->nodeName;
    }

    public function query(string $selector): ?DOMElement
    {
        $node = $this->queryAll($selector)->item(0);

        return $node instanceof DOMElement ? $node : null;
    }

    public function queryAll(string $selector): DOMNodeList
    {
        return (new DOMXPath($this->root->ownerDocument))
            ->query($this->cssSelectorToXpath($selector), $this->root);
    }

    private function cssSelectorToXpath(string $selector): string
    {
        return self::$xpathCache[$selector] ??= str_replace(
            '\\',
            '',
            (self::$converter ??= new CssSelectorConverter)->toXpath($selector)
        );
    }

    public function getText(): ?string
    {
        return $this->root->nodeValue;
    }
}
