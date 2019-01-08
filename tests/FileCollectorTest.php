<?php

use Kalyashka\Srcdoc\FileCollector;

class FileCollectorTest extends \PHPUnit\Framework\TestCase
{
    /** @var FileCollector */
    protected $fc;

    public function setUp()
    {
        $this->fc = new FileCollector();
    }

    public function testCollect()
    {
        $this->fc->collect('tests/data', ['php', 'js']);
        $files = $this->getRelativeFiles();
        $this->assertSame($files, [
            'file1.php',
            'dir1/file2.php',
            'dir2/file1.js',
        ]);
    }

    public function testCollectExclude()
    {
        $this->fc->collect('tests/data', ['php', 'js'], ['dir2']);
        $files = $this->getRelativeFiles();
        $this->assertSame($files, [
            'file1.php',
            'dir1/file2.php',
        ]);
    }

    public function testNonExistentFile()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->fc->setFileList(['tests/data/nonexistent.php']);
    }

    public function testList()
    {
        $this->fc->setFileList([
            'dir1/file2.php',
            'file1.php'
        ], 'tests/data');
        $files = $this->getRelativeFiles();
        $this->assertSame($files, [
            'dir1/file2.php',
            'file1.php',
        ]);
    }

    public function testIterator()
    {
        $this->assertTrue($this->fc->getIterator() instanceof Traversable);
    }

    public function testSetFiles()
    {
        $file = new \Kalyashka\Srcdoc\SourceFile('tests/data/file1.php');
        $this->fc->setFiles(new ArrayObject([$file]));
        $this->assertSame($file, $this->fc->getFiles()[0]);
    }

    protected function getRelativeFiles()
    {
        return array_map(function ($i) {
            return $i->getRelativeName();
        }, iterator_to_array($this->fc->getFiles()));
    }
}