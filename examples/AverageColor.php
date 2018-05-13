<?php

# Generate a <div> element using the image's average background color:
use \BenMajor\ImageResize\Image;

require 'vendor/autoload.php';

# Generate the image from a URL:
$image = new Image('http://4.bp.blogspot.com/-wykO_6QC7Sk/Uemq1_yXg8I/AAAAAAAAAHY/dtBq0IneP08/s1600/maka.jpg');

# Output a <div> with a background color that is the average color of the remote image:
echo '<div style="width:200px;height:200px;border:1px solid #000;background:'.$image->getAverageColor().'"></div>';
