<?php

namespace Bgarb002\Markdown\Command;

use \Bgarb002\Markdown\Parser\Interpreter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DefaultCommand extends Command
{
    /**
     * @var string The default command name.
     */
    protected static $defaultName = 'markdown';

    /**
     * @var string The default command description.
     */
    protected static $defaultDescription = 'Simple Markdown to HTML Converter.';

    /**
     * Argument and Option configuration for the current command.
     */
    protected function configure(): void
    {
        $this
            ->setDefinition(
                new InputDefinition([
                    new InputOption('file', 'f' , InputOption::VALUE_REQUIRED, 'Load markdown from file'),
                    new InputOption('output', 'o' , InputOption::VALUE_REQUIRED, 'Save HTML output to file'),
                    new InputArgument('markdown', InputArgument::OPTIONAL, 'Inline Markdown'),
                ])
            );
    }

    /**
     * Execute the current command.
     * 
     * @param InputInterface $input Representation of command input.
     * @param OutputInterface $output Representation of command output.
     * @return int Command exit status
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inlineMarkdown = $input->getArgument('markdown');
        $inputFile = $input->getOption('file');
        if (is_null($inlineMarkdown) && is_null($inputFile)) {
            $output->getErrorOutput()->writeln("Usage:\n command 'Markdown Text'\n command -f /relative/path/to/markdown/file");
            return Command::INVALID;
        }
        if (!is_null($inputFile)) {
            $inlineMarkdown = @file_get_contents($inputFile);
            if ($inlineMarkdown === false) {
                $output->getErrorOutput()->writeln('Cannot find or unable to open file.');
                return Command::INVALID;
            }
        }
        $html = Interpreter::interpret($inlineMarkdown);
        $outputFile = $input->getOption('output');
        if (is_null($outputFile)) {
            $output->writeln($html, true);
        } else {
            $bytesWritten = @file_put_contents($outputFile, $html);
            if ($bytesWritten === false) {
                $output->getErrorOutput()->writeln('Error occured while trying to write to output file.');
                return Command::INVALID;
            }
        }
        return Command::SUCCESS;
    }
}
