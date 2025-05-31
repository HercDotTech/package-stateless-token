<?php

namespace HercDotTech\Stateless;

use BadMethodCallException;

class TokenContext
{
    /**
     * @var string Identifier.
     */
    private readonly string $identifier;

    /**
     * @var string[] Array of key-value pairs to be stored in the token context as clues
     */
    private array $clues;

    /**
     * @param string $identifier Identifier for this token context.
     */
    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
        $this->clues = [];
    }

    /**
     * Will return the unique identifier for this token context.
     *
     * @return string The identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getClues(): array
    {
        return $this->clues;
    }

    /**
     * Adds additional clues to the token context.
     * These clues should not change between requests as validation will fail otherwise.
     * For example, you could use the user's IP address or their browser agent.
     *
     * @param string $key The key to store the value under.
     * @param string $value The value to store.
     * @param bool $overwrite Whether to overwrite the value if it already exists.
     *
     * @return $this The current instance.
     * @throws BadMethodCallException
     */
    public function addClue(string $key, string $value, bool $overwrite = false): TokenContext
    {
        if (!$overwrite && isset($this->clues[$key])) {
            throw new BadMethodCallException("Key $key already exists!");
        }

        $this->clues[$key] = $value;

        return $this;
    }
}
