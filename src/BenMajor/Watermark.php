<?php

namespace BenMajor\ImageResize;

class Watermark
{
    private $source;
    private $sourceWidth;
    private $sourceHeight;
    private $sourceMime;
    
    private $outputWidth;
    private $outputHeight;
    
    private $position;
    private $opacity;
    private $margin;
    
    private $image;
    private $watermark;
    
    protected $supported = [ 'image/jpeg', 'image/gif', 'image/png' ];
    
    function __construct( string $image = null )
    {
        $this->opacity   = 100;
        $this->position  = 'br';
        $this->margin    = 0;
        $this->watermark = null;
        
        if( !is_null($image) )
        {
            if( !is_string($image) )
            {
                throw new \Exception('Image parameter must be passed as a string.');
            }
            
            $info = getimagesize($image);
            
            # File doesn't exist:
            if( !$info )
            {
                throw new \Exception('Source image does not exist.');
            }
            
            # Make sure it's supported:
            if( !in_array($info['mime'], $this->supported) )
            {
                throw new \Exception('Image has an invalid MIME type ('.$info['mime'].').');
            }
            
            $this->source       = $image;
            $this->sourceWidth  = $info[0];
            $this->sourceHeight = $info[1];
            $this->sourceMime   = $info['mime'];
            
            # Load it:
            $this->image = imagecreatefromstring( file_get_contents($this->source) );
        }
        else
        {
            $this->source       = null;
            $this->sourceWidth  = 0;
            $this->sourceHeight = 0;
            $this->sourceMime   = null;
            $this->image        = null;
        }
        
        $this->outputWidth  = $this->sourceWidth;
        $this->outputHeight = $this->sourceHeight;
    }
    
    # Set the margin:
    public function setMargin( int $margin )
    {
        $this->margin = $margin;
    }
    
    # Get the margin:
    public function getMargin()
    {
        return $this->margin;
    }
    
    # Set the transparency:
    public function setOpacity( int $opacity )
    {
        $this->opacity = $opacity;
    }
    
    # Get the transparency:
    public function getOpacity()
    {
        return $this->opacity;
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
    
    # Get the position:
    public function getPosition()
    {
        return $this->position;
    }
    
    # Set the width:
    public function setWidth( int $width, $constrain = true )
    {
        $this->outputWidth = $width;
        
        if( $constrain )
        {
            $this->outputHeight = $this->sourceHeight * ( $width / $this->sourceWidth );
        }
    }
    
    # Get the width:
    public function getWidth()
    {
        return $this->outputWidth;
    }
    
    # Set the height:
    public function setHeight( int $height, $constrain = true )
    {
        $this->outputHeight = $height;
        
        if( $constrain )
        {
            $this->outputWidth = $this->sourceHeight * ( $height / $this->sourceHeight );
        }
    }
    
    # Get the height:
    public function getHeight()
    {
        return $this->outputHeight;
    }
    
    # Add it to the image:
    public function addToImage( $image )
    {
        $imageW = imagesx( $image );
        $imageH = imagesy( $image );
        
        if( is_string($this->position) )
        {
            switch( $this->position )
            {
                # Top center:
                case 't':
                    $poxY = $this->margin;
                    $poxX = round( ($imageW - $this->getWidth()) / 2 );
                    break;
                
                # Top right:
                case 'tr':
                    $posY = $this->margin;
                    $posX = $imageW - $this->getWidth() - $this->margin;
                    break;
                
                # Right center:
                case 'r':
                    $posY = round( ($imageH - $this->getHeight()) / 2);
                    $posX = $imageW - $this->getWidth() - $this->margin;
                    break;
                
                # Bottom right:
                case 'br':
                default:
                    $posY = $imageH - $this->getHeight() - $this->margin;
                    $posX = $imageW - $this->getWidth() - $this->margin;
                    break;
                
                # Bottom center:
                case 'b':
                    $posY = $imageH - $this->getHeight() - $this->margin;
                    $posX = round( ($imageW - $this->getWidth()) / 2 );
                    break;
                
                # Bottom left:
                case 'bl':
                    $posY = $imageH - $this->getHeight();
                    $posX = $this->margin;
                    break;
                
                # Left center:
                case 'l':
                    $poxY = round( ($imageH - $this->getHeight()) / 2 );
                    $poxX = $this->margin;
                    break;
                
                # Top left:
                case 'tl':
                    $posY = $this->margin;
                    $poxX = $this->margin;
                    break;
                
                # Center:
                case 'c':
                    $posY = round( ($imageH - $this->getHeight()) / 2);
                    $posX = round( ($imageW - $this->getWidth()) / 2 );
                    break;
            }
        }
        else
        {
            $posX = $this->position->x;
            $posY = $this->position->y;
        }
        
        $this->watermark = imagecreatetruecolor( $this->getWidth(), $this->getHeight() );
        
        # Transparency:
        $bg = imagecolorallocate($this->watermark, 0, 0, 0);
        imagecolortransparent($this->watermark, $bg);
        imagealphablending($this->watermark, false);
        imagesavealpha($this->watermark, true);
        
        imagecopyresampled( $this->watermark, $this->image, 0, 0, 0, 0, $this->getWidth(), $this->getHeight(), $this->sourceWidth, $this->sourceHeight);
        imagefilter($this->watermark, IMG_FILTER_COLORIZE, 0, 0, 0, (127 * (1 - ($this->opacity / 100))));
        imagecopy($image, $this->watermark, $posX, $posY, 0, 0, $this->getWidth(), $this->getHeight());
        
        return $image;
    }
    
    # Get the GD function name from the mime:
    private function getGDFn( $mime )
    {
        switch( $mime )
        {
            default:
                return str_replace('/', '', $mime);
                break;
        }
    }
    
    # Clean up resources and memory:
    private function cleanup()
    {
        imagedestroy($this->image);
        imagedestroy($this->watermark);
    }
}
