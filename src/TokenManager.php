<?php

namespace HercDotTech\Stateless;

use InvalidArgumentException;
use Random\RandomException;

class TokenManager
{
    private const string TOKEN_PARTS_SEPARATOR = '.';

    private const string TOKEN_HASH_ALGORITHM = 'sha256';

    private string $privateKey;

    public function __construct(string $privateKey)
    {
        $this->privateKey = $privateKey;
    }

    /**
     * Generate a token for the given context and expiration time.
     *
     * @param TokenContext $context Context to generate token for
     * @param int|null $expiresAt Timestamp at which the token expires. Defaults to 1 hour from now.
     * @return string The generated token.
     * @throws RandomException
     */
    public function generateToken(TokenContext $context, ?int $expiresAt = null): string
    {
        // Get the token's identifier
        $tokenIdentifier = $context->getIdentifier();

        // Hash the token identifier
        $tokenIdentifier = $this->hashString($tokenIdentifier);

        // Set the default expiration date to be an hour in the future.
        $expiresAt = $expiresAt ?? time() + 3600;

        // Encode the expiration date
        $expiresAt = $this->encodeString($expiresAt);

        // Get the token signature
        $signature = $this->getTokenSignature($context, $expiresAt);

        // Build token string
        $token = implode(self::TOKEN_PARTS_SEPARATOR, [$tokenIdentifier, $expiresAt, $signature, $this->getRandomSeed()]);

        // Return the URL Safe token
        return $this->encodeToken($token);
    }

    /**
     * Verify the token against the reference context and given time
     *
     * @param string $token Token to verify
     * @param TokenContext $reference Reference context to compare against
     * @param int|null $checkTime Time to check token expiration against. Defaults to current time.
     * @return bool Whether the token is valid or not
     */
    public function verifyToken(string $token, TokenContext $reference, ?int $checkTime = null): bool
    {
        // Set the check time to now if not provided
        $checkTime = $checkTime ?? time();

        // Decode the token
        $token = $this->decodeToken($token);

        // Explode the token into parts
        $tokenParts = explode(self::TOKEN_PARTS_SEPARATOR, $token);

        // Validate token parts
        try {
            $this->validateTokenParts($tokenParts, $checkTime);
        } catch (InvalidArgumentException $e) {
            return false;
        }

        // Generate reference signature
        $referenceSignature = $this->getTokenSignature($reference, $tokenParts[1]);

        // Check if the reference signature matches the token's signature
        return $tokenParts[2] === $referenceSignature;
    }

    /**
     * Validate the provided array as a valid token parts array
     *
     * @param array $tokenParts Token parts array to validate
     * @param int $checkTime Timestamp to check expiration against
     * @return void
     *
     * @throws InvalidArgumentException If the token parts array is invalid
     */
    public function validateTokenParts(array $tokenParts, int $checkTime): void
    {
        if (empty($tokenParts)) {
            throw new InvalidArgumentException('Token parts array cannot be empty!');
        }

        if (count($tokenParts) != 4) {
            throw new InvalidArgumentException('Token parts array must contain four parts!');
        }

        $expiresAt = $this->decodeString($tokenParts[1]);
        if (!is_numeric($expiresAt)) {
            throw new InvalidArgumentException('Token parts array must contain a valid expiration timestamp!');
        }

        if ($expiresAt < $checkTime) {
            throw new InvalidArgumentException('Token has expired!');
        }
    }

    private function getTokenSignature(TokenContext $context, string $expiresAt): string
    {
        // Flatten token context clues
        $payload = ArrayEncoder::encode($context->getClues());

        // Generate the token signature
        return $this->hashString(implode(self::TOKEN_PARTS_SEPARATOR, [$context->getIdentifier(), $expiresAt, $payload]));
    }

    private function decodeToken(string $token): string
    {
        return base64_decode(urldecode($token));
    }

    private function encodeToken(string $token): string
    {
        return urlencode(base64_encode($token));
    }

    private function hashString(string $data): string
    {
        return $this->encodeString(hash_hmac(self::TOKEN_HASH_ALGORITHM, $data, $this->privateKey, true));
    }

    private function encodeString(string $data): string
    {
        return base64_encode($data);
    }

    private function decodeString(string $data): string
    {
        return base64_decode($data);
    }

    /**
     * @throws RandomException
     */
    private function getRandomSeed(): string
    {
        return $this->encodeString(random_bytes(8));
    }
}
