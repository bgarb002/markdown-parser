<?php

namespace Tests\Command;

use \Bgarb002\Markdown\Command\DefaultCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DefaultCommandTest extends TestCase
{
    public function testNoFileNorInlineInput()
    {
        $mockInput = $this->getMockForAbstractClass(InputInterface::class);

        $mockInput
            ->expects($this->once())
            ->method('getArgument')
            ->with('markdown')
            ->will($this->returnValue(null));
        
        $mockInput
            ->expects($this->once())
            ->method('getOption')
            ->with('file')
            ->will($this->returnValue(null));
        
        $mockOutput = $this->getMockForAbstractClass(ConsoleOutputInterface::class);
        
        $mockErrorOutput = $this->getMockForAbstractClass(OutputInterface::class);
        
        $mockErrorOutput
            ->expects($this->once())
            ->method('writeln')
            ->will($this->returnValue("Usage:\n command 'Markdown Text'\n command -f /relative/path/to/markdown/file"));
        
        $mockOutput
            ->expects($this->once())
            ->method('getErrorOutput')
            ->will($this->returnValue($mockErrorOutput));

        $command = new DefaultCommand();
        $class = new \ReflectionClass('\Bgarb002\Markdown\Command\DefaultCommand');
        $method = $class->getMethod('execute');
        $method->setAccessible(true);
        $method->invokeArgs($command, [$mockInput, $mockOutput]);
    }

    public function testInlineOption()
    {
        $mockInput = $this->getMockForAbstractClass(InputInterface::class);

        $mockInput
            ->expects($this->once())
            ->method('getArgument')
            ->with('markdown')
            ->will($this->returnValue('This is sample markdown for the [Mailchimp](https://www.mailchimp.com) homework assignment.'));
        
        $mockInput
            ->expects($this->exactly(2))
            ->method('getOption')
            ->withConsecutive(['file'], ['output'])
            ->willReturnOnConsecutiveCalls(null, null);
        
        $mockOutput = $this->getMockForAbstractClass(ConsoleOutputInterface::class);
        
        $mockOutput
            ->expects($this->once())
            ->method('writeln')
            ->with('<p>This is sample markdown for the <a href="https://www.mailchimp.com">Mailchimp</a> homework assignment.</p>');
        
        $command = new DefaultCommand();
        $class = new \ReflectionClass('\Bgarb002\Markdown\Command\DefaultCommand');
        $method = $class->getMethod('execute');
        $method->setAccessible(true);
        $method->invokeArgs($command, [$mockInput, $mockOutput]);
    }

    public function testFileOption()
    {
        $mockInput = $this->getMockForAbstractClass(InputInterface::class);

        $mockInput
            ->expects($this->once())
            ->method('getArgument')
            ->with('markdown')
            ->will($this->returnValue(null));
        
        $mockInput
            ->expects($this->exactly(2))
            ->method('getOption')
            ->withConsecutive(['file'], ['output'])
            ->willReturnOnConsecutiveCalls('test.md', null);
        
        $mockOutput = $this->getMockForAbstractClass(ConsoleOutputInterface::class);

        $mockOutput
            ->expects($this->once())
            ->method('writeln')
            ->with('<h1>Sample Document for <a href="https://www.google.com">Google</a></h1><h3></h3><p>####### </p><p><a href="&lt;a&gt;">[&lt;a&gt;</a></p><p><a href=""></a></p><p><a href="www.google.com"></a></p><p>This the first sentence. This is the second <a href="www.google.com">giant</a> sentence. This is the third with a <a href="www.google.com/sdfsdfasdgfsdfgdfsgvfdsfg">massive link</a>.</p><p>Hello!</p><p>This is sample markdown for the <a href="https://www.mailchimp.com">Mailchimp</a> homework assignment.</p><p>####Not a Header ###Is this a header?</p>');
        
        $command = new DefaultCommand();
        $class = new \ReflectionClass('\Bgarb002\Markdown\Command\DefaultCommand');
        $method = $class->getMethod('execute');
        $method->setAccessible(true);
        $method->invokeArgs($command, [$mockInput, $mockOutput]);
    }
}
