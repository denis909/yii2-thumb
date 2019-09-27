<?php

namespace denis909\thumb;

use Exception;
use yii\helpers\FileHelper;
use Imagine\Gd;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\ManipulatorInterface;
use yii\imagine\Image;

class ThumbHelper
{

    const MODE_INSET = 'inset';

    const MODE_OUTBOUND = 'outbound';

    public static function thumb($source, $target, $width, $height, $mode = self::MODE_OUTBOUND, $quality = 90)
    {
        $photo = Image::getImagine()->open($source);

        if (!$width || !$height)
        {
            $size = $photo->getSize();
        
            $currentWidth = $size->getWidth();
            
            $currentHeight = $size->getHeight();
                
            if (!$width)
            {
                $width = $currentWidth * ($height / $currentHeight);
            }
            else
            {
                $height =  $currentHeight * ($width / $currentWidth);
            }
        }

        if ($mode == static::MODE_INSET)
        {
            $photo = $photo->thumbnail(new Box($width, $height), ManipulatorInterface::THUMBNAIL_INSET);
        }
        else
        {
            $photo = $photo->thumbnail(new Box($width, $height), ManipulatorInterface::THUMBNAIL_OUTBOUND);
        }

        $created = FileHelper::createDirectory(dirname($target));

        if (!$created)
        {
            throw new Exception('Can\'t create: ' . dirname($target));
        }

        return $photo->save($target, ['quality' => $quality]);
    }


}