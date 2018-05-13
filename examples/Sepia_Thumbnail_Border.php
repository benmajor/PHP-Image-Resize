<?php

# Create a 300x200 sepia thumbnail with a border.

use \BenMajor\ImageResize\Image;

require 'vendor/autoload.php';

# Generate a new image resize object using a remote image:
$image = new Image('http://4.bp.blogspot.com/-wykO_6QC7Sk/Uemq1_yXg8I/AAAAAAAAAHY/dtBq0IneP08/s1600/maka.jpg');

# Make it greyscale:
$image->greyscale();

# Colorize to simulate sepia:
$image->colorize('#332600');

# Add the border:
$image->setBorder( 5, '#333' );

# Resize and crop:
$image->resizeCrop( 300, 200 );

# Output it to the browser:
$image->output();
