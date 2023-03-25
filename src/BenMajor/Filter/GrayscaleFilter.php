<?php

namespace BenMajor\ImageResize\Filter;

use GdImage;

class GrayscaleFilter implements Filter
{
    /**
     * @inheritDoc
     */
    public function apply(GdImage $image): void
    {
        imagefilter($image, IMG_FILTER_GRAYSCALE);
    }
}
