<?php

namespace BenMajor\ImageResize\Model;

class BoundingBox
{
    private int $width;
    private int $height;
    private int $yOffset;

    public function __construct(int $width, int $height, int $yOffset)
    {
        $this->width = $width;
        $this->height = $height;
        $this->yOffset = $yOffset;
    }

    /**
     * Get the width
     *
     * @return integer
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * Get the height
     *
     * @return integer
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Get the Y offset
     *
     * @return integer
     */
    public function getYOffset(): int
    {
        return $this->yOffset;
    }
}
