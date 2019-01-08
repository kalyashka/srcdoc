<?php

namespace Kalyashka\Srcdoc;

class FileCollector implements \IteratorAggregate
{
    /** @var \ArrayObject|SourceFile[] */
    protected $files;

    /**
     * FileCollector constructor.
     */
    public function __construct()
    {
        $this->files = new \ArrayObject();
    }

    public function collect($path, array $extensions, array $excludes = [])
    {
        $regex    = '/(' . implode('|', array_map(function ($v) {
                return '\.' . $v;
            }, $extensions)) . ')$/i';
        $path     = realpath($path);
        $dir      = new \RecursiveDirectoryIterator($path, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($dir);
        $reg      = new \RegexIterator($iterator, $regex, \RecursiveRegexIterator::GET_MATCH);

        foreach ($reg as $file => $val) {
            $relPath = substr($file, strlen($path) + 1);
            foreach ($excludes as $exclude) {
                if (0 === strncasecmp($relPath, $exclude, strlen($exclude))) {
                    continue 2;
                }
            }

            $this->files->append(new SourceFile($file, $relPath));
        }
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return $this->files->getIterator();
    }

    /**
     * Adds file list
     *
     * @param string[] $list
     * @param string   $path
     */
    public function setFileList(array $list, $path = null)
    {
        $this->files->exchangeArray([]);
        $path = realpath($path ? $path : '') . '/' ;
        foreach ($list as $entry) {
            $fileName = $path . ($relativeName = trim($entry, " \t\n\r\0\x0B\\/"));
            $this->files->append(new SourceFile($fileName, $relativeName));
        }
    }

    /**
     * @return \ArrayObject|SourceFile[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param \ArrayObject $files
     *
     * @return $this
     */
    public function setFiles($files)
    {
        $this->files = $files;

        return $this;
    }

}