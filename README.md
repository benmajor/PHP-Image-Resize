# PHP ImageResize

ImageResize is a simple PHP class that can be used to resize images on the fly using PHP's native [GD library](http://php.net/manual/en/book.image.php). The library is also able to add watermarks and text overlays to your images automatically.

The only dependency for this Class is that the GD library be installed on your server. You must be running **PHP >= 5.6**.

[![Latest Version](https://img.shields.io/packagist/v/benmajor/php-image-resize.svg?style=flat-square)](https://packagist.org/packages/benmajor/php-image-resize)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

## Table of Contents:

1. [Version History](#1-version-history)
2. [Installation](#2-installation)
3. [Getting Started](#3-getting-started)
4. [Image Method Reference](#4-image-method-reference)
5. [Text Method Reference](#5-text-method-reference)
6. [Watermark Method Reference](#6-watermark-method-reference)
7. [Requirements](#7-requirements)
8. [Bugs & Features](#8-bugs-features)
9. [License](#9-license)

## 1. Version History:

+ **Version 1.1.0** (2018-05-13)
  + Added text overlay support
  + Added watermark support
  + Added `getAverageColor()` method to `Image`.
  + Minor bug fixes

+ **Version 1.0.0** (2018-05-09)
  + The library was officially launched and added to Github, Packagist and Composer.

## 2. Installation:

The easiest way to install the library is using [Composer](https://getcomposer.org/):

	$ composer require benmajor/php-image-resize
	
Or simply download the PHP package from the `src/` directory in this repository.

## 3. Getting Started:

To get started, call the constructor, and pass a path to a valid image to it:

	<?php
	
	use BenMajor\ImageResize\Image;
	
	$image = new Image('http://example.com/image.jpg');

**Note:** In order to use a remote URL, your PHP configuration must have the `allow_url_fopen` directive enable. If this is disabled, a fatal error will be thrown from the constructor stating that the source image does not exist.

Once you have created a new `ImageResize` object, it is possible to perform functions on the image (these functions are listed in Section 4). After applying functions and resize methods to the image, you are then able to output the generated image file to the browser, or force it to download to the user's computer by using the `output()` and `download()` methods respectively. 

To learn more about the usage of the library, please see the `examples` directory in this repository.

## 4. Image Method Reference:

**Note: The order in which methods are called is generally irrelevent, but you should always call the `output(*)` or `download(*)` functions last in order to avoid unexpected behaviour.**

The following is a list of the various methods that may be called on an `ImageResize` object. For more information and example usage, please see the examples posted in the `examples` directory of this repository.

Please note that some methods only affect certain resize techniques. When this is the case, a note has been added to the method reference below.

#### Resize mehtods:

`resize( $width, $height )`:<br />
Resizes the image to the specified dimensions, **ignoring aspect ratio**.
*If `$height` is `null`, a square image will be generated, using `$width` for both the width and height of the output.*

`resizeWidth( $width )`:<br />
Resizes the image to `$width` pixels wide, and calculates the height of the generated image in order to maintain the original aspect ratio.

`resizeHeight( $height )`:<br />
Resizes the image to `$height` pixels tall, and calculates the width of the generated image in order to maintain the original aspect ratio.

`resizeCrop( $width, $height )`:<br />
Resizes and automatically crops the image to the specified dimensions. This is particularly useful for generating thumbnails and smaller versions of images where the output size is constrained, but the aspect ratio of the original image should be maintained.<br />
*If `$height` is `null`, a square image will be generated, using `$width` for both the width and height of the output.*

`contain( $width, $height )`:<br />
Creates a canvas `$width` &times; `$height` pixels in size, and resizes the image proportionally to ensure the entire image fits onto the canvas and centers the resized image.
*If `$height` is `null`, a square image will be generated, using `$width` for both the width and height of the output.*

----

#### Output methods:

`output( $cache = false)`:<br />
Outputs the image using its original mime type.<br />
*`$cache` can be used as a directory pointer. If `$cache` is specified as a string, the resized image is written to the specified directory contained in `$cache`, rather than output to the buffer.*

`outputJPEG( $cache = false)`:<br />
Outputs the image as a JPEG.<br />
*`$cache` can be used as a directory pointer. If `$cache` is specified as a string, the resized image is written to the specified directory contained in `$cache`, rather than output to the buffer.*

`outputPNG( $cache = false )`:<br />
Outputs the image as a PNG.<br />
*`$cache` can be used as a directory pointer. If `$cache` is specified as a string, the resized image is written to the specified directory contained in `$cache`, rather than output to the buffer.*

`outputGIF( $cache = false )`:<br />
Outputs the image as a GIF.<br />
*`$cache` can be used as a directory pointer. If `$cache` is specified as a string, the resized image is written to the specified directory contained in `$cache`, rather than output to the buffer.*

`outputHTML( bool $tag, string $alt, string $title, bool $echo )`:<br />
Produces the necessary markup for an `<img />` element, and Base64 encodes the resized image. Accepts the following (optional) parameters:

+ `$tag`: include the HTML tag. If `false`, only the Base 64 URL will be returned.<br />Default: `true`.
+ `$alt`: string containing the value for the `alt` attribute of the generated element.<br />Default: `null`.
+ `$title`: string containing the value to be used in the `title` attribute of the generated element.<br />Default: `null`.
+ `$echo`: boolean indicating whether the tag should be echoed (output) as well as returned. Default: `true`.

----

#### Download methods:

`download( $filename = null )`:<br />
Force the image to be downloaded by the browser (rather than displayed inline) using its original mime type.<br />
*If `$filename` is null, the original filename will be used, or pass a string to force a different filename in the download dialog. The extension is not required.*

`downloadJPEG( $filename = null )`:<br />
Force the image to be downloaded by the browser as a JPEG image.<br />
*If `$filename` is null, the original filename will be used, or pass a string to force a different filename in the download dialog. The extension is not required.*

`downloadPNG( $filename = null )`:<br />
Force the image to be downloaded by the browser as a PNG image.<br />
*If `$filename` is null, the original filename will be used, or pass a string to force a different filename in the download dialog. The extension is not required.*

`downloadGIF( $filename = null )`:<br />
Force the image to be downloaded by the browser as a GIF image.<br />
*If `$filename` is null, the original filename will be used, or pass a string to force a different filename in the download dialog. The extension is not required.*

----

#### Editing methods:

`setPadding( $padding )`:<br />
Set the padding that should be used (in pixels) when using the `contain()` resize method.

`setTransparency( bool $transparency )`:<br />
Set whether the image resizer should maintain transparency (only valid for transparent PNG or GIF source images).

`disableRename()`:<br />
Do not automatically rename cached images when saving the resized image to the server.

`enableRename()`:<br />
Enables automatic renaming of cached images when saving to the server.

`setTmpDir()`:<br />
Set the temporary directory for working. By default, the temporary directory is the value returned by `sys_get_temp_dir()`.

`setQuality()`:<br />
Set the output quality of the generated image. The integer should be a value out of 100. The default value is **100**.

`setBackgroundColor( string $bg )`:<br />
Set the background colour that should be used for `contain()` or other methods. It should be passed as a hex-color string. If transparency is disabled, transparent GIFs or PNGs will have this as the background colour.

`setBorder( $width, $color )`:<br />
Add a border to the generated image. `$width` should be an integer representing the pixel width of the border. `$color` should be a hex string.

`setBorderWidth( $width )`:<br />
Sets the width of the border. `$width` should be an integer representing the pixel width of the border.

`setBorderColor( $color )`:<br />
Sets the color of the generated border. `$color` should be a string containing a hex value (e.g. `#000`).

----

#### Filter methods:

In addition to resizing images, this library also offers basic editing and filter capabilities for the image. This reference shows all of the currently supported filter methods:

`greyscale()`:<br />
Convert the image to greyscale (black and white).

`invert()`:<br />
Invert the colors of the image.

`setBrightness( int $brightness )`:<br />
Change the brightness of the image (accepts any valid integer value). Uses PHP's `IMG_FILTER_BRIGHTNESS` constant.

`setContrast( int $contrast )`:<br />
Changes the contrast of the image (accepts any valid integer value). Use's PHP's `IMG_FILTER_CONTRAST` constant.

`setSaturation( float $saturation )`:<br />
Increase or descrease the image's saturation. Since GD does not offer a built-in method for changing saturation, the only way we can achieve this is to manually set the colour value of each pixel in an image.<br />
**Note: if you're not caching images, this function may cause images to render slowly, especially larger images.**

`colorize( $color )`:<br />
Colorize the image using the specified `$color`. `$color` should be a string containing a valid hex color definition.

----

#### Get methods:

`getBorder()`:<br />
Get the current settings for the image's border (returns an object containing `width` and `color` properties.

`getBorderWidth()`:<br />
Gets the current width (in pixels) of the image's border.

`getBorderColor()`:<br />
Gets the current color (as an array) of the image's border.

`getTmpDir()`:<br />
Returns a string representing the current working directory.

`getQuality()`:<br />
Get the current quality setting.

`getTransparency()`:<br />
Check whether transparency is currently enabled.

`getPadding()`:<br />
Get the current pixel value of the padding. 

`getOutputWidth()`:<br />
Get the width of the generated image.<br />
***Note:** this must be called after a resize function has been called.*

`getOutputHeight()`:<br />
Get the height of the generated image.<br />
***Note:** this must be called after a resize function has been called.*

`getImgTagAttributes()`:<br />
Returns a string containing the size attributes of the resized image to be used for `<img />` tags (e.g. `width="x" height="x"`).
***Note:** this must be called after a resize function has been called.*

`getAverageColor()`:<br />
Returns the average colour of the image as a hex string (e.g. `#ff00ff`).

## 5. Text Method Reference:

In addition to offering resize functions, the library also allows the writing of text (including an optional bounding box) to the image before or after resizing. Text is defined using the `Text` class as follows:

	<?php

	$image = new Image('image_src');
	$text  = new Text('My String');
	
	$image->addText( $text );
	$image->output();
	
For more information and demos,  please check out the `examples` directory in the Github repo. 

The following is a list of the methods supported for `Text` objects:

----

#### Set methods:

`setText( string $text )`:<br />
Set the text to be rendered.

`setFont( string $src )`:<br />
Set the URL of a TTF font to be used for the rendered text.<br />
*Must point to a valid TTF font file -- can be remote.*

`setColor( string $hex )`:<br />
Set the foreground colour of the text using a valid hex string.

`setBackgroundColor( string $hex )`:<br />
Set the background colour of the bounding box using a valid hex string.

`setBackgroundOpacity( int $opacity )`:<br />
Set the % of the background's opacity.

`setPadding( int $padding )`:<br />
Set the padding (in `px`) of the bounding box.

`setPosition( mixed $position )`:<br />
Set the position of the text on the image. Accepts physical pixel co-ordinates (as an array indexed by `x` and `y` respectively), or one of the following string values:

+ `t` - centered, top
+ `tr` - top right corner
+ `r` - centered, right
+ `br` - bottom right corner
+ `b` - centered, bottom
+ `bl` - bottom left corner
+ `l` - centered, left
+ `tl` - top left corner
+ `c` - fully centered

`setWidth( mixed $width )`:<br />
Set the width of the bounding box. Either `'auto'`, or an integer representing a % width of the image. Auto will automatically size the bounding box to fit the content.

`setTextAlign( string $align )`:<br />
Set the text alignment within the bounding box. Accepts a string containing `left`, `center` or `right`.

----


#### Get methods:

`getText()`:<br />
Returns the text to be rendered in a string.

`getFont()`:<br />
Gets the URL of the font currently being used. (Default: `https://github.com/CartoDB/cartodb/blob/master/app/assets/fonts/helvetica.ttf?raw=true`).

`getFontSize()`:<br />
Get the current font size used for the text (in Points).

`getColor()`:<br />
Get the currently assigned foreground color. (Returns an array of RGB color values).

`getBackgroundColor()`:<br />
Get the background colour currently being used for the text's bounding box.

`getPadding()`:<br />
Get the bounding box padding.

`getPosition()`:<br />
Get the position for the text to be rendered on the original imafe.

`getWidth()`:<br />
Get the width of the bounding box.

`getTextAlign()`:<br />
Get the alignment of the text within the bounding box.


## 6. Watermark Method Reference:

Watermarks are useful for securing your images or making it difficult for unscrupulous web users to simply rip you off. Hower, manually adding watermarks to images can be laborious, and if you decide on a new logo or watermark, you need to do it all over again! This is the reason I decided to add watermark support to the library. 

Adding watermarks is easy; just specify the URL to use for the watermark logo, and add it to the image:

	<?php

	$image     = new Image('image_src');
	$watermark = new Watermark('watermark_src');
	
	$image->addWatermark( $watermark );
	$image->output();
	
**For best results, it is recommended to use a transparent PNG image for your watermark's source.**

For more information and demos,  please check out the `examples` directory in the Github repo. 

The following is a list of the methods supported for `Watermark` objects:

----

#### Set methods:
`setMargin( int $margin )`:<br />
Set the margin to be used for the watermark.

`setOpacity( int $opacity )`:<br />
Set the percentage of opacity to be used for the watermark.

`setPosition( mixed $position )`:<br />
Set the position of the watermark on the image. Accepts physical pixel co-ordinates (as an array indexed by `x` and `y` respectively), or one of the following string values:

+ `t` - centered, top
+ `tr` - top right corner
+ `r` - centered, right
+ `br` - bottom right corner
+ `b` - centered, bottom
+ `bl` - bottom left corner
+ `l` - centered, left
+ `tl` - top left corner
+ `c` - fully centered

`setWidth( int $width, bool $constrain = true )`:<br />
Set the resized width of the watermark, and optionally constrain its proportions.

`setHeight( int $height, bool $constrain = true )`:<br />
Set the resized height of the watermark, and optionally constrain its proportions.

----

#### Get methods:

`getMargin()`:<br />
Get the pixel value of the margin for the watermark.

`getOpacity()`:<br />
Get the opacity percentage of the watermark.

`getPosition()`:<br />
Get the position of the watermark.

`getWidth()`:<br />
Get the resized width of the watermark. May be useful if you are constraining the resize.

`getHeight()`:<br />
Get the resized height of the watermark. May be useful if you are constraining the resize.


## 7. Requirements:

The library requires PHP >= 5.6 and the PHP GD extension to be installed and enabled on the target server.

## 8. Bugs & Features:

If you have spotted any bugs, or would like to request additional features from the library, please file an issue via the Issue Tracker on the project's Github page: [https://github.com/benmajor/PHP-Image-Resize/issues](https://github.com/benmajor/PHP-Image-Resize/issues).

## 9. License:

Licensed under the **MIT License**:

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
