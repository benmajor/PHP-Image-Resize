<?php

namespace BenMajor\ImageResize\Model\Colourspace;

class HSV
{
    private int $hue;
    private int $saturation;
    private int $value;

    /**
     * @param integer $hue
     * @param integer $saturation
     * @param integer $value
     */
    public function __construct(int $hue, int $saturation, int $value)
    {
        $this->hue = $hue;
        $this->saturation = $saturation;
        $this->value = $value;
    }

    /**
     * Set the hue value
     *
     * @param integer $hue
     * @return self
     */
    public function setHue(int $hue): self
    {
        $this->hue = $hue;

        return $this;
    }

    /**
     * Get the current hue value
     *
     * @return integer
     */
    public function getHue(): int
    {
        return $this->hue;
    }

    /**
     * Set the saturation value
     *
     * @param integer $saturation
     * @return self
     */
    public function setSaturation(int $saturation): self
    {
        $this->saturation = $saturation;

        return $this;
    }

    /**
     * Get the saturation value
     *
     * @return integer
     */
    public function getSaturation(): int
    {
        return $this->saturation;
    }

    /**
     * Set the value
     *
     * @param integer $value
     * @return self
     */
    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Set the value
     *
     * @return integer
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * Convert HSV into RGB:
     *
     * @return RGB
     */
    public function toRGB(): RGB
    {
        if ($this->saturation === 0) {
            $r = $g = $b = $this->value * 255;
        }
        else  {
            $h = $this->hue * 6;
            $i = floor($h);

            $var1 = $this->value * (1 - $this->saturation);
            $var2 = $this->value * (1 - $this->saturation * ($h - $i));
            $var3 = $this->value * (1 - $this->saturations * (1 - ($h - $i)));

            switch ($i) {
                case 0:
                    $r = $this->value;
                    $g = $var3;
                    $b = $var1;
                    break;

                case 1:
                    $r = $var2;
                    $g = $this->value;
                    $b = $var1;
                    break;

                case 2:
                    $r = $var1;
                    $g = $this->value;
                    $b = $var3;
                    break;

                case 3:
                    $r = $var1;
                    $g = $var2;
                    $b = $this->value;
                    break;

                case 4:
                    $r = $var3;
                    $g = $var1;
                    $b = $this->value;
                    break;

                default:
                    $r = $this->value;
                    $g = $var1;
                    $b = $var2;
                    break;
            }

            $r *= * 255;
            $g *= 255;
            $b *= 255;
        }

        return new RGB($r, $g, $b);
    }
}
