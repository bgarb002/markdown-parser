<?php

namespace Bgarb002\Markdown\Parser;

use \Bgarb002\Markdown\Parser\Token;
use \Bgarb002\Markdown\Parser\TokenType as Type;

class Lexer
{
    /**
     * Representation of lines in the markdown text.
     * @var array
     */
    private array $lines;

    /**
     * Create a new lexical tokenizer for the markdown text.
     * 
     * @param string $text
     * @return Lexer
     */
    public function __construct(string $text)
    {
        $this->lines = preg_split('/\n/', $text);
    }

    /**
     * Generator of tokens analyzed by the lexer.
     * 
     * @return Token Lexumes and their token type.
     */
    public function tokenGenerator(): \Generator
    {
        $lineCount = count($this->lines);
        for ($i = 0; $i < $lineCount; $i++) {
            $line = $this->lines[$i];
            if (empty($line)) {
                continue;
            }
            $headerToken = $this->getHeaderToken($line);
            if (!empty($headerToken)) {
                yield $headerToken;
            }
            if (empty($headerToken) && $i + 1 < $lineCount && !empty($this->lines[$i + 1])) {
                $line = $this->combineConsecutiveLines($line, $i + 1);
            }
            $tokens = empty($headerToken) ?
                $this->getInlineTokens($line) :
                $this->getInlineTokens(substr($line, strlen($headerToken->value())));
            foreach ($tokens as $token) {
                yield $token;
            }
            yield Token::from(Type::NEWLINE, "\n");
        }
        yield Token::from(Type::EOF, PHP_EOL);
    }

    /**
     * Recursively concatenate consecutive non-empty markdown paragraph lines.
     * 
     * @param string $line Initial/current version of concatenated line.
     * @param int $i Line index within the markdown text.
     * @return string Concatenated version of consecutive lines.
     */
    private function combineConsecutiveLines(string $line, int $i): string
    {
        if ($i >= count($this->lines) || empty($this->lines[$i]) || $this->getHeaderToken($this->lines[$i])) {
            return $line;
        }
        $line .= ' ' . $this->lines[$i];
        $this->lines[$i] = '';
        return $this->combineConsecutiveLines($line, $i + 1);
    }

    /**
     * Fetch header token within line if it exists.
     * 
     * @param string $line
     * @return Token|null Token if line contains a header. Null if it does not.
     */
    private function getHeaderToken(string $line): ?Token
    {
        $matches = [];
        preg_match('/^#{1,6} .*/U', $line, $matches);
        if (empty($matches)) {
            return null;
        }
        return Token::from(Type::HEADER, $matches[0]);
    }

    /**
     * Gran all inline tokens within a line.
     * 
     * @param string $line
     * @return array Array of Char or Link tokens within the line.
     */
    private function getInlineTokens(string $line): array
    {
        $matches = []; $offset = 0; $tokens = [];
        preg_match_all('/\[.*\]\(.*\)/U', $line, $matches, PREG_OFFSET_CAPTURE);
        foreach ($matches[0] as $match) {
            $tokens[] = Token::from(Type::CHAR, substr($line, $offset, $match[1] - $offset));
	        $tokens[] = Token::from(Type::LINK, substr($line, $match[1], strlen($match[0])));
	        $offset = $match[1] + strlen($match[0]);
        }
        if ($offset < strlen($line)) {
            $tokens[] = Token::from(Type::CHAR, substr($line, $offset, strlen($line) - $offset));
        }
        return $tokens;
    }
}
