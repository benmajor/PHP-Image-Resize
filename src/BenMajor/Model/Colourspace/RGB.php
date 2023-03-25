<?php

namespace BenMajor\ImageResize\Model\Colourspace;

use BenMajor\ImageResize\Exception\InvalidHexStringException;

class RGB
{
    private int $red;
    private int $green;
    private int $blue;

    /**
     *
     * @param integer $red
     * @param integer $green
     * @param integer $blue
     */
    public function __construct(int $red, int $green, int $blue)
    {
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;
    }

    /**
     * Set the red value
     *
     * @param integer $red
     * @return self
     */
    public function setRed(int $red): self
    {
        $this->red = $red;

        return $this;
    }

    /**
     * Get the red value
     *
     * @return integer
     */
    public function getRed(): int
    {
        return $this->red;
    }

    /**
     * Set the green value
     *
     * @param integer $green
     * @return self
     */
    public function setGeen(int $green): self
    {
        $this->green = $green;

        return $this;
    }

    /**
     * Get the green value
     *
     * @return integer
     */
    public function getGreen(): int
    {
        return $this->green;
    }

    /**
     * Set the blue value
     *
     * @param integer $blue
     * @return self
     */
    public function setBlue(int $blue): self
    {
        $this->blue = $blue;

        return $this;
    }

    /**
     * Get the blue value
     *
     * @return integer
     */
    public function getBlue(): int
    {
        return $this->blue;
    }

    /**
     * Convert a hex string into RGB
     *
     * @param string $hex
     * @return self
     * @throws InvalidHexStringException
     */
    public static function fromHex(string $hex): self
    {
        if (substr($hex, 0, 1) === '#') {
            $hex = substr($hex, 1);
        }

        $length = strlen($hex);

        if ($length !== 6 && $length !== 3) {
            throw new InvalidHexStringException();
        }

        if (strlen($hex) === 6) {
            $r = substr($hex, 0, 2);
            $g = substr($hex, 2, 2);
            $b = substr($hex, 4, 2);
        }
        else {
            $r = str_repeat(substr($hex, 0, 1), 2);
            $g = str_repeat(substr($hex, 1, 1), 2);
            $b = str_repeat(substr($hex, 2, 1), 2);
        }

        return new self(
            hexdec($r),
            hexdec($g),
            hexdec($b)
        );
    }

    /**
     * Convert RGB to HSV
     *
     * @return HSV
     */
    public function toHSV(): HSV
    {
        $r = ($this->red / 255);
        $g = ($this->green / 255);
        $b = ($this->blue / 255);

        $rgbMin = min($r, $g, $b);
        $rgbMax = max($r, $g, $b);
        $chroma = $rgbMax - $rgbMin;
        $v = $rgbMax;

        if ($chroma === 0) {
            $h = 0;
            $s = 0;
        }
        else
        {
            $s = $chroma / $rgbMax;

            $chromaR = ((($rgbMax - $r) / 6) + ($chroma / 2)) / $chroma;
            $chromaG = ((($rgbMax - $g ) / 6) + ($chroma / 2)) / $chroma;
            $chromaB = ((($rgbMax - $b) / 6) + ($chroma / 2)) / $chroma;

            if ($r === $rgbMax) {
                $h = $chromaB - $chromaG;
            }
            elseif ($g === $rgbMax) {
                $h = ( 1 / 3 ) + $chromaR - $chromaB;
            }
            elseif ($b === $rgbMax) {
                $h = ( 2 / 3 ) + $chromaG - $chromaR;
            }

            if ($h < 0) {
                $h++;
            }

            if ($h > 1) {
                $h--;
            }
        }

        return new HSV($h, $s, $v);
    }
}
