<?php

namespace Bgarb002\Markdown\Parser;

use \Bgarb002\Markdown\Parser\TokenType;

class Token
{
    /**
     * The type of token it is.
     * @var TokenType
     */
    private TokenType $type;

    /**
     * The lexumes that form the token.
     * @var string
     */
    private string $value;

    /**
     * Create a new token.
     * 
     * @param TokenType Type of token it should be.
     * @param string Value of the token.
     * @return Token
     */
    private function __construct(TokenType $type, string $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * Syntatic sugar to create a new token.
     * 
     * @param TokenType Type of token it should be.
     * @param string Value of the token.
     * @return Token
     */
    public static function from(TokenType $type, string $value)
    {
        return new static($type, $value);
    }

    /**
     * Return the type of token it is.
     * 
     * @return TokenType
     */
    public function type(): TokenType
    {
        return $this->type;
    }

    /**
     * Return the string value of the token.
     * 
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }
}
