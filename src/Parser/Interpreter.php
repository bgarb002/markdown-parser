<?php

namespace Bgarb002\Markdown\Parser;

use \Bgarb002\Markdown\Parser\Lexer;
use \Bgarb002\Markdown\Parser\Token;
use \Bgarb002\Markdown\Parser\TokenType;

class Interpreter
{
    /**
     * Current token being processed by the interpreter
     * @var \Generator
     */
    private Token $currentToken;

    /**
     * Lexar token generator
     * @var \Generator
     */
    private \Generator $tokenGenerator;

    /**
     * Create a new markdown to HTML interpreter.
     * 
     * @param Lexer $lexer Tokenizer with markdown text.
     * @return Interpreter
     */
    private function __construct(Lexer $lexer)
    {
        $this->tokenGenerator = $lexer->tokenGenerator();
        $this->currentToken = $this->tokenGenerator->current();
    }

    /**
     * Transform markdown text to HTML.
     * 
     * @return string Full HTML output of the markdown passed in.
     */
    public static function interpret(string $text): string
    {
        $instance = new static(new Lexer($text));
        return $instance->parseTokens();
    }

    /**
     * Process block input.
     * 
     * @return string HTML block element with inline text and/or links.
     */
    private function block(): string
    {
        $token = $this->currentToken;
        if ($this->currentToken->type() === TokenType::HEADER) {
            $this->eat(TokenType::HEADER);
            return $this->header(strlen($token->value()) - 1);
        } else {
            return $this->paragraph();
        }
    }

    /**
     * Validate current token is of a specific type and grab the next one.
     * 
     * @param TokenType $type
     * @return void
     */
    private function eat(TokenType $type): void
    {
        if ($this->currentToken->type() === $type) {
            $this->tokenGenerator->next();
            $this->currentToken = $this->tokenGenerator->current();
        } else {
            throw new \Exception(sprintf('Error parsing input. Expecting %s', $type->value));
        }
    }

    /**
     * Process header input.
     * 
     * @param int $level Header level that should be generated.
     * @return string HTML header element with inline text and/or links.
     */
    private function header(int $level): string
    {
        return "<h$level>" . $this->inlineText(). "</h$level>";
    }

    /**
     * Process inline tokens.
     * 
     * @return string Inline text and/or links.
     */
    private function inlineText(): string
    {
        $inlineText = '';
        while (in_array($this->currentToken->type(), [TokenType::CHAR, TokenType::LINK])) {
            if ($this->currentToken->type() === TokenType::LINK) {
                $inlineText .= $this->link();
            } else {
                $inlineText .= htmlentities($this->currentToken->value());
                $this->eat(TokenType::CHAR);
            }
        }
        return $inlineText;
    }

    /**
     * Parse link token.
     * 
     * @return string Anchor tag representation of link token.
     */
    private function link(): string
    {
        $token = $this->currentToken;
        $this->eat(TokenType::LINK);
        $parts = explode(']', $token->value());
        $text = htmlentities(substr($parts[0], 1));
        $url = htmlentities(substr($parts[1], 1, strlen($parts[1]) - 2));
        return sprintf('<a href="%s">%s</a>', $url, $text);
    }

    /**
     * Process paragraph input.
     * 
     * @return string HTML paragraph element with inline text and/or links.
     */
    private function paragraph(): string
    {
        $text = $this->inlineText();
        return '<p>' . $text . '</p>';
    }

    /**
     * Parse tokens generated by lexer one by one.
     * 
     * @return string Full HTML output after all generated tokens has been processed.
     */
    private function parseTokens(): string
    {
        $result = $this->block();
        while ($this->currentToken->type() === TokenType::NEWLINE) {
            $this->eat(TokenType::NEWLINE);
            if ($this->currentToken->type() === TokenType::EOF) {
                break;
            }
            $result .= $this->block();
        }
        return $result;
    }
}