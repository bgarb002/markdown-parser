<?php

namespace Tests\Parser;

use \Bgarb002\Markdown\Parser\Interpreter;
use PHPUnit\Framework\TestCase;

class InterpreterTest extends TestCase
{
    public function testEmpty()
    {
        $html = Interpreter::interpret('');
        $this->assertEquals('<p></p>', $html);
    }

    public function testSingleLineParagraph()
    {
        $html = Interpreter::interpret('This is just a paragraph.');
        $this->assertEquals('<p>This is just a paragraph.</p>', $html);
    }

    public function testSingleLineParagraphWithALink()
    {
        $html = Interpreter::interpret('This is just a paragraph with a [link](http://google.com).');
        $this->assertEquals('<p>This is just a paragraph with a <a href="http://google.com">link</a>.</p>', $html);
    }

    public function testMultiLineParagraphWithLink()
    {
        $html = Interpreter::interpret("This the first sentence.\nThis is the second [link](www.google.com) sentence.\nThis is the third consecutive sentence.");
        $this->assertEquals('<p>This the first sentence. This is the second <a href="www.google.com">link</a> sentence. This is the third consecutive sentence.</p>', $html);
    }

    public function testOneLineLink()
    {
        $html = Interpreter::interpret('[Just an inline link.](http://google.com)');
        $this->assertEquals('<p><a href="http://google.com">Just an inline link.</a></p>', $html);
    }

    public function testOneLineHeaderWithLink()
    {
        $html = Interpreter::interpret('## This is a header [with a link](http://yahoo.com) and extra text.');
        $this->assertEquals('<h2>This is a header <a href="http://yahoo.com">with a link</a> and extra text.</h2>', $html);
    }

    public function testMultipleBlockElementsAtOnce()
    {
        $markdown = str_replace('        ', '', '
        # Header one

        Hello there

        How are you?
        What\'s going on?

        This is a paragraph [with an inline link](http://google.com). Neat, eh?
        ### Another But Smaller Header
        The beginning of the next paragraph that contains a fake header.
        ######## More than six hashes don\'t make a title.

        [[<a>](<a>)');
        $html = implode(['',
            '<h1>Header one</h1>',
            '<p>Hello there</p>',
            '<p>How are you? What&#039;s going on?</p>',
            '<p>This is a paragraph <a href="http://google.com">with an inline link</a>. Neat, eh?</p>',
            '<h3>Another But Smaller Header</h3>',
            '<p>The beginning of the next paragraph that contains a fake header. ######## More than six hashes don&#039;t make a title.</p>',
            '<p><a href="&lt;a&gt;">[&lt;a&gt;</a></p>',
        ]);
        $this->assertEquals($html, Interpreter::interpret($markdown));
    }
}
