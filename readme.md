# Simple markdown to html parser

This is a simple command line application intended to convert a small subset of markdown to HTML.

## Formatting Specifics

Markdown is a fairly rich specification. For the sake of simplicity and based 
on the goals described, this application will process the following content:

| Markdown                               | HTML                                              |
| -------------------------------------- | ------------------------------------------------- |
| `# Heading 1`                          | `<h1>Heading 1</h1>`                              | 
| `## Heading 2`                         | `<h2>Heading 2</h2>`                              | 
| `...`                                  | `...`                                             | 
| `###### Heading 6`                     | `<h6>Heading 6</h6>`                              | 
| `Unformatted text`                     | `<p>Unformatted text</p>`                         | 
| `[Link text](https://www.example.com)` | `<a href="https://www.example.com">Link text</a>` | 
| `Blank line`                           | `Ignored`                                         | 

## Requirements
- PHP >8.1
- Composer CLI

## Command line application

In order to get up and running with this project, simply install the composer 
dependencies within the project directory.

```bash
composer install
```

This will install the required packages to operate the program via CLI that will be accessible executing one of the following commands:

```bash
./bin/markdown '## Markdown Text'

./bin/markdown -f /relative/path/to/markdown/file.md
```

The console application allows you to either enter inline markdown
or specify a path to a plain text file you would like to process.

## Usage
```bash
./bin/markdown --help
```
--help option will outout the following:
```bash
markdown [options] [--] [<markdown>]

Arguments:
  markdown              Inline Markdown

Options:
  -f, --file=FILE       Load markdown from file
  -o, --output=OUTPUT   Save HTML output to file
  -h, --help            Display help for the given command. When no command is given display help for the markdown command
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
```
