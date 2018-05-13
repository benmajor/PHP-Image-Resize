<?php

# Replace transparent background with a colour:

use \BenMajor\ImageResize\Image;

require 'vendor/autoload.php';

# Generate a new image resize object using a remote image:
$image = new Image('http://vignette.wikia.nocookie.net/rickandmorty/images/1/19/Pickle_rick_transparent.png/revision/latest/scale-to-width-down/363?cb=20171025014216');

# Turn off transparency:
$image->setTransparency( false );

# Set the background:
$image->setBackgroundColor('#ff00ff');

# Resize it to 300px wide:
$image->resizeWidth(300);

# Output it to the browser:
$image->output();
