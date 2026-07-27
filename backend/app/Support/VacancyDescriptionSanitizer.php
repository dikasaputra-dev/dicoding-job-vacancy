<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

final class VacancyDescriptionSanitizer
{
    /**
     * HTML tags that may remain in vacancy descriptions.
     *
     * @var list<string>
     */
    private const ALLOWED_TAGS = [
        'p',
        'br',
        'strong',
        'b',
        'em',
        'i',
        'u',
        'h2',
        'h3',
        'ul',
        'ol',
        'li',
        'blockquote',
    ];

    /**
     * Elements whose entire content must be removed.
     *
     * @var list<string>
     */
    private const DROP_WITH_CONTENT_TAGS = [
        'script',
        'style',
        'iframe',
        'object',
        'embed',
        'template',
        'noscript',
        'svg',
        'math',
    ];

    /**
     * Sanitize vacancy rich-text HTML.
     */
    public function sanitize(string $html): string
    {
        if (trim($html) === '') {
            return '';
        }

        $document = new DOMDocument(
            version: '1.0',
            encoding: 'UTF-8',
        );

        $previousErrorSetting = libxml_use_internal_errors(true);

        $loaded = $document->loadHTML(
            '<?xml encoding="UTF-8">'
            .'<div data-vacancy-description-root="1">'
            .$html
            .'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previousErrorSetting);

        if ($loaded === false) {
            return '';
        }

        $xpath = new DOMXPath($document);

        $rootNodes = $xpath->query(
            '//*[@data-vacancy-description-root="1"]',
        );

        $root = $rootNodes === false
            ? null
            : $rootNodes->item(0);

        if (! $root instanceof DOMElement) {
            return '';
        }

        $root->removeAttribute(
            'data-vacancy-description-root',
        );

        $this->sanitizeChildren($root);

        return trim(
            $this->innerHtml($root),
        );
    }

    /**
     * Sanitize all direct and nested children.
     */
    private function sanitizeChildren(
        DOMNode $parent,
    ): void {
        $node = $parent->firstChild;

        while ($node !== null) {
            $nextNode = $node->nextSibling;

            if ($node instanceof DOMElement) {
                $tagName = strtolower($node->tagName);

                if (
                    in_array(
                        $tagName,
                        self::DROP_WITH_CONTENT_TAGS,
                        true,
                    )
                ) {
                    $parent->removeChild($node);
                    $node = $nextNode;

                    continue;
                }

                if (
                    ! in_array(
                        $tagName,
                        self::ALLOWED_TAGS,
                        true,
                    )
                ) {
                    $this->sanitizeChildren($node);

                    while ($node->firstChild !== null) {
                        $parent->insertBefore(
                            $node->firstChild,
                            $node,
                        );
                    }

                    $parent->removeChild($node);
                    $node = $nextNode;

                    continue;
                }

                $this->removeAttributes($node);
                $this->sanitizeChildren($node);
            } elseif ($node->nodeType === XML_COMMENT_NODE) {
                $parent->removeChild($node);
            }

            $node = $nextNode;
        }
    }

    /**
     * Remove every HTML attribute from an allowed element.
     */
    private function removeAttributes(
        DOMElement $element,
    ): void {
        while ($element->attributes->length > 0) {
            $attribute = $element->attributes->item(0);

            if ($attribute === null) {
                break;
            }

            $element->removeAttributeNode($attribute);
        }
    }

    /**
     * Retrieve the HTML inside an element.
     */
    private function innerHtml(
        DOMElement $element,
    ): string {
        $document = $element->ownerDocument;

        if (! $document instanceof DOMDocument) {
            return '';
        }

        $html = '';

        foreach ($element->childNodes as $childNode) {
            $serializedNode = $document->saveHTML(
                $childNode,
            );

            if ($serializedNode !== false) {
                $html .= $serializedNode;
            }
        }

        return $html;
    }
}
