<?php

namespace BenMajor\ImageResize;

use BenMajor\ImageResize\Exception\WatermarkException;
use GdImage;

class Watermark
{
    private string $source;
    private int $sourceWidth;
    private int $sourceHeight;
    private int $outputWidth;
    private int $outputHeight;

    private array|string $position;
    private int $margin;

    private GdImage $image;
    private GdImage $watermark;

    protected $supported = [ 'image/jpeg', 'image/gif', 'image/png' ];

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

    public function __construct(string $image, bool $verify = true)
    {
        $this->position = self::POSITION_BOTTOM_RIGHT;
        $this->margin = 0;

        $info = getimagesize($image);

        // File does not exist:
        if (!$info) {
            throw new WatermarkException('Specified watermark source file does not exist.');
        }

        $this->source = $image;
        $this->sourceWidth = $info[0];
        $this->sourceHeight = $info[1];

        $context = stream_context_create([
            'ssl' => [
                'allow_self_signed' => (!$verify),
                'verify_peer' => $verify,
                'verify_peer_name' => $verify,
            ]
        ]);

        // Load the image:
        $this->image = imagecreatefromstring(
            file_get_contents($this->source, false, $context)
        );

        $this->outputWidth  = $this->sourceWidth;
        $this->outputHeight = $this->sourceHeight;
    }

    /**
     * Set the margin
     *
     * @param integer $margin
     * @return self
     */
    public function setMargin(int $margin): self
    {
        $this->margin = $margin;

        return $this;
    }

    /**
     * Get the margin
     *
     * @return integer
     */
    public function getMargin(): int
    {
        return $this->margin;
    }

    /**
     * Set the position of the watermark
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
     * @return array|string
     */
    public function getPosition(): array|string
    {
        return $this->position;
    }

    /**
     * Set the width of the watermark
     *
     * @param integer $width
     * @param boolean $constrain
     * @return self
     */
    public function setWidth(int $width, $constrain = true): self
    {
        $this->outputWidth = $width;

        if ($constrain === true) {
            $this->outputHeight = $this->sourceHeight * ($width / $this->sourceWidth);
        }

        return $this;
    }

    /**
     * Get the width of the watermark
     *
     * @return integer
     */
    public function getWidth(): int
    {
        return $this->outputWidth;
    }

    /**
     * Set the height of the watermark
     *
     * @param integer $height
     * @param boolean $constrain
     * @return self
     */
    public function setHeight(int $height, $constrain = true): self
    {
        $this->outputHeight = $height;

        if ($constrain === true) {
            $this->outputWidth = $this->sourceHeight * ($height / $this->sourceHeight);
        }

        return $this;
    }

    /**
     * Get the height
     *
     * @return integer
     */
    public function getHeight(): int
    {
        return $this->outputHeight;
    }

    /**
     * Add the watermark to the image
     *
     * @param Image $image
     * @return void
     */
    public function addToImage(Image $image)
    {
        $imageW = imagesx($image->getOutput());
        $imageH = imagesy($image->getOutput());

        if (is_string($this->getPosition())) {
            switch ($this->getPosition()) {
                case self::POSITION_TOP:
                    $poxY = $this->margin;
                    $poxX = round( ($imageW - $this->getWidth()) / 2 );
                    break;

                case self::POSITION_TOP_RIGHT:
                    $posY = $this->margin;
                    $posX = $imageW - $this->getWidth() - $this->margin;
                    break;

                case self::POSITION_RIGHT:
                    $posY = round(($imageH - $this->getHeight()) / 2);
                    $posX = $imageW - $this->getWidth() - $this->margin;
                    break;

                case self::POSITION_BOTTOM_RIGHT:
                default:
                    $posY = $imageH - $this->getHeight() - $this->margin;
                    $posX = $imageW - $this->getWidth() - $this->margin;
                    break;

                case self::POSITION_BOTTOM:
                    $posY = $imageH - $this->getHeight() - $this->margin;
                    $posX = round(($imageW - $this->getWidth()) / 2);
                    break;

                case self::POSITION_BOTTOM_LEFT:
                    $posY = $imageH - $this->getHeight();
                    $posX = $this->margin;
                    break;

                case self::POSITION_LEFT:
                    $poxY = round(($imageH - $this->getHeight()) / 2);
                    $poxX = $this->margin;
                    break;

                case self::POSITION_TOP_LEFT:
                    $posY = $this->margin;
                    $poxX = $this->margin;
                    break;

                case self::POSITION_CENTER:
                    $posY = round(($imageH - $this->getHeight()) / 2);
                    $posX = round(($imageW - $this->getWidth()) / 2);
                    break;
            }
        }
        else {
            $posX = $this->getPosition()['x'];
            $posY = $this->getPosition()['y'];
        }

        $this->watermark = imagecreatetruecolor($this->getWidth(), $this->getHeight());

        imagecolortransparent($this->watermark, imagecolorallocate($this->watermark, 0, 0, 0));
        imagealphablending($this->watermark, false);
        imagesavealpha($this->watermark, true);

        imagecopyresampled(
            $this->watermark,
            $this->image,
            0,
            0,
            0,
            0,
            $this->getWidth(),
            $this->getHeight(),
            $this->sourceWidth,
            $this->sourceHeight
        );

        imagecopy(
            $image->getOutput(),
            $this->watermark,
            $posX,
            $posY,
            0,
            0,
            $this->getWidth(),
            $this->getHeight()
        );

        return $image;
    }
}
