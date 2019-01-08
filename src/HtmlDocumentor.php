<?php

namespace Kalyashka\Srcdoc;

use Highlight\Highlighter;

class HtmlDocumentor
{
    protected $headingTag;

    /** @var FileCollector */
    protected $files;
    /** @var  string */
    protected $styleFile;

    public function __construct($headingTag = 'h3')
    {
        if ($headingTag) {
            $this->headingTag = $headingTag;
        }
    }

    /**
     * @param string|resource $outputFile
     */
    public function output($outputFile)
    {
        $f  = is_string($outputFile) ? fopen($outputFile, 'w') : $outputFile;
        $hl = new Highlighter();
        $wl = function ($s) use ($f) {
            fwrite($f, $s . PHP_EOL);
        };
        $wl(<<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
HTML
        );
        if ($this->styleFile && is_readable($this->styleFile)) {
            $wl('<style type="text/css">');
            $wl(file_get_contents($this->styleFile));
            $wl('</style>');
        }
        $wl('</head>');
        $wl('<body>');
        /** @var SourceFile $file */
        foreach ($this->files as $file) {
            $wl(sprintf('<%s>%s</%1$s>', $this->headingTag, $file->getRelativeName()));
            $highlighted = $hl->highlight($file->getExtension(), $file->getContents());
            $wl("<pre class=\"hljs {$highlighted->language}\">");
            $wl($highlighted->value);
            $wl('</pre>');
        }
        $wl('</body>');
        $wl('</html>');
        if (is_string($outputFile)) {
            fclose($f);
        }
    }

    /**
     * @param $styleFile
     *
     * @return HtmlDocumentor
     */
    public function setStyleFile($styleFile): HtmlDocumentor
    {
        $this->styleFile = $styleFile;

        return $this;
    }

    /**
     * @param FileCollector $files Collected files
     *
     * @return HtmlDocumentor
     */
    public function setFiles(FileCollector $files): HtmlDocumentor
    {
        $this->files = $files;

        return $this;
    }
}