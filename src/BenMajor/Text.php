<?php

namespace BenMajor\ImageResize;

use BenMajor\ImageResize\Model\BoundingBox;
use BenMajor\ImageResize\Model\Font\FontInterface;
use BenMajor\ImageResize\Model\Colourspace\RGB;
use BenMajor\ImageResize\Model\Font\HelveticaFont;
use GdImage;
use InvalidArgumentException;

class Text
{
    private string $text;
    private RGB $color;
    private RGB $backgroundColor;
    private int $backgroundOpacity;
    private int $rotation;
    private int $padding;
    private string $position;
    private string|int $width;
    private string $textAlign;

    private ?FontInterface $font;

    // Alignment constants:
    public const ALIGN_LEFT = 'left';
    public const ALIGN_CENTER = 'center';
    public const ALIGN_RIGHT = 'right';

    // Position constants:
    public const POSITION_TOP = 't';
    public const POSITION_TOP_RIGHT = 'tr';
    public const POSITION_RIGHT = 'r';
    public const POSITION_BOTTOM_RIGHT = 'br';
    public const POSITION_BOTTOM = 'b';
    public const POSITION_BOTTOM_LEFT = 'bl';
    public const POSITION_LEFT = 'l';
    public const POSITION_TOP_LEFT = 'tl';
    public const POSITION_CENTER = 'c';

    public function __construct(string $text)
    {
        $this->text = $text;
        $this->font = new HelveticaFont();
        $this->color = RGB::fromHex('#fff');
        $this->backgroundColor = RGB::fromHex('#000');
        $this->backgroundOpacity = 0;
        $this->rotation = 0;
        $this->padding = 0;
        $this->width = 'auto';
        $this->textAlign = self::ALIGN_LEFT;
        $this->position = self::POSITION_BOTTOM_RIGHT;
    }

    /**
     * Set the text content
     *
     * @param string $text
     * @return self
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get the text
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Set the font to be used
     *
     * @param FontInterface $font
     * @return self
     */
    public function setFont(FontInterface $font): self
    {
        $this->font = $font;

        return $this;
    }

    /**
     * Set the font size
     *
     * @param integer $size
     * @return self
     */
    public function setFontSize(int $size): self
    {
        $this->getFont()->setSize($size);

        return $this;
    }

    /**
     * Get the currently defined font
     *
     * @return FontInterface|null
     */
    public function getFont(): FontInterface
    {
        return $this->font;
    }

    /**
     * Set the text color
     *
     * @param string|RGB $color
     * @return self
     */
    public function setColor(string|RGB $color): self
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
     * Set the background color
     *
     * @param string|RGB $color
     * @return self
     */
    public function setBackgroundColor(string|RGB $color): self
    {
        $this->backgroundColor = ($color instanceof RGB)
            ? $color
            : RGB::fromHex($color);

        return $this;
    }

    /**
     * Get the background color
     *
     * @return RGB
     */
    public function getBackgroundColor(): RGB
    {
        return $this->backgroundColor;
    }

    /**
     * Set the background opacity (%)
     *
     * @param integer $opacity
     * @return self
     */
    public function setBackgroundOpacity(int $opacity): self
    {
        $this->backgroundOpacity = $opacity;

        return $this;
    }

    /**
     * Get the background opacity
     *
     * @return integer
     */
    public function getBackgroundOpacity(): int
    {
        return $this->backgroundOpacity;
    }

    /**
     * Set the rotation angle
     *
     * @param integer $angle
     * @return self
     */
    public function setRotation(int $angle): self
    {
        $this->rotation = $angle;

        return $this;
    }

    /**
     * Get the rotation angle
     *
     * @return integer
     */
    public function getRotation(): int
    {
        return $this->rotation;
    }

    /**
     * Set the padding
     *
     * @param integer $padding
     * @return self
     */
    public function setPadding(int $padding): self
    {
        $this->padding = $padding;

        return $this;
    }

    /**
     * Get the padding
     *
     * @return integer
     */
    public function getPadding(): int
    {
        return $this->padding;
    }

    /**
     * Set the image pos
     *
     * @param string|array $position
     * @return self
     */
    public function setPosition(string|array $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get the position
     *
     * @return string|array
     */
    public function getPosition(): string|array
    {
        return $this->position;
    }

    /**
     * Set the width of the bounding box
     *
     * @param string|integer $width
     * @return self
     */
    public function setWidth(string|int $width): self
    {
        if ((is_string($width) === true && $width !== 'auto') || is_int($width) === false) {
            throw new InvalidArgumentException('Specified width must be either "auto", or integer.');
        }

        $this->width = $width;

        return $this;
    }

    /**
     * Get the width of the bounding box
     *
     * @return string|integer
     */
    public function getWidth(): string|int
    {
        return $this->width;
    }

    /**
     * Set the text alignment
     *
     * @param string $align
     * @return void
     */
    public function setTextAlignment(string $align)
    {
        $supported = [
            self::ALIGN_CENTER,
            self::ALIGN_LEFT,
            self::ALIGN_RIGHT
        ];

        if (in_array($align, $supported) === false) {
            throw new InvalidArgumentException('Specified alignment is invalid');
        }

        $this->textAlign = $align;

        return $this;
    }

    /**
     * Get the text alignment
     *
     * @return string
     */
    public function getTextAlignment(): string
    {
        return $this->textAlign;
    }

    /**
     * Add the text to the specified image.
     *
     * @param Image $image
     * @return Image
     */
    public function addToImage(Image $image): Image
    {
        $width  = imagesx($image->getOutput());
        $height = imagesy($image->getOutput());

        $boundingBox = $this->getBoundingBoxSize(
            $this->getFont(),
            $this->rotation,
            $this->text
        );

        $boxWidth = ($this->getWidth() === 'auto')
            ? $boundingBox->getWidth() - 1 + ($this->getPadding() * 2)
            : min($width, (($width * ($this->getWidth() / 100)) + ($this->getPadding() * 2)));

        $boxHeight = $boundingBox->getHeight() + ($this->getPadding() * 2);

        if (is_string($this->getPosition())) {
            switch ($this->position) {
                case self::POSITION_TOP:
                    $boxY = 0;
                    $boxX = round(($width - $boxWidth) / 2);
                    break;

                case self::POSITION_TOP_RIGHT:
                    $boxY = 0;
                    $boxX = $width - $boxWidth;
                    break;

                case self::POSITION_RIGHT:
                    $boxY = round(($height - $boxHeight) / 2);
                    $boxX = $width - $boxWidth;
                    break;

                case self::POSITION_BOTTOM_RIGHT:
                default:
                    $boxY = $height - $boxHeight;
                    $boxX = $width - $boxWidth;
                    break;

                case self::POSITION_BOTTOM:
                    $boxY = $height - $boxHeight;
                    $boxX = round(($width - $boxWidth) / 2);
                    break;

                case self::POSITION_BOTTOM_LEFT:
                    $boxY = $height - $boxHeight;
                    $boxX = 0;
                    break;

                # Left center:
                case self::POSITION_LEFT:
                    $boxY = round(($height - $boxHeight) / 2);
                    $boxX = 0;
                    break;

                case self::POSITION_TOP_LEFT:
                    $boxY = 0;
                    $boxX = 0;
                    break;

                case self::POSITION_CENTER:
                    $boxY = round(($height - $boxHeight) / 2);
                    $boxX = round( ($width - $boxWidth) / 2);
                    break;
            }
        }
        else {
            $boxX = $this->getPosition()['x'];
            $boxY = $this->getPosition()['y'];
        }

        $boxX2 = $boxX + $boxWidth;
        $boxY2 = $boxY + $boxHeight;

        // Handle the text alignment:
        switch ($this->textAlign) {
            case self::ALIGN_LEFT:
            default:
                $textX = ($boxX + $this->getPadding());
                $textY = (($boxY + $boundingBox->getHeight() + $boundingBox->getYOffset()) + $this->getPadding());
                break;

            case self::ALIGN_CENTER:
                $textX = round((($boxWidth - $boundingBox->getWidth()) - ($this->getPadding() * 2)) / 2 );
                $textY = (($boxY + $boundingBox->getHeight() + $boundingBox->getYOffset()) + $this->getPadding());
                break;

            case self::ALIGN_RIGHT:
                $textX = ($boxWidth - $this->getPadding() - $boundingBox->getWidth());
                $textY = (($boxY + $boundingBox->getHeight() + $boundingBox->getYOffset()) + $this->getPadding());
                break;
        }

        // Build the background:
        if ($this->getBackgroundOpacity() > 0) {
            imagefilledrectangle(
                $image->getOutput(),
                $boxX,
                $boxY,
                $boxX2,
                $boxY2,
                imagecolorallocatealpha(
                    $image->getOutput(),
                    $this->backgroundColor->getRed(),
                    $this->backgroundColor->getGreen(),
                    $this->backgroundColor->getBlue(),
                    100 - $this->getBackgroundOpacity()
                )
            );
        }

        imagettftext(
            $image->getOutput(),
            $this->getFont()->getSize(),
            $this->rotation,
            $textX,
            $textY,
            imagecolorallocate(
                $image->getOutput(),
                $this->getColor()->getRed(),
                $this->getColor()->getGreen(),
                $this->getColor()->getBlue()
            ),
            $this->getFont()->getFile(),
            $this->text
        );

        return $image;
    }

    /**
     * Calculate the bounding box for the text
     * http://php.net/manual/en/function.imagettfbbox.php#75407
     *
     * @param FontInterface $font
     * @param integer $angle
     * @param string $text
     * @return BoundingBox
     */
    private function getBoundingBoxSize(
        FontInterface $font,
        int $angle,
        string $text
    ): BoundingBox {
        $boundingBox = imagettfbbox($font->getSize(), $angle, $font->getFile(), $text);

        $width = abs($boundingBox[2] - $boundingBox[0]);

        if ($boundingBox[0] < -1) {
            $width = abs($boundingBox[2]) + abs($boundingBox[0]) - 1;
        }

        $height = abs($boundingBox[7]) - abs($boundingBox[1]);

        if ( $boundingBox[5] < 7) {
            $height += abs($boundingBox[5] + $boundingBox[3]);
        }

        return new BoundingBox(
            $width,
            $height,
            (0 - $boundingBox[3])
        );
    }
}
