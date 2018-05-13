<?php

namespace BenMajor\ImageResize;

class Text
{
    private $text;
    private $color;
    private $backgroundColor;
    private $backgroundOpacity;
    private $rotation;
    private $padding;
    private $position;
    
    private $font;
    private $fontSize;
    
    private $image;
    
    # Allowed alignment properties:
    protected $allowedAlignment = [ 'left', 'center', 'right' ];
    
    # Allowed positions:
    protected $allowedPositions = [ 't', 'tr', 'r', 'br', 'b', 'bl', 'l', 'tl', 'c' ];
    
    function __construct( $text )
    {
        $this->text              = $text;
        
        $font     = file_get_contents('https://github.com/CartoDB/cartodb/blob/master/app/assets/fonts/helvetica.ttf?raw=true');
        $fontFile = tempnam(sys_get_temp_dir(), 'FONT_');
        file_put_contents($fontFile, $font);
        
        $this->font              = $fontFile;
        $this->fontSize          = 12;
        
        $this->color             = $this->hex2rgb( '#fff' );
        $this->backgroundColor   = $this->hex2rgb( '#000' );
        $this->backgroundOpacity = 0;
        $this->rotation          = 0;
        $this->padding           = 0;
        $this->width             = 'auto';
        $this->textAlign         = 'left';
        
        $this->position          = 'br';
    }
    
    # Set the text:
    public function setText( $text )
    {
        $this->text = (string) $text;
    }
    
    # Get the text:
    public function getText()
    {
        return $this->text;
    }
    
    # Set the font source:
    public function setFont( string $src )
    {
        $font     = file_get_contents('https://github.com/CartoDB/cartodb/blob/master/app/assets/fonts/helvetica.ttf?raw=true');
        $fontFile = tempnam(sys_get_temp_dir(), 'FONT_');
        file_put_contents($fontFile, $font);
        
        $this->font = $fontFile;
    }
    
    # Get the font name:
    public function getFont()
    {
        return $this->font;
    }
    
    # Set the font size:
    public function setFontSize( int $size = 12 )
    {
        $this->fontSize = $size;
    }
    
    # Get the font size:
    public function getFontSize()
    {
        return $this->fontSize;
    }
    
    # Set the color:
    public function setColor( string $hex )
    {
        $this->color = $this->hex2rgb( $hex );
    }
    
    # Get the color:
    public function getColor()
    {
        return $this->color;
    }
    
    # Set the background color:
    public function setBackgroundColor( string $hex )
    {
        $this->backgroundColor = $this->hex2rgb( $hex );
    }
    
    # Get the background color:
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }
    
    # Set the background opacity:
    public function setBackgroundOpacity( int $opacity )
    {
        $this->backgroundOpacity = $opacity;
    }
    
    # Set the angle:
    public function setRotation( int $angle )
    {
        $this->rotation = $angle;
    }
    
    # Get the angle:
    public function getRotation()
    {
        return $this->rotation;
    }
    
    # Get the background opacity:
    public function getBackgroundOpacity()
    {
        return $this->backgroundOpacity;
    }
    
    # Set the padding:
    public function setPadding( int $padding = 0 )
    {
        $this->padding = $padding;
    }
    
    # Get the padding:
    public function getPadding()
    {
        return $this->padding;
    }
    
    # Set the position:
    public function setPosition( $position )
    {
        if( is_array($position) )
        {
            $this->position = new \stdClass();
            
            $this->position->x = $position['x'];
            $this->position->y = $position['y'];
        }
        elseif( is_string($position) )
        {
            $this->position = $position;
        }
        else
        {
            throw new \Exception('Position must be a string containing ('.implode(', ', $this->allowedPositions).') or an array containing X and Y co-ordinates');
        }
    }
    
    # Get the position:
    public function getPosition()
    {
        return $this->position;
    }
    
    # Set the width:
    public function setWidth( $width )
    {
        if( $width != 'auto' && !is_numeric($width) )
        {
            throw new \Exception('Specified width must be either auto, or valid integer.');
        }
        
        $this->width = $width;
    }
    
    # Get the width:
    public function getWidth()
    {
        return $this->width;
    }
    
    # Set the text align:
    public function setTextAlign( string $align = 'center' )
    {
        if( !in_array($align, $this->allowedAlignment) )
        {
            throw new \Exception( 'Specified alignment is invalid. You must use one of the following: '.implode(', ', $this->allowedAlignment));
        }
        
        $this->textAlign = $align;
    }
    
    # Get the text align:
    public function getTextAlign()
    {
        return $this->textAlign;
    }
    
    # Add the text to a GD image:
    public function addToImage( $image )
    {
        $this->image = $image;
        
        $width  = imagesx( $this->image );
        $height = imagesy( $this->image );
        
        // Only handle if it's set:
        if( !is_null($this->font) )
        {
            $box = $this->getBoundingBoxSize($this->fontSize, $this->rotation, $this->font, $this->text);
            
            # How big does the bg need to be?
            if( $this->getWidth() == 'auto' )
            {
                $boxWidth = $box['w'] - 1 + ($this->getPadding() * 2);
            }
            else
            {
                $boxWidth = min($width, (($width * ($this->getWidth() / 100)) + ($this->getPadding() * 2)));
            }
            
            $boxHeight = ($box['h']) + ($this->padding * 2);
            
            
            # Handle the positioning:
            if( is_string($this->position) )
            {
                switch( $this->position )
                {
                    # Top center:
                    case 't':
                        $boxY = 0;
                        $boxX = round( ($width - $boxWidth) / 2 );
                        break;
                    
                    # Top right:
                    case 'tr':
                        $boxY = 0;
                        $boxX = $width - $boxWidth;
                        break;
                    
                    # Right center:
                    case 'r':
                        $boxY = round( ($height - $boxHeight) / 2);
                        $boxX = $width - $boxWidth;
                        break;
                    
                    # Bottom right:
                    case 'br':
                    default:
                        $boxY = $height - $boxHeight;
                        $boxX = $width - $boxWidth;
                        break;
                    
                    # Bottom center:
                    case 'b':
                        $boxY = $height - $boxHeight;
                        $boxX = round( ($width - $boxWidth) / 2);
                        break;
                    
                    # Bottom left:
                    case 'bl':
                        $boxY = $height - $boxHeight;
                        $boxX = 0;
                        break;
                    
                    # Left center:
                    case 'l':
                        $boxY = round( ($height - $boxHeight) / 2 );
                        $boxX = 0;
                        break;
                    
                    # Top left:
                    case 'tl':
                        $boxY = 0;
                        $boxX = 0;
                        break;
                    
                    # Center:
                    case 'c':
                        $boxY = round( ($height - $boxHeight) / 2 );
                        $boxX = round( ($width - $boxWidth) / 2);
                        break;
                }
            }
            else
            {
                $boxX = $this->position->x;
                $boxY = $this->position->y;
            }
            
            $boxX2 = $boxX + $boxWidth;
            $boxY2 = $boxY + $boxHeight;
            
            # Handle the alignment:
            switch( $this->textAlign && $this->width != 'auto' )
            {
                case 'left':
                default:
                    $textX = ($boxX + $this->padding);
                    $textY = (($boxY + $box['h'] + $box['y']) + $this->padding);
                    break;
                
                case 'center':
                    $textX = round( (($boxWidth - $box['w']) - ($this->padding * 2)) / 2 );
                    $textY = (($boxY + $box['h'] + $box['y']) + $this->padding);
                    break;
                
                case 'right':
                    $textX = ($boxWidth - $this->padding - $box['w']);
                    $textY = (($boxY + $box['h'] + $box['y']) + $this->padding);
                    break;
            }
            
            # Background color:
            $bg = imagecolorallocatealpha($this->image, $this->backgroundColor['r'], $this->backgroundColor['g'], $this->backgroundColor['b'], abs(127 * (($this->backgroundOpacity / 100) - 1)) );
            
            # Generate the background:
            imagefilledrectangle( $this->image, $boxX, $boxY, $boxX2, $boxY2, $bg );
            
            # Foreground color:
            $color = imagecolorallocate( $this->image, $this->color['r'], $this->color['g'], $this->color['b'] );
            
            # Generate the text:
            imagettftext(
                $this->image,
                $this->fontSize,
                $this->rotation,
                $textX,
                $textY,
                $color,
                $this->font,
                $this->text
            );
            
            return $this->image;
        }
    }
    
    # Calculate the CORRECT bounding box (http://php.net/manual/en/function.imagettfbbox.php#75407):
    private function getBoundingBoxSize($size, $angle, $fontfile, $text)
    {
        $bbox = imagettfbbox($size, $angle, $fontfile, $text);
        $box  = [ ];
    
        # Calculate actual text width
        $box['w'] = abs($bbox[2] - $bbox[0]);
        
        if($bbox[0] < -1)
        {
            $bbox['w'] = abs($bbox[2]) + abs($bbox[0]) - 1;
        }
    
        # Calculate actual text height:
        $box['h'] = abs($bbox[7]) - abs($bbox[1]);
        
        if( $bbox[5] < 7 )
        {
            $box['h'] = $box['h'] + abs($bbox[5] + $bbox[3]);
        }
        
        $box['y'] = 0 - $bbox[3];
    
        return $box;
    }
        
   
    # Convert hex to RGB:
    private function hex2rgb( $hex )
    {
        $hex = str_replace('#', '', $hex);
        
        $hex      = str_replace('#', '', $hex);
        $length   = strlen($hex);
        $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
        $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
        $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));

        return $rgb;
    }
}
