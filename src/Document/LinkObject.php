<?php
declare(strict_types = 1);

namespace Mikemirten\JsonApi\Component\Document;

use Mikemirten\JsonApi\Component\Document\Behaviour\MetadataContainer;

/**
 * Link Object
 *
 * @see http://jsonapi.org/format/#document-links
 *
 * @package Mikemirten\JsonApi\Component\Document
 */
class LinkObject
{
    use MetadataContainer;

    /**
     * Link's reference
     *
     * @var string
     */
    protected $reference;

    /**
     * LinkObject constructor.
     *
     * @param string $reference
     */
    public function __construct(string $reference, array $metadata = [])
    {
        $this->reference = $reference;
        $this->metadata  = $metadata;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * Cast to a string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->reference;
    }
}