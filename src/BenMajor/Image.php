<?php

namespace BenMajor\ImageResize;

use BenMajor\ImageResize\Exception\CacheDirectoryNotWriteableException;
use BenMajor\ImageResize\Exception\InvalidSourceFileException;
use BenMajor\ImageResize\Exception\QualityOutOfBoundsException;
use BenMajor\ImageResize\Exception\SourceFileException;
use BenMajor\ImageResize\Exception\SourceFileNotSupportedException;
use BenMajor\ImageResize\Exception\TempDirectoryDoesNotExistException;
use BenMajor\ImageResize\Exception\TempDirectoryNotWriteableException;
use BenMajor\ImageResize\Filter\Filter;
use BenMajor\ImageResize\Model\Colourspace\RGB;
use BenMajor\ImageResize\Model\Component\Border;
use GdImage;
use InvalidArgumentException;

use function imagecreatefromstring;

class Image
{
    public const TYPE_JPEG = 'image/jpeg';
    public const TYPE_GIF = 'image/gif';
    public const TYPE_PNG = 'image/png';
    public const TYPE_WEBP = 'image/webp';
    public const TYPE_BMP = 'image/bmp';

    private string $source;
    private int $sourceWidth;
    private int $sourceHeight;
    private string $sourceMimeType;

    private ?GdImage $input;
    private ?GdImage $output;

    private ?int $outputWidth;
    private ?int $outputHeight;

    private RGB $backgroundColor;
    private Border $border;
    private ?string $filename;
    private ?string $extension;
    private ?string $basename;

    private int $padding = 0;
    private int $quality = 100;
    private bool $transparency = true;
    private bool $rename = true;
    private string $temporaryDirectory;
    private array $filters = [];

    public function __construct(string $image, bool $verify = true)
    {
        // Initialise some default values:
        $this->border = new Border(0, '#000');
        $this->backgroundColor = new RGB(0, 0, 0);
        $this->temporaryDirectory = sys_get_temp_dir();

        if (empty($image)) {
            throw new InvalidArgumentException('Image expects parameter 1 to be a valid file pointer string.');
        }

        // Read in the info:
        $info = getimagesize($image);

        if ($info === false) {
            throw new SourceFileException();
        }

        if ($this->isSupported($info['mime']) === false) {
            throw new SourceFileNotSupportedException();
        }

        $this->source = $image;
        $this->sourceWidth = $info[0];
        $this->sourceHeight = $info[1];
        $this->sourceMimeType = $info['mime'];

        $context = stream_context_create([
            'ssl' => [
                'allow_self_signed' => (!$verify),
                'verify_peer' => $verify,
                'verify_peer_name' => $verify,
            ]
        ]);

        // Load the image:
        $source = imagecreatefromstring(
            file_get_contents($this->source, false, $context)
        );

        if ($source === null) {
            throw new InvalidSourceFileException();
        }

        $this->input = $source;
        $this->output = $source;

        if (filter_var($image, FILTER_VALIDATE_URL)) {
            $url = parse_url($image);
            $this->filename = basename($url['path']);
        }
        else {
            $this->filename = basename($image);
        }

        $nameParts = explode('.', $this->filename);
        $this->extension = end($nameParts);
        $this->basename = implode('.', array_slice($nameParts, 0, -1));

        $this->outputWidth  = $this->sourceWidth;
        $this->outputHeight = $this->sourceHeight;
    }

    /**
     * Get the output image
     *
     * @return GdImage
     */
    public function getOutput(): GdImage
    {
        return $this->output;
    }

    /**
     * Set the padding (px)
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
     * Return the padding amount (px)
     *
     * @return integer
     */
    public function getPadding(): int
    {
        return $this->padding;
    }

    /**
     * Check if transparency is enabled
     *
     * @return boolean
     */
    public function isTransparent(): bool
    {
        return $this->transparency;
    }

    /**
     * Set the transparency mode
     *
     * @param boolean $transparency
     * @return self
     */
    public function setTransparency(bool $transparency): self
    {
        $this->transparency = $transparency;

        return $this;
    }

    /**
     * Set the output quality
     *
     * @param integer $quality
     * @return self
     */
    public function setQuality(int $quality): self
    {
        if ($quality < 0 || $quality > 100) {
            throw new QualityOutOfBoundsException();
        }

        $this->quality = $quality;

        return $this;
    }

    /**
     * Get the output quality
     *
     * @return integer
     */
    public function getQuality(): int
    {
        return $this->quality;
    }

    /**
     * Set the output color
     *
     * @param string $color
     * @return self
     */
    public function setBackgroundColor(string $color): self
    {
        $this->backgroundColor = RGB::fromHex($color);

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
     * Get the border
     *
     * @return Border
     */
    public function getBorder(): Border
    {
        return $this->border;
    }

    /**
     * Set the border width (px)
     *
     * @param integer $width
     * @return self
     */
    public function setBorderWidth(int $width): self
    {
        $this->getBorder()->setWidth($width);

        return $this;
    }

    /**
     * Set the border color
     *
     * @param string $color
     * @return self
     */
    public function setBorderColor(string $color): self
    {
        $this->getBorder()->setColor($color);

        return $this;
    }

    /**
     * Get the output width
     *
     * @return integer
     */
    public function getOutputWidth(): int
    {
        return $this->outputWidth;
    }

    /**
     * Set the output width
     *
     * @param integer $width
     * @return self
     */
    public function setOutputWidth(int $width): self
    {
        $this->outputWidth = $width;

        return $this;
    }

    /**
     * Get the output height
     *
     * @return integer
     */
    public function getOutputHeight(): int
    {
        return $this->outputHeight;
    }

    /**
     * Set the output height
     *
     * @param integer $height
     * @return self
     */
    public function setOutputHeight(int $height): self
    {
        $this->outputHeight = $height;

        return $this;
    }

    /**
     * Disable renaming
     *
     * @return self
     */
    public function disableRenaming(): self
    {
        $this->rename = false;

        return $this;
    }

    /**
     * Enable renaming
     *
     * @return self
     */
    public function enableRenaming(): self
    {
        $this->rename = true;

        return $this;
    }

    /**
     * Set the temporary directory
     *
     * @param string $directory
     * @return self
     */
    public function setTemporaryDirectory(string $directory): self
    {
        if (file_exists($directory) === false || is_dir($directory) === false) {
            throw new TempDirectoryDoesNotExistException();
        }

        if (is_writable($directory) === false) {
            throw new TempDirectoryNotWriteableException();
        }

        $this->temporaryDirectory = $directory;

        return $this;
    }

    /**
     * Get the temporary directory
     *
     * @return string
     */
    public function getTemporaryDirectory(): string
    {
        return $this->temporaryDirectory;
    }

    /**
     * Apply the specified filter
     *
     * @param Filter $filter
     * @return self
     */
    public function addFilter(Filter $filter): self
    {
        $this->filters[] = $filter;

        $filter->apply($this->output);

        return $this;
    }

    /**
     * Add the specified Text object to the image
     *
     * @param Text $text
     * @param string|null $position
     * @return self
     */
    public function addText(Text $text, ?string $position = null): self
    {
        if ($position !== null) {
            $text->setPosition($position);
        }

        $text->addToImage($this->output);

        return $this;
    }

    /**
     * Get the filters applied
     *
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Get the avergae colour of the image
     *
     * @return string
     */
    public function getAverageColor(): string
    {
        $sample = imagecreatetruecolor(1, 1);

        imagecopyresampled(
            $sample,
            $this->input,
            0,
            0,
            0,
            0,
            1,
            1,
            $this->sourceWidth,
            $this->sourceHeight
        );

        $rgb  = imagecolorat($sample, 0, 0);
        $color = imagecolorsforindex($sample, $rgb);

        $rgb = [
            'r' => round(round(($color['red'] / 0x33)) * 0x33),
            'g' => round(round(($color['green'] / 0x33)) * 0x33),
            'b' => round(round(($color['blue'] / 0x33)) * 0x33)
        ];

        return sprintf(
            '#%02X%02X%02X',
            $rgb['r'],
            $rgb['g'],
            $rgb['b']
        );
    }

    /**
     * Force resize an image using the specified width and height
     *
     * @param integer $width
     * @param integer|null $height
     * @return self
     */
    public function resize(int $width, ?int $height = null): self
    {
        if ($height === null) {
            return $this->resizeX($width);
        }

        $this->outputWidth  = $width;
        $this->outputHeight = $height;

        $this->output = imagecreatetruecolor($width, $height);

        if ($this->isTransparent() === false) {
            imagefill(
                $this->output,
                0,
                0,
                imagecolorallocate(
                    $this->output,
                    $this->getBackgroundColor()->getRed(),
                    $this->getBackgroundColor()->getGreen(),
                    $this->getBackgroundColor()->getBlue()
                )
            );
        }
        else {
            imagecolortransparent($this->output, imagecolorallocate($this->output, 0, 0, 0));
            imagealphablending($this->output, false);
            imagesavealpha($this->output, true);
        }

		imagecopyresampled(
            $this->output,
            $this->input,
            0,
            0,
            0,
            0,
            $width,
            $height,
            $this->sourceWidth,
            $this->sourceHeight
        );

        return $this;
    }

    /**
     * Resize image by width, maintaining aspect ratio
     *
     * @param integer $width
     * @return self
     */
    public function resizeX(int $width): self
    {
        $height = round($this->sourceHeight * ($width / $this->sourceWidth));

        $this->outputWidth  = $width;
        $this->outputHeight = $height;

		$this->output = imagecreatetruecolor($width, $height);

        if ($this->isTransparent() === false) {
            imagefill(
                $this->output,
                0,
                0,
                imagecolorallocate(
                    $this->output,
                    $this->getBackgroundColor()->getRed(),
                    $this->getBackgroundColor()->getGreen(),
                    $this->getBackgroundColor()->getBlue()
                )
            );
        }
        else {
            imagecolortransparent($this->output, imagecolorallocate($this->output, 0, 0, 0));
            imagealphablending($this->output, false);
            imagesavealpha($this->output, true);
        }

		imagecopyresampled(
            $this->output,
            $this->input,
            0,
            0,
            0,
            0,
            $width,
            $height,
            $this->sourceWidth,
            $this->sourceHeight
        );

        return $this;
    }

    /**
     * Resize image by height, maintaing aspect ratio
     *
     * @param integer $height
     * @return self
     */
    public function resizeY(int $height): self
    {
        $width = round($this->sourceWidth * ($height / $this->sourceHeight));

        $this->outputWidth  = $width;
        $this->outputHeight = $height;

		$this->output = imagecreatetruecolor($width, $height);

        if ($this->isTransparent() === false) {
            imagefill(
                $this->output,
                0,
                0,
                imagecolorallocate(
                    $this->output,
                    $this->getBackgroundColor()->getRed(),
                    $this->getBackgroundColor()->getGreen(),
                    $this->getBackgroundColor()->getBlue()
                )
            );
        }
        else {
            imagecolortransparent($this->output, imagecolorallocate($this->output, 0, 0, 0));
            imagealphablending($this->output, false);
            imagesavealpha($this->output, true);
        }

		imagecopyresampled(
            $this->output,
            $this->input,
            0,
            0,
            0,
            0,
            $width,
            $height,
            $this->sourceWidth,
            $this->sourceHeight
        );

        return $this;
    }

    /**
     * Resize the image, respecting its aspect ratio, and crop to the specified dimensions
     *
     * @param integer $width
     * @param integer|null $height
     * @return self
     */
    public function thumbnail(int $width, int $height = null): self
    {
        if ($height === null) {
            $height = $width;
        }

        // First, we need to resize it:
        $this->output = imagecreatetruecolor($width, $height);

        if ($this->isTransparent() === false) {
            imagefill(
                $this->output,
                0,
                0,
                imagecolorallocate(
                    $this->output,
                    $this->getBackgroundColor()->getRed(),
                    $this->getBackgroundColor()->getGreen(),
                    $this->getBackgroundColor()->getBlue()
                )
            );
        }
        else {
            imagecolortransparent($this->output, imagecolorallocate($this->output, 0, 0, 0));
            imagealphablending($this->output, false);
            imagesavealpha($this->output, true);
        }

        $ratio = $width / $this->sourceWidth;
        $newW = $width;
        $newH = $this->sourceHeight * $ratio;

        if ($newH < $height) {
            $ratio = $height / $this->sourceHeight;
            $newH = $height;
            $newW = $this->sourceWidth * $ratio;
        }

        $image2 = imagecreatetruecolor($newW, $newH);

        if ($this->isTransparent() === false) {
            imagefill(
                $image2,
                0,
                0,
                imagecolorallocate(
                    $image2,
                    $this->getBackgroundColor()->getRed(),
                    $this->getBackgroundColor()->getGreen(),
                    $this->getBackgroundColor()->getBlue()
                )
            );
        }
        else {
            imagecolortransparent($image2, imagecolorallocate($image2, 0, 0, 0));
            imagealphablending($image2, false);
            imagesavealpha($image2, true);
        }

        imagecopyresampled(
            $image2,
            $this->input,
            0,
            0,
            0,
            0,
            $newW,
            $newH,
            $this->sourceWidth,
            $this->sourceHeight
        );

        if(($newH !== $height) || ($newW !== $width)) {
            if($newH > $height) {
                $extra = $newH - $height;
                $x = 0;
                $y = round($extra / 2);

                imagecopyresampled(
                    $this->output,
                    $image2,
                    0,
                    0,
                    $x,
                    $y,
                    $width,
                    $height,
                    $width,
                    $height
                );
            }
            else {
                $extra = $newW - $width;
                $x = round($extra / 2);
                $y = 0;

                imagecopyresampled(
                    $this->output,
                    $image2,
                    0,
                    0,
                    $x,
                    $y,
                    $width,
                    $height,
                    $width,
                    $height
                );
            }

            $this->outputWidth  = $width;
            $this->outputHeight = $height;

            imagedestroy($image2);
        }
        else {
            $this->outputWidth  = $newW;
            $this->outputHeight = $newH;

            imagecopyresampled(
                $this->output,
                $this->input,
                0,
                0,
                0,
                0,
                $newW,
                $newH,
                $this->sourceWidth,
                $this->sourceHeight
            );
        }

        return $this;
    }


    /**
     * Contain the original image in a new image with the specified dimensions
     *
     * @param integer $width
     * @param integer|null $height
     * @param [type] $padding
     * @return self
     */
    public function contain(int $width, int $height = null): self
    {
        if ($height === null) {
            $height = $width;
        }

        $this->outputWidth  = $width;
        $this->outputHeight = $height;

        $this->output = imagecreatetruecolor($width, $height);

        $w = $this->sourceWidth;
        $h = $this->sourceHeight;

        if ($w < $width && $h < $height) {
            $x = round(($width - $w) / 2);
            $y = round(($height - $h) / 2);
            $newH = $h;
            $newW = $w;
        }
        else {
            if ($w > $h) {
                $newW = $width - ($this->padding * 2);
                $newH = ($h * ($width / $w)) - ($this->padding * 2);

                if ($newH > $height) {
                    $newH = $height - ($this->padding * 2);
                    $newW = $w * ($height / $h);
                }
            }
            else {
                $newH = $height - ($this->padding * 2);
                $newW = ($w * ($height / $h)) - ($this->padding * 2);
            }

            $x = round(($width - $newW) / 2);
            $y = round(($height - $newH) / 2);
        }

        if ($this->isTransparent() === false) {
            imagefill(
                $this->output,
                0,
                0,
                imagecolorallocate(
                    $this->output,
                    $this->getBackgroundColor()->getRed(),
                    $this->getBackgroundColor()->getGreen(),
                    $this->getBackgroundColor()->getBlue()
                )
            );
        }
        else {
            imagesavealpha($this->output, true);
            imagefill(
                $this->output,
                0,
                0,
                imagecolorallocatealpha($this->output, 0, 0, 0, 127)
            );
        }

        imagecopyresampled(
            $this->output,
            $this->input,
            $x,
            $y,
            0,
            0,
            $newW,
            $newH,
            $w,
            $h
        );

        return $this;
    }

    /**
     * Output the image
     *
     * @param string $format
     * @param string|null $cache
     * @return void
     */
    public function output(string $format, string $cache = null): void
    {
        // Do we need to add a border?
        $this->getBorder()->addToImage($this->output);
        $outputFunction = $this->getGdFunction($format);
        $quality = $this->getQualityValue($format);

        if ($cache !== null) {
            $outputName = $this->getCacheName($format, $cache);

            if ($format !== self::TYPE_BMP) {
                $outputFunction(
                    $this->output,
                    $outputName,
                    $quality
                );
            }
            else {
                $outputFunction(
                    $this->output,
                    $outputName
                );
            }
        }

        header('Content-type: '.$format);
        if ($format !== self::TYPE_BMP) {
            $outputFunction(
                $this->output,
                null,
                $quality
            );
        }
        else {
            $outputFunction(
                $this->output,
                null
            );
        }

        // Cleanup temp files:
        $this->cleanup();
    }

    /**
     * Encode the image as a base64 string
     *
     * @return string
     */
    public function toBase64($format = null): string
    {
        $mime = ($format === null)
            ? $this->sourceMimeType
            : $format;

        $outputFunction = $this->getGdFunction($mime);

        // Create a temporary file:
        $name = tempnam(
            $this->getTemporaryDirectory(),
            'IR_'
        );

        $outputFunction(
            $this->output,
            $name,
            $this->getQualityValue($mime)
        );

        $base64 = base64_encode(file_get_contents($name));

        return sprintf(
            'data:%s;base64,%s',
            $mime,
            $base64
        );
    }

    public function outputAsHtml(array $attributes = []): string
    {
        $outputFunction = $this->getGdFunction($this->sourceMimeType);

        // Create a temporary file:
        $name = tempnam(
            $this->getTemporaryDirectory(),
            'IR_'
        );

        $outputFunction(
            $this->output,
            $name,
            $this->getQualityValue($this->sourceMimeType)
        );

        $base64 = base64_encode(file_get_contents($name));

        $src = sprintf(
            'data:%s;base64,%s',
            $this->sourceMimeType,
            $base64
        );

        $html = '<img src="'.$src.'"';
        $html .= sprintf(' width="%d"', $this->outputWidth);
        $html .= sprintf(' height="%d"', $this->outputHeight);

        foreach ($attributes as $attribute => $value) {
            $html .= sprintf(' %s="%s"', $attribute, $value);
        }

        $html.= ' />';

        return $html;
    }

    /**
     * Force the resized image to be downloaded
     *
     * @param string $format
     * @param string|null $filename
     * @return void
     */
    public function download(string $format, ?string $filename = null): void
    {
        $filename = ($filename === null)
            ? $this->filename
            : $filename;

        header('Content-disposition: attachment; filename="'.$filename.'"');

        $this->output($format, false);
    }

    /**
     * Check if the specified MIME type is supported
     *
     * @param string $mime
     * @return boolean
     */
    private function isSupported(string $mime): bool
    {
        return in_array(
            $mime,
            [
                self::TYPE_GIF,
                self::TYPE_JPEG,
                self::TYPE_PNG,
                self::TYPE_WEBP
            ]
        );
    }

    /**
     * Generate the cached filename
     *
     * @param string $cacheDirectory
     * @return string
     */
    private function getCacheName(string $format, string $cacheDirectory): string
    {
        // Check it exists:
        if (file_exists($cacheDirectory) === false) {
            mkdir($cacheDirectory);
        }

        // Make sure the cache directory is writeable:
        if (is_writable($cacheDirectory) === false) {
            throw new CacheDirectoryNotWriteableException();
        }

        // Renaming enabled?
        if ($this->rename === true) {
            $name  = sprintf(
                '%s%s%s',
                rtrim($cacheDirectory, DIRECTORY_SEPARATOR),
                DIRECTORY_SEPARATOR,
                $this->basename ?? ''
            );

            $append = '';
            $i = 0;

            while(file_exists($name.$append.'.'.$this->getExtension($format))) {
                $i++;
                $append = '-'.$i;
            }

            return sprintf(
                '%s%s.%s',
                $name,
                $append,
                $this->getExtension($format)
            );
        }

        return sprintf(
            '%s%s%s.%s',
            rtrim($cacheDirectory, DIRECTORY_SEPARATOR),
            DIRECTORY_SEPARATOR,
            $this->basename,
            $this->getExtension($format)
        );
    }

    /**
     * Get extension by MIME
     *
     * @param string $type
     * @return string
     */
    private function getExtension(string $type): string
    {
        switch ($type) {
            default:
            case self::TYPE_JPEG:
                return 'jpeg';

            case self::TYPE_BMP:
                return 'bmp';

            case self::TYPE_GIF:
                return 'gif';

            case self::TYPE_PNG:
                return 'png';

            case self::TYPE_WEBP:
                return 'webp';
        }
    }

    /**
     * Get the GD output function
     *
     * @param string $type
     * @return string
     */
    private function getGdFunction(string $type): string
    {
        if ($this->isTransparent() === true) {
            return 'imagepng';
        }

        switch ($type) {
            case self::TYPE_BMP:
                return 'imagewbmp';

            case self::TYPE_GIF:
                return 'imagegif';

            case self::TYPE_JPEG:
            default:
                return 'imagejpeg';

            case self::TYPE_PNG:
                return 'imagepng';

            case self::TYPE_WEBP:
                return 'imagewebp';
        }
    }

    /**
     * Get the normalised output format
     *
     * @param string $type
     * @return integer
     */
    private function getQualityValue(string $type): int
    {
        if ($type === self::TYPE_PNG || $this->isTransparent() === true) {
            return abs( round((0 - ($this->quality / 100)) * 9) );
        }

        return $this->quality;
    }

    /**
     * Clean up the temporary files:
     *
     * @return void
     */
    private function cleanup(): void
    {
        imagedestroy($this->input);
        imagedestroy($this->output);
    }
}
