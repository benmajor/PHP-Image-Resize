<?php

# Contain the image on a 500x500 pixel canvas:

use \BenMajor\ImageResize\Image;

require 'vendor/autoload.php';

# Generate a new image resize object using a remote image:
$image = new Image('http://4.bp.blogspot.com/-wykO_6QC7Sk/Uemq1_yXg8I/AAAAAAAAAHY/dtBq0IneP08/s1600/maka.jpg');

# Set transparency to false (because we're using JPEG),
# and setting to tru would force a black background:
$image->setTransparency( false );

# Set the background to white:
$image->setBackgroundColor('#ffffff');

# Contain the image:
$image->contain( 500, 500 );

# Output it to the browser:
$image->output();
