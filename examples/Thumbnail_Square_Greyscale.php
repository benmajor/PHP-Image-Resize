<?php

# Resize the image to a 200x200 thumbnail, and convert to greyscale:

use \BenMajor\ImageResize\Image;

require 'vendor/autoload.php';

# Generate a new image resize object using a remote image:
$image = new Image('http://4.bp.blogspot.com/-wykO_6QC7Sk/Uemq1_yXg8I/AAAAAAAAAHY/dtBq0IneP08/s1600/maka.jpg');

# Convert it to greyscale:
$image->greyscale();

# Resize it (will produce a 200x200 thumb):
$image->resizeCrop( 200 );

# Output it to the browser:
$image->output();
