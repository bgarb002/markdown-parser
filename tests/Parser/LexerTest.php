<?php

namespace Tests\Parser;

use \Bgarb002\Markdown\Parser\Lexer;
use \Bgarb002\Markdown\Parser\TokenType;
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{
    public function testEmpty()
    {
        $lexer = new Lexer('');
        $token = $lexer->tokenGenerator()->current();
        $this->assertEquals(TokenType::EOF, $token->type());
    }

    public function testOneLineHeaderWithLink()
    {
        $generator = (new Lexer('## This is a header [with a link](http://yahoo.com) and extra text.'))->tokenGenerator();
        $token = $generator->current();
        $this->assertEquals(TokenType::HEADER, $token->type());
        $this->assertEquals('## ', $token->value());

        $generator->next();
        $token = $generator->current();
        $this->assertEquals(TokenType::CHAR, $token->type());
        $this->assertEquals('This is a header ', $token->value());

        $generator->next();
        $token = $generator->current();
        $this->assertEquals(TokenType::LINK, $token->type());
        $this->assertEquals('[with a link](http://yahoo.com)', $token->value());

        $generator->next();
        $token = $generator->current();
        $this->assertEquals(TokenType::CHAR, $token->type());
        $this->assertEquals(' and extra text.', $token->value());

        $generator->next();
        $token = $generator->current();
        $this->assertEquals(TokenType::NEWLINE, $token->type());
        $this->assertEquals("\n", $token->value());

        $generator->next();
        $token = $generator->current();
        $this->assertEquals(TokenType::EOF, $token->type());
    }

    public function testHeaderFollowedByMultiLineParagraph()
    {
        $text = "# Sample Document\n\nThis the first sentence.\nThis is the second [link](www.google.com) sentence.\nThis is the third consecutive sentence.";
        $generator = (new Lexer($text))->tokenGenerator();
        $token = $generator->current();
        $this->assertEquals(TokenType::HEADER, $token->type());
        $this->assertEquals('# ', $token->value());

        $generator->next();
        $token = $generator->current();
        $this->assertEquals(TokenType::CHAR, $token->type());
        $this->assertEquals('Sample Document', $token->value());

        $generator->next();
        $token = $generator->current();
        $this->assertEquals(TokenType::NEWLINE, $token->type());
        $this->assertEquals("\n", $token->value());

        $generator->next();
        $token = $generator->current();
        $this->assertEquals(TokenType::CHAR, $token->type());
        $this->assertEquals('This the first sentence. This is the second ', $token->value());

        $generator->next();
        $token = $generator->current();
        $this->assertEquals(TokenType::LINK, $token->type());
        $this->assertEquals('[link](www.google.com)', $token->value());

        $generator->next();
        $token = $generator->current();
        $this->assertEquals(TokenType::CHAR, $token->type());
        $this->assertEquals(' sentence. This is the third consecutive sentence.', $token->value());

        $generator->next();
        $token = $generator->current();
        $this->assertEquals(TokenType::NEWLINE, $token->type());
        $this->assertEquals("\n", $token->value());

        $generator->next();
        $token = $generator->current();
        $this->assertEquals(TokenType::EOF, $token->type());
    }

    public function testHeaderWithBrandNewLineAfterIt()
    {
        $text = "###### Header Six\nThis should be a paragraph.";
        $generator = (new Lexer($text))->tokenGenerator();
        $token = $generator->current();
        $this->assertEquals(TokenType::HEADER, $token->type());
        $this->assertEquals('###### ', $token->value());

        $generator->next();
        $token = $generator->current();
        $this->assertEquals(TokenType::CHAR, $token->type());
        $this->assertEquals('Header Six', $token->value());

        $generator->next();
        $token = $generator->current();
        $this->assertEquals(TokenType::NEWLINE, $token->type());
        $this->assertEquals("\n", $token->value());

        $generator->next();
        $token = $generator->current();
        $this->assertEquals(TokenType::CHAR, $token->type());
        $this->assertEquals('This should be a paragraph.', $token->value());
    }

    public function testMoreThanSixConsecutiveHastags()
    {
        $text = '####### ';
        $generator = (new Lexer($text))->tokenGenerator();
        $token = $generator->current();
        $this->assertEquals(TokenType::CHAR, $token->type());
        $this->assertEquals('####### ', $token->value());
    }
}
