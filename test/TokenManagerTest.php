<?php

namespace HercDotTech\Stateless\Test;

use HercDotTech\Stateless\TokenManager;
use HercDotTech\Stateless\TokenContext;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TokenManagerTest extends TestCase
{
    private TokenManager $generator;
    private TokenManager $validator;

    public static function tokenPartsProvider(): array
    {
        return [
            [['123456789'], 123456789, 'Token parts array must contain four parts!'],
            [['123456789','123456789','123456789','123456789','123456789'], 123456789, 'Token parts array must contain four parts!'],

            [['val0', 'val1', 'val2', 'val3'], 123456789, 'Token parts array must contain a valid expiration timestamp!'],

            [['val1', '123456789', 'val2', 'val3'], 123457789, 'Token has expired!'],
        ];
    }

    public function setUp(): void
    {
        $this->generator = new TokenManager('test-key');
        $this->validator = new TokenManager('test-key');
    }

    public function testGeneratedTokensAreDifferent(): void
    {
        $context = new TokenContext('test-identifier');
        $context->addClue('test-key', 'test-value');

        $tokenOne = $this->generator->generateToken($context, 123456789);
        $tokenTwo = $this->generator->generateToken($context, 123456789);

        $this->assertNotEquals($tokenOne, $tokenTwo);
    }

    #[DataProvider('tokenPartsProvider')]
    public function testValidateTokenParts(array $input, int $checkTime, string $expectedError): void
    {
        $input[1] = base64_encode($input[1]);

        try {
            $this->validator->validateTokenParts($input, $checkTime);
            $this->expectNotToPerformAssertions();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals($expectedError, $e->getMessage());
        }
    }

    public function testVerifyTokenExpiryDate(): void
    {
        $context = new TokenContext('test-identifier');
        $context->addClue('test-key', 'test-value');

        $token = $this->generator->generateToken($context, 123456789);

        $this->assertTrue($this->validator->verifyToken($token, $context, 123455789));
        $this->assertTrue($this->validator->verifyToken($token, $context, 123456789));
        $this->assertFalse($this->validator->verifyToken($token, $context, 123456790));
        $this->assertFalse($this->validator->verifyToken($token, $context, 123457789));
    }

    public function testVerifyTokenIdentifier(): void
    {
        $context = new TokenContext('test-identifier');
        $token = $this->generator->generateToken($context, 123456789);

        $newContext = new TokenContext('test-identifier-2');

        $this->assertTrue($this->validator->verifyToken($token, $context, 123455789));
        $this->assertFalse($this->validator->verifyToken($token, $newContext, 123455789));
    }

    public function testVerifyTokenClues(): void
    {
        $context = new TokenContext('test-identifier');
        $context->addClue('test-key', 'test-value');
        $token = $this->generator->generateToken($context, 123456789);

        $newContext = new TokenContext('test-identifier');
        $newContext->addClue('test-key-2', 'test-value');

        $this->assertTrue($this->validator->verifyToken($token, $context, 123455789));
        $this->assertFalse($this->validator->verifyToken($token, $newContext, 123455789));
    }
}
