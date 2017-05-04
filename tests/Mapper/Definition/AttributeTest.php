<?php

namespace Mikemirten\Component\JsonApi\Mapper\Definition;

use PHPUnit\Framework\TestCase;

/**
 * @group   mapper
 * @package Mikemirten\Component\JsonApi\Mapper\Definition
 */
class AttributeTest extends TestCase
{
    public function testBasics()
    {
        $attribute = new Attribute('test_attr', 'getAttr');

        $this->assertSame('test_attr', $attribute->getName());
        $this->assertSame('getAttr', $attribute->getGetter());
    }

    public function testType()
    {
        $attribute = new Attribute('test_attr', 'getAttr');

        $this->assertFalse($attribute->hasType());

        $attribute->setType('datetime');

        $this->assertTrue($attribute->hasType());
        $this->assertSame('datetime', $attribute->getType());
    }

    public function testTypeParameters()
    {
        $attribute = new Attribute('test_attr', 'getAttr');

        $this->assertSame([], $attribute->getTypeParameters());

        $attribute->setTypeParameters(['Y-m-d']);

        $this->assertSame(['Y-m-d'], $attribute->getTypeParameters());
    }

    public function testPropertyName()
    {
        $attribute = new Attribute('test_attr', 'getAttr');

        $this->assertFalse($attribute->hasPropertyName());

        $attribute->setPropertyName('createdAt');

        $this->assertTrue($attribute->hasPropertyName());
        $this->assertSame('createdAt', $attribute->getPropertyName());
    }

    /**
     * @depends testType
     * @depends testTypeParameters
     * @depends testPropertyName
     */
    public function testMerge()
    {
        $attribute  = new Attribute('test_attr', 'getAttr');
        $attribute2 = $this->createMock(Attribute::class);

        $attribute2->method('hasType')
            ->willReturn(true);

        $attribute2->method('getType')
            ->willReturn('test_type');

        $attribute2->method('getTypeParameters')
            ->willReturn(['qwerty']);

        $attribute2->method('hasPropertyName')
            ->willReturn(true);

        $attribute2->method('getPropertyName')
            ->willReturn('test_name');

        $attribute->merge($attribute2);

        $this->assertSame('test_type', $attribute->getType());
        $this->assertSame(['qwerty'], $attribute->getTypeParameters());
        $this->assertSame('test_name', $attribute->getPropertyName());
    }
}