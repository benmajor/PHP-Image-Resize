<?php

namespace BenMajor\ImageResize\Filter;

use GdImage;

class BlurFilter implements Filter
{
    private int $radius;

    public function __construct(int $radius)
    {
        $this->radius = $radius;
    }

    /**
     * Set the blur radius
     *
     * @param integer $radius
     * @return self
     */
    public function setRadius(int $radius): self
    {
        $this->radius = $radius;

        return $this;
    }

    /**
     * Get the blur radius
     *
     * @return integer
     */
    public function getRadius(): int
    {
        return $this->radius;
    }

    /**
     * @inheritDoc
     */
    public function apply(GdImage $image): void
    {
        for ($i = 0; $i < $this->getRadius(); $i++) {
            imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
        }
    }
}
