<?php

namespace Mikemirten\Component\JsonApi\Document;

use Mikemirten\Component\JsonApi\Document\Behaviour\LinksAwareInterface;
use Mikemirten\Component\JsonApi\Document\Behaviour\MetadataAwareInterface;
use PHPUnit\Framework\TestCase;

/**
 * @group document
 */
class IdentifierCollectionRelationshipTest extends TestCase
{
    public function testIdentifiers()
    {
        $relationship = new IdentifierCollectionRelationship();

        $this->assertEmpty($relationship->getIdentifiers());

        $identifier = $this->createMock(ResourceIdentifierObject::class);
        $relationship->addIdentifier($identifier);

        $this->assertSame($identifier, $relationship->getFirstIdentifier());
        $this->assertSame([$identifier], $relationship->getIdentifiers());
    }

    public function testIterator()
    {
        $identifier = $this->createMock(ResourceIdentifierObject::class);

        $relationship = new IdentifierCollectionRelationship();
        $relationship->addIdentifier($identifier);

        $this->assertSame([$identifier], iterator_to_array($relationship));
    }

    public function testMetadata()
    {
        $relationship = new IdentifierCollectionRelationship(['test' => 42]);

        $this->assertInstanceOf(MetadataAwareInterface::class, $relationship);

        $this->assertFalse($relationship->hasMetadataAttribute('qwerty'));
        $this->assertTrue($relationship->hasMetadataAttribute('test'));
        $this->assertSame(42, $relationship->getMetadataAttribute('test'));
        $this->assertSame(['test' => 42], $relationship->getMetadata());
    }

    public function testLinks()
    {
        $relationship = new IdentifierCollectionRelationship();

        $this->assertInstanceOf(LinksAwareInterface::class, $relationship);

        $link = $this->createMock(LinkObject::class);
        $relationship->setLink('test', $link);

        $this->assertFalse($relationship->hasLink('qwerty'));
        $this->assertTrue($relationship->hasLink('test'));
        $this->assertSame($link, $relationship->getLink('test'));
        $this->assertSame(['test' => $link], $relationship->getLinks());
    }

    public function testToArrayResources()
    {
        $resource = $this->createMock(ResourceIdentifierObject::class);

        $resource->expects($this->once())
            ->method('toArray')
            ->willReturn(['test' => 'qwerty']);

        $relationship = new IdentifierCollectionRelationship();
        $relationship->addIdentifier($resource);

        $this->assertSame(
            [
                'data' => [
                    ['test' => 'qwerty']
                ]
            ],
            $relationship->toArray()
        );
    }

    public function testToArrayLinks()
    {
        $relationship = new IdentifierCollectionRelationship();
        $relationship->setLink('test_link', $this->createLink(
            'http://test.com',
            ['link_meta' => 123]
        ));

        $this->assertSame(
            [
                'links' => [
                    'test_link' => [
                        'href' => 'http://test.com',
                        'meta' => [
                            'link_meta' => 123
                        ]
                    ]
                ],
                'data' => []
            ],
            $relationship->toArray());
    }

    public function testToArrayMetadata()
    {
        $relationship = new IdentifierCollectionRelationship();
        $relationship->setMetadataAttribute('test', 'qwerty');

        $this->assertSame(
            [
                'meta' => ['test' => 'qwerty'],
                'data' => []
            ],
            $relationship->toArray()
        );
    }

    public function createLink(string $reference, array $metadata = []): LinkObject
    {
        $link = $this->createMock(LinkObject::class);

        $link->method('hasMetadata')
            ->willReturn(! empty($metadata));

        $link->method('getMetadata')
            ->willReturn($metadata);

        $link->method('getReference')
            ->willReturn($reference);

        return $link;
    }
}