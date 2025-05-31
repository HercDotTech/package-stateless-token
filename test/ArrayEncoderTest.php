<?php

namespace HercDotTech\Stateless\Test;

use HercDotTech\Stateless\ArrayEncoder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ArrayEncoderTest extends TestCase
{
    public static function dataProvider(): array
    {
        return [
            [[], ''],
            [['test' => 'test'], 'eyJ0ZXN0IjoidGVzdCJ9'],
            [['test' => 'test', 'test2' => 'test2'], 'eyJ0ZXN0IjoidGVzdCIsInRlc3QyIjoidGVzdDIifQ=='],
            [['test' => ['test' => 'test']], 'eyJ0ZXN0Ijp7InRlc3QiOiJ0ZXN0In19'],
            [['test', 'second'], 'WyJ0ZXN0Iiwic2Vjb25kIl0='],
        ];
    }

    #[DataProvider('dataProvider')]
    public function testEncode(array $data, string $expected)
    {
        $this->assertEquals($expected, ArrayEncoder::encode($data));
    }

    #[DataProvider('dataProvider')]
    public function testDecode(array $expected, string $data)
    {
        $this->assertEquals($expected, ArrayEncoder::decode($data));
    }
}