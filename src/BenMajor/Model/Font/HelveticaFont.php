<?php

namespace BenMajor\ImageResize\Model\Font;

class HelveticaFont implements FontInterface
{
    public const FONT_NAME = 'sans';
    private int $size;
    private string $file;

    public function __construct(int $size = 12)
    {
        $this->size = $size;
        $this->file = __DIR__.'/../../../fonts/helvetica.ttf';
    }

    /**
     * @inheritDoc
     */
    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return self::FONT_NAME;
    }

    /**
     * @inheritDoc
     */
    public function getFile(): string
    {
        return $this->file;
    }
}
