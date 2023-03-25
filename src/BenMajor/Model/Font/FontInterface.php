<?php

namespace BenMajor\ImageResize\Model\Font;

interface FontInterface
{
    /**
     * Set the font size
     *
     * @param integer $size
     * @return self
     */
    public function setSize(int $size): self;

    /**
     * Get the font size
     *
     * @return integer
     */
    public function getSize(): int;

    /**
     * Get the font name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the contents of the file
     *
     * @return string
     */
    public function getFile(): string;
}
