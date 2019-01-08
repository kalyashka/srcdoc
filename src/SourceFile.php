<?php

namespace Kalyashka\Srcdoc;

class SourceFile
{
    /** @var string File name */
    protected $fileName;
    /** @var string Relative name */
    protected $relativeName;

    public function __construct(string $fileName, ?string $relativeName = null)
    {
        if (!is_readable($fileName)) {
            throw new \InvalidArgumentException('File not found: "' . $fileName . '"');
        }
        $this->fileName     = $fileName;
        $this->relativeName = $relativeName;
    }

    public function getExtension(): string
    {
        return pathinfo($this->fileName, PATHINFO_EXTENSION);
    }

    public function getMime(): string
    {
        return mime_content_type($this->fileName);
    }

    public function getContents(): string
    {
        return file_get_contents($this->fileName);
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     *
     * @return SourceFile
     */
    public function setFileName(string $fileName): SourceFile
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @return string
     */
    public function getRelativeName(): string
    {
        return $this->relativeName ?: $this->fileName;
    }

    /**
     * @param string $relativeName
     *
     * @return SourceFile
     */
    public function setRelativeName(string $relativeName): SourceFile
    {
        $this->relativeName = $relativeName;

        return $this;
    }
}