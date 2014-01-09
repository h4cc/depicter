<?php

namespace Silpion\Depicter\Report;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class Generator
{
    /**
     * @var File
     */
    private $file;
    private $target;
    private $source;
    private $highlighter;

    public function generateReport(File $file, $target, $source)
    {
        $this->file = $file;
        $this->target = rtrim($target, "/");
        $this->source = rtrim($source, "/");

        if (!is_dir($this->target)) {
            throw new \RuntimeException("Target path is not a readable directory: '" . $this->target . "'");
        }

        if (!is_dir($this->source)) {
            throw new \RuntimeException("Target path is not a readable directory: '" . $this->source . "'");
        }

        $this->highlighter = new \FSHL\Highlighter(
            new \FSHL\Output\Html(),
          \FSHL\Highlighter::OPTION_TAB_INDENT | \FSHL\Highlighter::OPTION_LINE_COUNTER
        );
        $this->highlighter->setLexer(new \FSHL\Lexer\Php());

        $this->generate();
    }

    protected function generate()
    {

        // Fetch all needed Data
        $data['files'] = $this->getFileData();

        // Render Template with given data
        $content = $this->renderTemplate($data);

        // Write template
        file_put_contents($this->target . '/index.html', $content);

        // Copy static files
        $fs = new Filesystem();
        $finder = new Finder();
        $finder->in(__DIR__ . '/../Resources/public/');
        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($finder->files() as $file) {
            $fs->copy($file, $this->target . '/' . $file->getFilename(), true);
        }
    }

    protected function renderTemplate(array $content)
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../Resources/views/');
        $twig = new \Twig_Environment($loader, array('debug' => true, 'strict_variables' => true));
        $twig->addExtension(new \Twig_Extension_Debug());

        // Use only files with comments.
        $errorFiles = array_filter(
            $content['files'],
            function (array $file) {
                return count($file['comments']) > 0;
            }
        );

        // Count the total number of comments.
        $numberComments = array_reduce(
            $content['files'],
            function ($count, array $file) {
                return $count + count($file['comments']);
            },
            0
        );
        return $twig->render(
            'report.html',
            array(
                'files' => $errorFiles,
                'numberComments' => $numberComments
            )
        );
    }

    protected function getFileData()
    {
        // Fetch from each file the corosponding part of the message.
        $data = array();

        foreach ($this->file->getFiles() as $path) {
            $file = $this->file->getFile($path);

            // Only selected values.
            $data[$path]['path'] = $path;

            // A unique random id.
            $data[$path]['id'] = md5($path);

            // We need the comments.
            $data[$path]['comments'] = $file['comments'];
            $commentedLineNumbers = array_keys($file['comments']);

            // Attach highlighted source.
            $source = $this->getSource($path);
            $source = $this->highlighter->highlight($source);

            // Remove lines from source, that are not relevant.
            $data[$path]['source'] = $this->removeNotNeededLines($source, $commentedLineNumbers);
        }

        return $data;
    }

    protected function removeNotNeededLines($source, array $lineNumbers)
    {
        $lines = explode("\n", $source);
        $linesCount = count($lines);

        $parts = array();

        foreach ($lineNumbers as $number) {
            $linePre = max(0, $number - 5);
            $linePost = min($linesCount, $number + 5);

            $parts[] = array_slice($lines, $linePre, $linePost - $linePre, true);
        }

        $source = array();

        // Merge Lines
        foreach ($parts as $part) {
            foreach ($part as $number => $line) {
                // Correct the offset, that occured by explode().
                $source[$number + 1] = $line;
            }
        }

        return $source;
    }

    protected function getSource($path)
    {
        $path = $this->source . '/' . $path;
        if (!is_file($path) || !is_readable($path)) {
            throw new \RuntimeException("Path '$path' does not exist or ist nor readable.");
        }

        return file_get_contents($path);
    }
}
 