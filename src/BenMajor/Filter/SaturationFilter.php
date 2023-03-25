<?php

namespace BenMajor\ImageResize\Filter;

use BenMajor\ImageResize\Model\Colourspace\RGB;
use GdImage;

class SaturationFilter implements Filter
{
    private float $saturation;

    public function __construct(float $saturation)
    {
        $this->saturation = $saturation;
    }

    /**
     * Set the saturation level
     *
     * @param float $saturation
     * @return self
     */
    public function setSaturation(float $saturation): self
    {
        $this->saturation = $saturation;

        return $this;
    }

    /**
     * Get the saturation level
     *
     * @return float
     */
    public function getSaturation(): float
    {
        return $this->saturation;
    }

    /**
     * @inheritDoc
     */
    public function apply(GdImage $image): void
    {
        $width  = imagesx($image);
        $height = imagesy($image);

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $color = imagecolorat($image, $x, $y);

                $rgb = new RGB(
                    ($color >> 16) & 0xFF,
                    ($color >> 8) & 0xFF,
                    ($color & 0xFF)
                );

                $alpha = ($color & 0xFF000000) >> 24;

                $hsv = $rgb->toHSV();
                $saturation = $hsv->getSaturation();

                $saturation *= $this->saturation;

                if ($saturation > 1) {
                    $saturation = 1;
                }

                $hsv->setSaturation($saturation);
                $rgb = $hsv->toRGB();

                // Assign the pixels:
                imagesetpixel(
                    $image,
                    $x,
                    $y,
                    imagecolorallocatealpha(
                        $image,
                        $rgb->getRed(),
                        $rgb->getGreen(),
                        $rgb->getBlue(),
                        $alpha
                    )
                );
            }
        }
    }
}
