<?php

# Maintain transparent background:

use \BenMajor\ImageResize\Image;

require 'vendor/autoload.php';

# Generate a new image resize object using a remote image:
$image = new Image('http://4.bp.blogspot.com/-wykO_6QC7Sk/Uemq1_yXg8I/AAAAAAAAAHY/dtBq0IneP08/s1600/maka.jpg');

# Resize crop:
$image->resizeCrop(200,200);

# Generate a HTML tag and echo it - alt: My Alt, title: My Title
$image->outputHTML( true, 'My Alt', 'My Title' );
