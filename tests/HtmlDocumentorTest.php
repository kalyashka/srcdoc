<?php

class HtmlDocumentorTest extends \PHPUnit\Framework\TestCase
{
    const styleFile = 'vendor/scrivo/highlight.php/styles/idea.css';

    public function testOutput()
    {
        $fc = new \Kalyashka\Srcdoc\FileCollector();
        $fc->setFileList([
            'file1.php',
            'dir1/file2.php',
            'dir2/file1.js',
        ], 'tests/data');
        $doc = new \Kalyashka\Srcdoc\HtmlDocumentor('h3');
        $doc->setFiles($fc);
        $doc->setStyleFile(self::styleFile);
        $fd = fopen('php://memory', 'w+');
        $doc->output($fd);
        rewind($fd);
        $output = stream_get_contents($fd);

        $this->assertContains('.hljs {', $output);
        $this->assertContains('<h3>file1.php</h3>', $output);
        $this->assertContains('// Code here', $output);

        $tempFile = tempnam(sys_get_temp_dir(), '');
        $doc->output($tempFile);
        $this->assertSame($output, file_get_contents($tempFile));
        unlink($tempFile);
    }
}