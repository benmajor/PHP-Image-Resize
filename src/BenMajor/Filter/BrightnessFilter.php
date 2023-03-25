<?php

namespace BenMajor\ImageResize\Filter;

use GdImage;

class BrightnessFilter implements Filter
{
    private int $brightness = 100;

    public function __construct(int $brightness = 100)
    {
        $this->brightness = $brightness;
    }

    /**
     * Set the brightness
     *
     * @param integer $brightness
     * @return self
     */
    public function setBrightness(int $brightness): self
    {
        $this->brightness = $brightness;

        return $this;
    }

    /**
     * Get the brightness
     *
     * @return integer
     */
    public function getBrightness(): int
    {
        return $this->brightness;
    }

    /**
     * @inheritDoc
     */
    public function apply(GdImage $image): void
    {
        imagefilter(
            $image,
            IMG_FILTER_BRIGHTNESS,
            $this->brightness
        );
    }
}
