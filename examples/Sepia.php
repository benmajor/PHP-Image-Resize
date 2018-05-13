<?php

# Don't resize, but create sepia effect:

use \BenMajor\ImageResize\Image;

require 'vendor/autoload.php';

# Generate a new image resize object using a remote image:
$image = new Image('http://4.bp.blogspot.com/-wykO_6QC7Sk/Uemq1_yXg8I/AAAAAAAAAHY/dtBq0IneP08/s1600/maka.jpg');

# Make it greyscale:
$image->greyscale();

# Colorize to simulate sepia:
$image->colorize('#332600');

# Output it to the browser:
$image->output();
