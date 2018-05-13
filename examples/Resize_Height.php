<?php

# Resize the image to 300 pixels high, maintaining the aspect ratio

use \BenMajor\ImageResize\Image;

require 'vendor/autoload.php';

# Generate a new image resize object using a remote image:
$image = new Image('http://4.bp.blogspot.com/-wykO_6QC7Sk/Uemq1_yXg8I/AAAAAAAAAHY/dtBq0IneP08/s1600/maka.jpg');

# Resize and constrain:
$image->resizeHeight( 300 );

# Output it to the browser:
$image->output();
