<?php

namespace Silpion\Depicter\Report;

class File
{

    protected $files = array();

    // Unused so far
    protected $metrics = array();

    // Unused so far
    protected $codeElements = array();

    public static function createFromFile($file)
    {
        if (!is_file($file) || !is_readable($file)) {
            throw new \InvalidArgumentException("Unable to read file '$file'");
        }
        $json = file_get_contents($file);
        $data = json_decode($json, true);

        $instance = new self();

        foreach ($data['files'] as $file) {
            $instance->files[$file['path']] = $file;
        }

        $instance->metrics = $data['metrics'];

        $instance->codeElements = $data['code_elements'];

        return $instance;
    }

    /**
     * Return a list of all file paths.
     *
     * @return string[]
     */
    public function getFiles()
    {
        return array_keys($this->files);
    }

    /**
     * Return the entry for the current file.
     *
     * @param $path
     * @return mixed
     */
    public function getFile($path)
    {
        return $this->files[$path];
    }
}
 