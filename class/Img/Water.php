<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/6
 * Time: 11:09
 */
class Img_Water extends Img_GMagick
{
    const POSTION_LEFT_TOP = 1;
    const POSTION_LEFT_BOTTOM = 2;
    const POSTION_RIGTH_TOP = 3;
    const POSTION_RIGHT_BOTTOM = 4;

    private $waterInfo = [
        'imageFile' => '',
        'style' => [
            'location' => self::POSTION_RIGHT_BOTTOM,
            'offsetX' => 0,
            'offsetY' => 0
        ]
    ];

    /**
     * @return mixed
     */
    public function getWaterStyleLocation()
    {
        return $this->waterInfo['style']['location'];
    }

    /**
     * @return mixed
     */
    public function getWaterStyleOffsetX()
    {
        return $this->waterInfo['style']['offsetX'];
    }

    /**
     * @return mixed
     */
    public function getWaterStyleOffsetY()
    {
        return $this->waterInfo['style']['offsetY'];
    }

    /**
     * @return mixed
     */
    public function getWaterImageFile()
    {
        return $this->waterInfo['imageFile'];
    }

    /**
     * 设置图片水印
     * @param $waterInfo
     */
    public function setWaterInfo($waterInfo)
    {
        if (file_exists($waterInfo['imageFile'])) {
            $this->waterInfo['imageFile'] = $waterInfo['imageFile'];
        }
        $style = $waterInfo['style'];
        foreach ($this->waterInfo['style'] as $key => $v) {
            if (!empty($style[$key])) {
                $this->waterInfo['style'][$key] = $style[$key];
            }
        }

    }


    private $thumbnail = 0.2;

    public function setThumbnailSize($num)
    {
        return $this->thumbnail * $num;
    }

    public function thumbnailImage($width, $height)
    {
        if ($width > $height) {
            $width = $this->setThumbnailSize($width);
            $width < $this->getImageWidth() && parent::thumbnailImage($width, 0);
        } else {
            $height = $this->setThumbnailSize($height);
            $height < $this->getImageHeight() && parent::thumbnailImage(0, $height);
        }
        return $this;
    }

    /**
     * 水印图片
     * @param Img_GMagick $ImgGMagick
     * @param null $waterInfo
     * @return $this
     * @throws Exception
     */
    public function waterMark(Img_GMagick $ImgGMagick, $waterInfo = null)
    {
        if (!empty($waterInfo)) {
            $this->setWaterInfo($waterInfo);
        }
        $waterImageFile = $this->getWaterImageFile();
        if (!$waterImageFile) {
            throw new Exception('水印图片不存在');
        }

        /**
         * 1.初始化水印
         * 2.缩放水印
         * 3.调整位置
         * 4.合并图层
         */
        $width = $ImgGMagick->getImageWidth();
        $height = $ImgGMagick->getImageHeight();
        $this->setImage($waterImageFile)
            ->thumbnailImage($width, $height);

        switch ($this->getWaterStyleLocation()) {
            case self::POSTION_LEFT_TOP:
                $offsetX = $this->getWaterStyleOffsetX();
                $offsetY = $this->getWaterStyleOffsetY();
                break;
            case self::POSTION_LEFT_BOTTOM:
                $offsetX = $this->getWaterStyleOffsetX();
                $offsetY = $this->getWaterStyleOffsetY();
                $offsetY = $height - $this->getImageHeight() - $offsetY;
                break;
            case self::POSTION_RIGTH_TOP:
                $offsetX = $this->getWaterStyleOffsetX();
                $offsetX = $width - $this->getImageWidth() - $offsetX;
                $offsetY = $this->getWaterStyleOffsetY();
                break;
            default:
                $offsetX = $this->getWaterStyleOffsetX();
                $offsetX = $width - $this->getImageWidth() - $offsetX;
                $offsetY = $this->getWaterStyleOffsetY();
                $offsetY = $height - $this->getImageHeight() - $offsetY;
                break;
        }
        $ImgGMagick->compositeImage($this, $offsetX, $offsetY);
        return $this;
    }




}