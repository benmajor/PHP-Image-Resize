<?php

namespace BenMajor\ImageResize\Model\Component;

use BenMajor\ImageResize\Model\Colourspace\RGB;
use GdImage;

class Border
{
    private ?int $width;
    private ?RGB $color;

    public function __construct(?int $width = null, ?string $hex = null)
    {
        $this->width = $width;

        if ($hex !== null) {
            $this->color = RGB::fromHex($hex);
        }
    }

    /**
     * Set the border width (px)
     *
     * @param integer $width
     * @return self
     */
    public function setWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get the border width (px)
     *
     * @return integer|null
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * Set the colour of the border
     *
     * @param string|RGB $color
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
     * Get the color of the border
     *
     * @return RGB|null
     */
    public function getColor(): ?RGB
    {
        return $this->color;
    }

    /**
     * Apply the border to the specified image
     *
     * @param $image
     * @return void
     */
    public function addToImage($image): void
    {
        if ($this->getWidth() > 0 && $this->getColor() !== null) {
            $x1 = 0;
            $y1 = 0;
            $x2 = imagesx($image) - 1;
            $y2 = imagesy($image) - 1;

            for ($i = 0; $i < $this->getWidth(); $i++) {
                imagerectangle(
                    $image,
                    $x1++,
                    $y1++,
                    $x2--,
                    $y2--,
                    imagecolorallocate(
                        $image,
                        $this->getColor()->getRed(),
                        $this->getColor()->getGreen(),
                        $this->getColor()->getBlue()
                    )
                );
            }
        }
    }
}
