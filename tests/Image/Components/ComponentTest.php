<?php

declare(strict_types=1);

namespace MinepicTests\Image\Components;

use Minepic\Image\Components\Component;
use MinepicTests\TestCase;

class ComponentTest extends TestCase
{
    public function testValidHeadComponent()
    {
        $component = Component::getHead();

        $this->assertEquals(8, $component->getTop()->getWidth());
        $this->assertEquals(8, $component->getTop()->getHeight());

        $this->assertEquals(8, $component->getBottom()->getWidth());
        $this->assertEquals(8, $component->getBottom()->getHeight());

        $this->assertEquals(8, $component->getFront()->getWidth());
        $this->assertEquals(8, $component->getFront()->getHeight());

        $this->assertEquals(8, $component->getBack()->getWidth());
        $this->assertEquals(8, $component->getBack()->getHeight());

        $this->assertEquals(8, $component->getLeft()->getWidth());
        $this->assertEquals(8, $component->getLeft()->getHeight());

        $this->assertEquals(8, $component->getRight()->getWidth());
        $this->assertEquals(8, $component->getRight()->getHeight());
    }

    public function testValidHelmComponent()
    {
        $component = Component::getHelm();

        $this->assertEquals(8, $component->getTop()->getWidth());
        $this->assertEquals(8, $component->getTop()->getHeight());

        $this->assertEquals(8, $component->getBottom()->getWidth());
        $this->assertEquals(8, $component->getBottom()->getHeight());

        $this->assertEquals(8, $component->getFront()->getWidth());
        $this->assertEquals(8, $component->getFront()->getHeight());

        $this->assertEquals(8, $component->getBack()->getWidth());
        $this->assertEquals(8, $component->getBack()->getHeight());

        $this->assertEquals(8, $component->getLeft()->getWidth());
        $this->assertEquals(8, $component->getLeft()->getHeight());

        $this->assertEquals(8, $component->getRight()->getWidth());
        $this->assertEquals(8, $component->getRight()->getHeight());
    }
}
