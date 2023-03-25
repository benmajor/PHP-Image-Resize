<?php

namespace BenMajor\ImageResize\Filter;

use GdImage;

interface Filter
{
    /**
     * Apply the filter to a specified image
     *
     * @param $image
     * @return void
     */
    public function apply(GdImage $image): void;
}
