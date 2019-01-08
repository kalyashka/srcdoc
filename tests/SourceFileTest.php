<?php

use Kalyashka\Srcdoc\SourceFile;

class SourceFileTest extends \PHPUnit\Framework\TestCase
{
    public function testNonExistent()
    {
        $this->expectException(InvalidArgumentException::class);
        new SourceFile('tests/data/nonexistent.php');
    }

    public function testExtension()
    {
        $sf = new SourceFile('tests/data/file1.php');
        $this->assertSame($sf->getExtension(), 'php');
    }

    public function testMime()
    {
        $sf = new SourceFile('tests/data/file1.php');

        $this->assertSame($sf->getMime(), 'text/x-php');
    }

    public function testContent()
    {
        $sf = new SourceFile('tests/data/file1.php');
        $this->assertContains('// Code here', $sf->getContents());
    }

    public function testName()
    {
        $sf = new SourceFile('tests/data/file1.php');
        $this->assertSame($sf->getFileName(), 'tests/data/file1.php');
        $sf->setFileName('tests/data/file2.php');
        $this->assertSame($sf->getFileName(), 'tests/data/file2.php');
    }

    public function testRelative()
    {
        $sf = new SourceFile('tests/data/file1.php');
        $this->assertSame($sf->getRelativeName(), 'tests/data/file1.php');
        $sf->setRelativeName('file1.php');
        $this->assertSame($sf->getRelativeName(), 'file1.php');
    }
}