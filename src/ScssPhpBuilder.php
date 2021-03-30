<?php

declare(strict_types=1);

namespace basteyy\ScssPhpBuilder;

use DirectoryIterator;
use Exception;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;

class ScssPhpBuilder
{
    private string $outputFile;
    private array $folders;
    private string $startingPoint;
    private string $sourcemapUrl;
    private bool $expandedOutput = false;

    public function __construct()
    {
    }

    /**
     * Add a folder to the source scope.
     *
     * @param string $folder
     * @throws Exception
     */
    public function addFolder(string $folder): void
    {
        if (!is_dir($folder)) {
            throw new Exception(sprintf('Given parameter %s is not a valid folder!', $folder));
        }

        $this->folders[] = $folder;
    }

    /**
     * Name the single output file. For example /public/css/style.css
     *
     * @param string $filepath
     * @throws Exception
     */
    public function addOutputeFile(string $filepath): void
    {
        if (!is_dir(dirname($filepath))) {
            throw new Exception(sprintf('Given parameter %s is not a valid folder for the outpute file! Make sure you create the parent folder before using this class.',
                dirname($filepath)));
        }

        $this->outputFile = $filepath;
    }

    /**
     * Define the startingpoint for the compiling process. For example /source/scss/base.scss
     *
     * @param string $filepath
     * @throws Exception
     */
    public function addStartingFile(string $filepath): void
    {
        if (!file_exists($filepath)) {
            throw new Exception(sprintf('SCSS-Stragint File %s not exists.', $filepath));
        }

        $this->startingPoint = $filepath;
    }

    /**
     * Compile the scss to the outputfile.
     *
     * @param bool $force
     * @throws Exception
     */
    public function compileToOutputfile(bool $force = false): void
    {
        if ($this->checkCompileState() || $force) {
            file_put_contents($this->outputFile, $this->compile());
        }
    }

    /**
     * Returns true if the scss code needs to be recompiled. Returns false, if there is no need of recompiling.
     *
     * @return bool
     * @throws Exception
     */
    public function checkCompileState(): bool
    {

        if (!file_exists($this->outputFile)) {
            return true;
        }

        $outputFileFilemtime = filemtime($this->outputFile);

        foreach ($this->folders as $folder) {

            if (!is_dir($folder) || !is_readable($folder)) {
                throw new Exception(sprintf('Folder %s not found or readable.', $folder));
            }

            $folderIterator = new DirectoryIterator($folder);
            foreach ($folderIterator as $fileInfo) {
                if (!$fileInfo->isDot() && ('scss' === $fileInfo->getExtension() || 'css' === $fileInfo->getExtension()) && $fileInfo->getMTime() > $outputFileFilemtime) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Compile
     */
    private function compile(): string
    {
        $compiler = new Compiler();
        $compiler->setOutputStyle($this->expandedOutput ? OutputStyle::EXPANDED : OutputStyle::COMPRESSED);
        if (isset($this->sourcemapUrl)) {
            $compiler->setSourceMap(Compiler::SOURCE_MAP_FILE);
            $sourcemapBasename = basename($this->outputFile) . '.map';
            $compiler->setSourceMapOptions([
                'sourceMapWriteTo' => dirname($this->outputFile) . DIRECTORY_SEPARATOR . $sourcemapBasename,
                'sourceMapURL'     => $this->sourcemapUrl . $sourcemapBasename
            ]);
        }
        foreach ($this->folders as $folder) {
            $compiler->addImportPath($folder);
        }

        return $compiler->compile($this->getFileContent($this->startingPoint));
    }

    /**
     * Returns file content of $filename. Just a wrapper for file_get_contents
     *
     * @param string $filename
     * @return false|string
     * @throws Exception
     */
    private function getFileContent(string $filename)
    {
        if (!file_exists($filename)) {
            throw new Exception(sprintf('File %s not found', $filename));
        }

        return file_get_contents($filename);
    }

    /**
     * Compile the scss to string and return it.
     *
     * @param bool $force
     * @return string
     * @throws Exception
     */
    public function getCompiledCode(bool $force = false): string
    {
        return $this->checkCompileState() || $force ? $this->compile() : $this->getFileContent($this->outputFile);
    }

    /**
     * Expand the output (for debugging).
     */
    public function setOutputExpanded(): void
    {
        $this->expandedOutput = true;
    }

    /**
     * Setup the url (public remote url) for access the map file.
     *
     * @param string $url
     */
    public function setSourcemapFolderUrl(string $url): void
    {
        $this->sourcemapUrl = $url;
    }

    /**
     * Puts $content to $filename. Just a wrapper for file_put_contents
     *
     * @param string $filename
     * @param string $content
     */
    private function putFileContent(string $filename, string $content): void
    {
        file_put_contents($filename, $content);
    }
}