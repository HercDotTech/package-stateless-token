<?php

namespace HercDotTech\Stateless\Test;

use BadMethodCallException;
use HercDotTech\Stateless\TokenContext;
use PHPUnit\Framework\TestCase;

class TokenContextTest extends TestCase
{
    public function testIdentifier(): void
    {
        $instance = new TokenContext('test-identifier');
        $this->assertEquals('test-identifier', $instance->getIdentifier());
    }

    public function testAddClue(): void
    {
        $instance = new TokenContext('test-identifier');
        $instance->addClue('test-key', 'test-value');

        $this->assertEquals(['test-key' => 'test-value'], $instance->getClues());
    }

    public function testAddClueOverwrite(): void
    {
        $instance = new TokenContext('test-identifier');
        $instance->addClue('test-key', 'test-value');
        $instance->addClue('test-key', 'test-value-2', true);

        $this->assertEquals(['test-key' => 'test-value-2'], $instance->getClues());
    }

    public function testAddClueDuplicate(): void
    {
        $instance = new TokenContext('test-identifier');
        $instance->addClue('test-key', 'test-value');

        $exceptionThrown = false;

        try {
            $instance->addClue('test-key', 'test-value-2');
        } catch (BadMethodCallException $e) {
            $exceptionThrown = true;
            $this->assertEquals('Key test-key already exists!', $e->getMessage());
        }

        $this->assertTrue($exceptionThrown);
        $this->assertEquals(['test-key' => 'test-value'], $instance->getClues());
    }
}