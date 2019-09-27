<?php

namespace denis909\thumb;

use Yii;

class ThumbBehavior extends \yii\base\Behavior
{

    public $attribute;

    public $basePath;

    public $baseUrl;

    public $thumbsUrl;

    public $thumbsPath = '@frontend/web/uploads/thumbs';

    public $defaultImage;

    public $quality = 90;

    public $throwException = true;

    public function init()
    {
        parent::init();

        if (strpos($this->basePath, '@') !== false) 
        {
            $this->basePath = Yii::getAlias($this->basePath);
        }

        if (strpos($this->thumbsPath, '@') !== false) 
        {
            $this->thumbsPath = Yii::getAlias($this->thumbsPath);
        }        
    }

    public function thumb($width = null, $height = null, $mode = null, $defaultImage = null, $quality = null)
    {  
        if (!$mode)
        {
            $mode = ThumbHelper::MODE_OUTBOUND;
        }

        if (!$quality)
        {
            $quality = $this->quality;
        }

        if (!$defaultImage)
        {
            $defaultImage = $this->defaultImage;
        }

        $target = $mode . '-' . $width . '-' . $height;

        if (!$this->owner->{$this->attribute})
        {
            if (!$defaultImage)
            {
                return null;
            }

            $source = $defaultImage;

            $target .= '/' . pathinfo($defaultImage, PATHINFO_BASENAME);
        }
        else
        {
            if ((!$width) && (!$height))
            {
                return $this->baseUrl . '/' . $this->owner->{$this->attribute};
            }

            $source = $this->basePath . '/' . $this->owner->{$this->attribute};

            $target .= '/' . $this->owner->{$this->attribute};
        }

        if (!$this->throwException && !is_file($source))
        {
            return null;
        }

        
        $result = ThumbHelper::thumb($source, $this->thumbsPath . '/' . $target, $width, $height, $mode, $quality);

        if (!$result)
        {
            if (!$this->throwException)
            {
                return null;
            }

            throw new Exception('Can\'t create thumb: ' . $target);
        }

        return $this->thumbsUrl . '/' . $target;
    }

}