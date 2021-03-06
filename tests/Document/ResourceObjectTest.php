<?php

namespace Mikemirten\Component\JsonApi\Document;

use Mikemirten\Component\JsonApi\Document\Behaviour\LinksAwareInterface;
use Mikemirten\Component\JsonApi\Document\Behaviour\MetadataAwareInterface;
use Mikemirten\Component\JsonApi\Document\Behaviour\RelationshipsAwareInterface;
use PHPUnit\Framework\TestCase;

/**
 * @group document
 */
class ResourceObjectTest extends TestCase
{
    public function testBasics()
    {
        $resource = new ResourceObject('42', 'test');

        $this->assertSame('42', $resource->getId());
        $this->assertSame('test', $resource->getType());
    }

    public function testAttributes()
    {
        $resource = new ResourceObject('42', 'test', [
            'test' => 42
        ]);

        $this->assertFalse($resource->hasAttribute('qwerty'));
        $this->assertTrue($resource->hasAttribute('test'));
        $this->assertSame(42, $resource->getAttribute('test'));
        $this->assertSame(['test' => 42], $resource->getAttributes());
    }

    /**
     * @depends testAttributes
     */
    public function testRemoveAttribute()
    {
        $resource = new ResourceObject('42', 'test', [
            'test' => 42
        ]);

        $resource->removeAttribute('test');

        $this->assertFalse($resource->hasAttribute('test'));
    }

    public function testMetadata()
    {
        $resource = new ResourceObject('42', 'test', [], [
            'test' => 42
        ]);

        $this->assertInstanceOf(MetadataAwareInterface::class, $resource);

        $this->assertFalse($resource->hasMetadataAttribute('qwerty'));
        $this->assertTrue($resource->hasMetadataAttribute('test'));
        $this->assertSame(42, $resource->getMetadataAttribute('test'));
        $this->assertSame(['test' => 42], $resource->getMetadata());
    }

    /**
     * @depends testMetadata
     */
    public function testMetadataRemove()
    {
        $resource = new ResourceObject('1', 'test');
        $resource->setMetadataAttribute('test', 42);

        $this->assertTrue($resource->hasMetadataAttribute('test'));

        $resource->removeMetadataAttribute('test');

        $this->assertFalse($resource->hasMetadataAttribute('test'));
    }

    public function testLinks()
    {
        $resource = new ResourceObject('42', 'test');

        $this->assertInstanceOf(LinksAwareInterface::class, $resource);

        $link = $this->createMock(LinkObject::class);
        $resource->setLink('test', $link);

        $this->assertFalse($resource->hasLink('qwerty'));
        $this->assertTrue($resource->hasLink('test'));
        $this->assertSame($link, $resource->getLink('test'));
        $this->assertSame(['test' => $link], $resource->getLinks());
    }

    public function testLinkRemove()
    {
        $resource = new ResourceObject('42', 'test');

        $link = $this->createMock(LinkObject::class);
        $resource->setLink('test', $link);

        $this->assertTrue($resource->hasLink('test'));

        $resource->removeLink('test');

        $this->assertFalse($resource->hasLink('test'));
    }

    public function testRelationships()
    {
        $resource = new ResourceObject('42', 'test');

        $this->assertInstanceOf(RelationshipsAwareInterface::class, $resource);

        $relationship = $this->createMock(AbstractRelationship::class);
        $resource->setRelationship('test', $relationship);

        $this->assertFalse($resource->hasRelationship('qwerty'));
        $this->assertTrue($resource->hasRelationship('test'));
        $this->assertSame($relationship, $resource->getRelationship('test'));
        $this->assertSame(['test' => $relationship], $resource->getRelationships());
    }

    /**
     * @depends testRelationships
     */
    public function testRemoveRelationship()
    {
        $resource = new ResourceObject('42', 'test');

        $relationship = $this->createMock(AbstractRelationship::class);
        $resource->setRelationship('test', $relationship);

        $resource->removeRelationship('test');

        $this->assertFalse($resource->hasRelationship('test'));
    }

    public function testToArrayBasics()
    {
        $resource = new ResourceObject('42', 'test');

        $this->assertSame(
            [
                'id'   => '42',
                'type' => 'test'
            ],
            $resource->toArray()
        );
    }

    public function testToArrayAttributes()
    {
        $resource = new ResourceObject('42', 'test');
        $resource->setAttribute('test_attr', 'qwerty');

        $this->assertSame(
            ['test_attr' => 'qwerty'],
            $resource->toArray()['attributes']
        );
    }

    public function testToArrayRelationships()
    {
        $relationship = $this->createMock(AbstractRelationship::class);

        $relationship->expects($this->once())
            ->method('toArray')
            ->willReturn(['test' => 'qwerty']);

        $resource = new ResourceObject('42', 'test');
        $resource->setRelationship('test_rel', $relationship);

        $this->assertSame(
            ['test_rel' => ['test' => 'qwerty']],
            $resource->toArray()['relationships']
        );
    }

    public function testToArrayMetadata()
    {
        $resource = new ResourceObject('42', 'test');
        $resource->setMetadataAttribute('test_attr', 'qwerty');

        $this->assertSame(
            ['test_attr' => 'qwerty'],
            $resource->toArray()['meta']
        );
    }

    public function testToArrayLinks()
    {
        $link = $this->createMock(LinkObject::class);

        $link->expects($this->once())
            ->method('getReference')
            ->willReturn('http://qwerty.com');

        $resource = new ResourceObject('42', 'test');
        $resource->setLink('test_link', $link);

        $this->assertSame(
            ['test_link' => 'http://qwerty.com'],
            $resource->toArray()['links']
        );
    }

    public function testToString()
    {
        $resource = new ResourceObject('42', 'test');

        $this->assertRegExp('~Resource~', (string) $resource);
        $this->assertRegExp('~42~', (string) $resource);
        $this->assertRegExp('~test~', (string) $resource);
    }

    /**
     * @expectedException \Mikemirten\Component\JsonApi\Document\Exception\AttributeNotFoundException
     *
     * @expectedExceptionMessageRegExp ~Attribute~
     * @expectedExceptionMessageRegExp ~42~
     * @expectedExceptionMessageRegExp ~test~
     * @expectedExceptionMessageRegExp ~qwerty~
     */
    public function testAttributeNotFound()
    {
        $resource = new ResourceObject('42', 'test');
        $resource->getAttribute('qwerty');
    }

    /**
     * @expectedException \Mikemirten\Component\JsonApi\Document\Exception\AttributeOverrideException
     *
     * @expectedExceptionMessageRegExp ~Attribute~
     * @expectedExceptionMessageRegExp ~42~
     * @expectedExceptionMessageRegExp ~test~
     * @expectedExceptionMessageRegExp ~qwerty~
     */
    public function testAttributeOverride()
    {
        $resource = new ResourceObject('42', 'test');

        $resource->setAttribute('qwerty', 1);
        $resource->setAttribute('qwerty', 2);
    }

    /**
     * @expectedException \Mikemirten\Component\JsonApi\Document\Exception\RelationshipNotFoundException
     *
     * @expectedExceptionMessageRegExp ~Relationship~
     * @expectedExceptionMessageRegExp ~42~
     * @expectedExceptionMessageRegExp ~test~
     * @expectedExceptionMessageRegExp ~qwerty~
     */
    public function testRelationshipNotFound()
    {
        $resource = new ResourceObject('42', 'test');
        $resource->getRelationship('qwerty');
    }

    /**
     * @expectedException \Mikemirten\Component\JsonApi\Document\Exception\RelationshipOverrideException
     *
     * @expectedExceptionMessageRegExp ~Relationship~
     * @expectedExceptionMessageRegExp ~42~
     * @expectedExceptionMessageRegExp ~test~
     * @expectedExceptionMessageRegExp ~qwerty~
     */
    public function testRelationshipOverride()
    {
        $resource = new ResourceObject('42', 'test');

        $relationship = $this->createMock(AbstractRelationship::class);

        $resource->setRelationship('qwerty', $relationship);
        $resource->setRelationship('qwerty', $relationship);
    }

    /**
     * @expectedException \Mikemirten\Component\JsonApi\Document\Exception\MetadataAttributeNotFoundException
     *
     * @expectedExceptionMessageRegExp ~Resource~
     * @expectedExceptionMessageRegExp ~test_attribute~
     */
    public function testMetadataNotFound()
    {
        $resource = new ResourceObject('42', 'test');

        $resource->getMetadataAttribute('test_attribute');
    }

    /**
     * @expectedException \Mikemirten\Component\JsonApi\Document\Exception\MetadataAttributeOverrideException
     *
     * @expectedExceptionMessageRegExp ~Resource~
     * @expectedExceptionMessageRegExp ~test_attribute~
     */
    public function testMetadataOverride()
    {
        $resource = new ResourceObject('42', 'test');

        $resource->setMetadataAttribute('test_attribute', 1);
        $resource->setMetadataAttribute('test_attribute', 2);
    }

    /**
     * @expectedException \Mikemirten\Component\JsonApi\Document\Exception\LinkNotFoundException
     *
     * @expectedExceptionMessageRegExp ~Resource~
     * @expectedExceptionMessageRegExp ~test_link~
     */
    public function testLinkNotFound()
    {
        $resource = new ResourceObject('42', 'test');

        $resource->getLink('test_link');
    }

    /**
     * @expectedException \Mikemirten\Component\JsonApi\Document\Exception\LinkOverrideException
     *
     * @expectedExceptionMessageRegExp ~Resource~
     * @expectedExceptionMessageRegExp ~test_link~
     */
    public function testLinkOverride()
    {
        $resource = new ResourceObject('42', 'test');

        $link = $this->createMock(LinkObject::class);

        $resource->setLink('test_link', $link);
        $resource->setLink('test_link', $link);
    }
}