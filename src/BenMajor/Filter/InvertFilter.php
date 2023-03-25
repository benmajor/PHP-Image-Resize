<?php

namespace BenMajor\ImageResize\Filter;

use GdImage;

class InvertFilter implements Filter
{
    /**
     * @inheritDoc
     */
    public function apply(GdImage $image): void
    {
        imagefilter($image, IMG_FILTER_NEGATE);
    }
}
