<?php

/**
 * bimage class for basic image manipulations,
 * requires GD library to be loaded in PHP,
 * OR nconvert from XnView
 *
 * Copyright (c) 2004 Krasimir Kazakov, wasp@mail.bg
 *
 * This is a FREE, LGPL-ed code, see
 * http://www.fsf.org/copyleft/lgpl.html for more.
 *
 * @author Krasimir Kazakov <wasp@drun.net>
 * @version $Revision: 1.1.1.1 $
 * @package bimage
 */

class bimage {
    public $VERSION = 1.2;
    public $im;
    public $usingGD = false;
    public $fileName = '';
    public $fileType = 0; // file type by default, 1 - gif, 2 - jpeg, 3 - png
    public $jpegQuality = 100; // in percents
    public $colorsTotal = 0; // count of colors
    public $imageIsTrueColor = false; // if the image is true color or not
    public $width = 0;
    public $height = 0;
    public $success = false;
    public $tmpPath = '/tmp/';
    public $tmpFile = '';
    public $nconvert = '';

    /**
     * Will load the image into the class
     *
     * @param string $fileName path to the real file name
     *
     * @return boolean true on success, false on failure
     */
    public function __construct($fileName, $nconvertPath = '')
    {
        if (function_exists('imagecreate')) {
            $this->usingGD = true;
            return $this->gdimage($fileName);
        } else {
            $this->usingGD = false;
            return $this->nimage($fileName, $nconvertPath);
        }
    }

    public function nimage($fileName, $nconvertPath)
    {
        global $NCONVERT_PATH;

        if (!file_exists($fileName)) {
            return false;
        }

        if ($nconvertPath == '') {
            $nconvertPath = $NCONVERT_PATH;
        }

        if (!file_exists($nconvertPath)) {
            return false;
        }

        $this->nconvert = $nconvertPath;

        $this->tmpFile = $this->randomChars();

        exec($nconvertPath." -info ".$fileName, $a);

        unset($a[0]); unset($a[1]);

        foreach ($a as $aRow) {
            if ((eregi(':', $aRow)) && (!eregi($fileName, $aRow))) {
                //echo $aRow."\n";
                $ll = split(':', $aRow);
                $key = strtolower(trim($ll[0]));
                $val = trim($ll[1]);
                $options[$key] = $val;
            }
        }
        if ((eregi('jpeg', $options['format'])) ||
            (eregi('jpg', $options['format']))) {

            $this->fileType = 2;
            $this->imageIsTrueColor = true;
        }
        if (eregi('png', $options['format'])) {
            $this->fileType = 3;
            if ($options['depth'] > 14) {
                $this->imageIsTrueColor = true;
            } else {
                $this->imageIsTrueColor = false;
            }
        }

        $this->colorsTotal = (int) $options['# colors'];
        $this->width = $options['width'];
        $this->height = $options['height'];

        $this->success = true;
        $this->fileName = $fileName;

        $this->tmpFile = $this->tmpPath."/".$this->randomChars();
        exec('cp '.$this->fileName.' '.$this->tmpFile);

        return $this->success;
    }

    public function gdimage($fileName)
    {
        if (!file_exists($fileName)) {
            return false;
        }
        list($width, $height, $type, $attr) = getimagesize($fileName);

        $this->width = $width;
        $this->height = $height;
        $this->fileName = $fileName;
        $this->fileType = $type;
        switch ($type) {
            case '1':
                $this->im = imagecreatefromgif($fileName);
                break;
            case '2':
                $this->im = imagecreatefromjpeg($fileName);
                break;
            case '3':
                $this->im = imagecreatefrompng($fileName);
                break;
            default :
                return false;
                break;
        }

        if ($this->im) {
            $this->success = true;
        } else {
            $this->success = false;
        }

        $this->colorsTotal = imagecolorstotal($this->im);
        $this->imageIsTrueColor = imageistruecolor($this->im);

        return true;
    }

    /**
     * Will save the image
     *
     * @param string $fileName save to file, or empty for overwrite input
     *
     * @return boolean true on success, false on failure
     */
    public function save($fileName = '')
    {
        if ($this->usingGD) {
            return $this->gdsave($fileName);
        } else {
            return $this->nsave($fileName);
        }
    }

    public function nsave($fileName)
    {
        system('cp '.$this->tmpFile.' '.$fileName);
        return true;
    }


    public function gdsave($fileName)
    {
        if ($fileName == '') {
            $fileName = $this->fileName;
        }
        switch ($this->fileType) {
            case '1':
                imagegif($this->im, $fileName);
                break;
            case '2':
                imagejpeg($this->im, $fileName, $this->jpegQuality);
                break;
            case '3':
                imagepng($this->im, $fileName);
                break;
            default :
                return false;
                break;
        }
    }

    /**
     * Will resize the image
     *
     * @param integer $newWidth new width, in pixels
     * @param integer $newHeight new height, in pixels
     *
     * @return boolean true on success, false on failure
     */
    public function resize($newWidth, $newHeight)
    {
        if ($this->usingGD) {
            return $this->gdresize($newWidth, $newHeight);
        } else {
            return $this->nresize($newWidth, $newHeight);
        }
    }

    public function nresize($newWidth, $newHeight)
    {
        $quality = "";
        $colors = "";
        if (!$this->imageIsTrueColor) {
            $colors = "-colors ".$this->colorsTotal;
        } else {
            $colors = "-truecolors";
            $quality = "-q ".$this->jpegQuality;
        }
        switch ($this->fileType) {
            case '1':
                $out = '-out gif';
                break;
            default:
            case '2':
                $out = '-out jpeg';
                break;
            case '3':
                $out = '-out png';
                break;
        }
        $size = "-resize {$newWidth} {$newHeight}";

        $convertString = $this->nconvert." -quiet $quality $colors $out $size -o ".$this->tmpFile." ".$this->tmpFile;

        //echo $convertString;
        exec($convertString, $a);

        return true;
    }

    public function gdresize($newWidth, $newHeight)
    {
		 $newImg = imagecreatetruecolor($newWidth, $newHeight);
		imagealphablending($newImg, false);
		imagesavealpha($newImg, true);
		$transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
		imagefilledrectangle($newImg, 0, 0, $width, $height, $transparent);
		if (!imagecopyresampled($newImg, $this->im, 0, 0, 0, 0, $newWidth, $newHeight, $this->width, $this->height) ){
            return false;
			
		}
		
        imagedestroy($this->im);
        $this->im = $newImg;
        $this->width = $newWidth;
        $this->height = $newHeight;
        return true;
    }

    /**
     * Will resize the image proportionally, based on the width specified
     *
     * @param integer $newWidth new width, in pixels
     *
     * @return boolean true on success, false on failure
     */
    public function resizeByWidth($newWidth)
    {
        $factor = round($this->width / $this->height, 5);
        $newHeight = round($newWidth / $factor);
        return $this->resize($newWidth, $newHeight);
    }

    /**
     * Will resize the image proportionally, based on the height specified
     *
     * @param integer $newHeight new height, in pixels
     *
     * @return boolean true on success, false on failure
     */
    public function resizeByHeight($newHeight)
    {
        $factor = round($this->width / $this->height, 5);

        $newWidth = round($newHeight * $factor);
        return $this->resize($newWidth, $newHeight);
    }

    /**
     * Will convert the image from truecolors to palette based colors
     *
     * @param integer $colors count of colors, max 256
     * @param boolean $dither true to dither the image, false otherwise
     *
     * @return boolean true on success, false on failure
     */
    public function toColors($colors, $dither = false)
    {
        if (imagetruecolortopalette($this->im, $dither, $colors)) {
            $this->colorsTotal = $colors;
            $this->imageIsTrueColor = false;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Will convert the image from palette based colors to truecolors
     *
     * @return boolean true on success, false on failure
     */
    public function toTrueColors()
    {
        $newIm = imagecreatetruecolor($this->width, $this->height);
        $res = imagecopy($newIm, $this->im, 0, 0, 0, 0, $this->width, $this->height);
        if ($res) {
            imagedestroy($this->im);
            $this->im = $newIm;
            $this->imageIsTrueColor = true;
            $this->colorsTotal = 0;
        }
        return $res;
    }

    /**
     * Will change the image type to GIF, JPEG or PNG
     *
     * @param string $newType new type, can be either number - 1: gif, 2:jpg,
     *    3: png, or text: 'gif', 'jpg'/'jpeg' or 'png'
     */
    public function setType($newType)
    {
        $newType = strtoupper($newType);
        switch ($newType) {
            case '1':
            case 'GIF':
            case 'gif':
                $this->fileType = 1;
                break;
            case '2':
            case 'JPG':
            case 'jpg':
            case 'JPEG':
            case 'jpeg':
                $this->fileType = 2;
                $this->imageIsTrueColor = true;
                break;
            case '3':
            case 'PNG':
            case 'png':
                $this->fileType = 3;
                break;
            default:
                return false;
                break;
        }

    }

    public function randomChars($length = 12)
    {
        $randomChars = '';

        for ($i = 0; $i < $length; $i++) {
            $randomChars .= chr(rand(97,122));
        }
        return $randomChars;
    }

    public function destroy() {
        if ($this->tmpFile != '') {
            @unlink($this->tmpFile);
        }
		 imagedestroy($this->im);
    }
	
	public function autoResize($newWidth, $newHeight) {
		if( $this->width > $this->height) {
			$this->resizeByWidth($newWidth);
		} 
		else {
			$this->resizeByHeight($newHeight);
		}
	}
	public function autoResizeBoth($newWidth, $newHeight) {
			$this->resize($newWidth, $newHeight);
	}
}
?>