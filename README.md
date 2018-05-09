# PHP ImageResize

ImageResize is a simple PHP class that can be used to resize images on the fly using PHP's native [GD library](http://php.net/manual/en/book.image.php). The only dependency for this Class is that the GD library be installed on your server. You must be running **PHP >= 5.6**.

## Table of Contents:

1. [Version History](#1-version-history)
2. [Installation](#2-installation)
3. [Getting Started](#3-getting-started)
4. [Method Reference](#4-method-reference)
5. [Requirements](#5-requirements)
6. [Bugs & Features](#6-bugs-features)
7. [License](#7-license)

## 1. Version History:

+ **Version 1.0.0** (2018-05-09)
  + The library was officially launched and added to Github, Packagist and Composer.

## 2. Installation:

The easiest way to install the library is using [Composer](https://getcomposer.org/):

	$ composer require benmajor/php-image-resize
	
Or simply download the PHP package from the `src/` directory in this repository.

## 3. Getting Started:

To get started, call the constructor, and pass a path to a valid image to it:

	<?php
	
	$image = new ImageResize('http://example.com/image.jpg');

**Note:** In order to use a remote URL, your PHP configuration must have the `allow_url_fopen` directive enable. If this is disabled, a fatal error will be thrown from the constructor stating that the source image does not exist.

Once you have created a new `ImageResize` object, it is possible to perform functions on the image (these functions are listed in Section 4). After applying functions and resize methods to the image, you are then able to output the generated image file to the browser, or force it to download to the user's computer by using the `output()` and `download()` methods respectively. 

To learn more about the usage of the library, please see the `examples` directory in this repository.

## 4. Method Reference:

**Note: The order in which methods are called is generally irrelevent, but you should always call the `output(*)` or `download(*)` functions last in order to avoid unexpected behaviour.**

The following is a list of the various methods that may be called on an `ImageResize` object. For more information and example usage, please see the examples posted in the `examples` directory of this repository.

Please note that some methods only affect certain resize techniques. When this is the case, a note has been added to the method reference below.

#### Resize mehtods:

`resize( $width, $height )`:
Forces 


## 5. Requirements:

The library requires PHP >= 5.6 and the PHP GD extension to be installed and enabled on the target server.

## 6. Bugs & Features:

If you have spotted any bugs, or would like to request additional features from the library, please file an issue via the Issue Tracker on the project's Github page: [https://github.com/benmajor/PHP-Image-Resize/issues](https://github.com/benmajor/PHP-Image-Resize/issues).

## 7. License:

Licensed under the **MIT License**:

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
