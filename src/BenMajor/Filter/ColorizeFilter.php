<?php

namespace BenMajor\ImageResize\Filter;

use BenMajor\ImageResize\Model\Colourspace\RGB;
use GdImage;

class ColorizeFilter implements Filter
{
    private RGB $color;

    public function __construct(string $color)
    {
        $this->color = RGB::fromHex($color);
    }

    /**
     * Set the color
     *
     * @param string|RGB $contrast
     * @return self
     */
    public function setColor($color): self
    {
        $this->color = ($color instanceof RGB)
            ? $color
            : RGB::fromHex($color);

        return $this;
    }

    /**
     * Get the color
     *
     * @return RGB
     */
    public function getColor(): RGB
    {
        return $this->color;
    }

    /**
     * @inheritDoc
     */
    public function apply(GdImage $image): void
    {
        imagefilter(
            $image,
            IMG_FILTER_COLORIZE,
            $this->color->getRed(),
            $this->color->getGreen(),
            $this->color->getBlue(),
        );
    }
}
