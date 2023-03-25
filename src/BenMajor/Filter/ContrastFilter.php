<?php

namespace BenMajor\ImageResize\Filter;

use GdImage;

class ContrastFilter implements Filter
{
    private int $contrast = 100;

    public function __construct(int $brightness = 100)
    {
        $this->contrast = $brightness;
    }

    /**
     * Set the contrast
     *
     * @param integer $contrast
     * @return self
     */
    public function setContrast(int $contrast): self
    {
        $this->contrast = $contrast;

        return $this;
    }

    /**
     * Get the contrast
     *
     * @return integer
     */
    public function getContrast(): int
    {
        return $this->contrast;
    }

    /**
     * @inheritDoc
     */
    public function apply(GdImage $image): void
    {
        imagefilter(
            $image,
            IMG_FILTER_CONTRAST,
            $this->contrast
        );
    }
}
