# Srcdoc

A command line utility for collecting and pasting source code in html document.
Uses [scrivo/highlight.php](https://github.com/scrivo/highlight.php) for syntax highlighting.

## Installation

```bash
composer global require kalyashka/srcdoc
```

## Usage
```bash
srcdoc [options] [<directory>]
```
where
`<directory>` - root directory with source files (defaults to current directory)
`options`:
* `--help, -h` - show help
* `--extensions, -e` - file extensions (comma separated, "php,js,css,scss")
* `--exclude, -x` - files/directories patterns for exclude (e.g. "vendor,assets", comma separated)
* `--output, -o` - output file name (stdout will be used if not provided)
* `--list, -l` - do not scan directory and use listing file (one file per line)
* `--no-syntax, -s` - disable syntax highlighting
* `--theme, t` - use one of highlight.php theme
* `--theme-list` - outputs list of highlight.php themes
* `--heading` - heading tag (default h3)

### Example
```bash
srcdoc -e vendor -x "php,js,css" --heading h2 --theme idea -o doc.html
```