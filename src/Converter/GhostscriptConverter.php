<?php

namespace Xthiago\PDFVersionConverter\Converter;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Converter that uses ghostscript to change PDF version.
 *
 * @author Thiago Rodrigues <xthiago@gmail.com>
 */
class GhostscriptConverter implements ConverterInterface
{
    public function __construct(
        protected GhostscriptConverterCommand $command,
        protected Filesystem $fs,
        protected ?string $tmp = null
    ) {
        $this->tmp = $tmp ?: sys_get_temp_dir();
    }

    /**
     * Generates a unique absolute path for tmp file.
     *
     * @return string absolute path
     */
    protected function generateAbsolutePathOfTmpFile(): string
    {
        return $this->tmp . '/' . uniqid('pdf_version_changer_') . '.pdf';
    }

    /**
     * {@inheritdoc }
     */
    public function convert($file, $newVersion): void
    {
        $tmpFile = $this->generateAbsolutePathOfTmpFile();

        $this->command->run($file, $tmpFile, $newVersion);

        if (!$this->fs->exists($tmpFile)) {
            throw new \RuntimeException("The generated file '{$tmpFile}' was not found.");
        }

        $this->fs->copy($tmpFile, $file, true);
        $this->fs->remove($tmpFile);
    }
}
