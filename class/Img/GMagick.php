<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/26
 * Time: 11:12
 */
class Img_GMagick
{

    private $imageFile;

    private $gmagick;

    public function __construct()
    {
        if (!class_exists('Gmagick')) {
            die('请安装GMagick扩展');
        }
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        $this->destroy();
    }

    public function destroy()
    {
        if ($this->gmagick && $this->gmagick instanceof GMagick) {
            $this->gmagick->destroy();
            $this->gmagick = null;
        }
    }

    public function save($targetFileName)
    {
        $this->getGMagick()->write($targetFileName);
        return $this;
    }

    public function __clone()
    {
        // TODO: Implement __clone() method.
        $this->gmagick = new GMagick();
    }

    private function getGMagick()
    {
        return $this->gmagick ? $this->gmagick : ($this->gmagick = new GMagick());
    }

    /**
     * 压缩图片
     * @param $quality 质量
     * @return GMagick|Img_GMagick
     */
    public function compress($quality)
    {
        $gm = $this->getGMagick();
        if (method_exists($gm, 'setCompressionQuality')) {
            $gm->setCompressionQuality($quality);
        }
        return $gm;
    }


    /**
     * @return int
     */
    public function getImageHeight()
    {
        $height = 0;
        if ($this->imageFile) {
            $gm = $this->getGMagick();
            $height = $gm->getimageheight();
        }
        return $height;
    }

    /**
     * @return int
     */
    public function getImageWidth()
    {
        $width = 0;
        if ($this->imageFile) {
            $gm = $this->getGMagick();
            $width = $gm->getimagewidth();
        }
        return $width;
    }

    /**
     * 绘制图像
     * @param Img_Font $imgFont
     * @return mixed
     */
    public function drawImage(Img_Font $imgFont)
    {
        $this->getGMagick()->drawimage($imgFont->getGmagickDraw());
        return $this;
    }

    /**
     * @param $imageFile
     * @param bool $clear
     * @return $this
     * @throws Exception
     */
    public function setImage($imageFile, $clear = false)
    {
        if ($clear) {
            $this->getGMagick()->clear();
        }
        if (!file_exists($imageFile)) {
            throw new Exception('文件不存在');
        }
        $this->getGMagick()->read($imageFile);
        $this->imageFile = $imageFile;
        return $this;
    }

    /**
     * 创建一张背景图片
     * @param $w
     * @param $h
     * @param $backgroundColor
     * @param string $format
     * @return Img_GMagick
     */
    public static function createBackgroundImage($w, $h, $backgroundColor, $format = 'png')
    {
        $imgGmagick = new self;
        $imgGmagick->getGMagick()
            ->newImage($w, $h, $backgroundColor)
            ->setImageFormat($format);;
        return $imgGmagick;
    }


    /**
     * 合并图片
     * @param Img_GMagick $ImgGMagick
     * @param $x
     * @param $y
     * @return $this
     */
    public function compositeImage(Img_GMagick $ImgGMagick, $x = 0, $y = 0)
    {
        $this->getGMagick()
            ->compositeimage($ImgGMagick->getGMagick(), Gmagick::COMPOSITE_OVER, $x, $y);
        return $this;
    }


    /**
     * 缩略图
     * @param $width
     * @param $height
     * @param $fit
     * @return $this
     */
    public function thumbnailImage($width, $height, $fit = false)
    {
        $this->getGMagick()->thumbnailimage($width, $height, $fit);
        return $this;
    }

    /**
     * 水印图片
     * @param $sourceImage          源图像
     * @param null $waterInfo
     * @return bool|Img_GMagick
     */
    public static function waterMarkImage($sourceImage, $waterInfo = null)
    {
        try {
            $imgGMagick = new self;
            $imgGMagick->setImage($sourceImage);
            $ImgWater = new Img_Water();
            $ImgWater->waterMark($imgGMagick, $waterInfo);
            return $imgGMagick;
        } catch (Exception $e) {
            log_message('img', json_encode($e), 'img');
            return false;
        }
    }


    /**
     * 水印文字图片
     * @param $sourceImage
     * @param $string
     * @param null $waterInfo
     * @return bool|Img_GMagick
     */
    public static function waterMarkString($sourceImage, $string, $waterInfo = null)
    {
        try {
            $imgGMagick = new self;
            $imgGMagick->setImage($sourceImage);
            $imgFont = new Img_Font();
            $imgFont->create($waterInfo);
            $offsetX = empty($waterInfo['offsetX']) ? 0 : $waterInfo['offsetX'];
            $offsetY = empty($waterInfo['offsetY']) ? $imgFont->getFontSize() : $waterInfo['offsetY'];
            $imgFont->waterMarkString($imgGMagick, $string, $offsetX, $offsetY);
            return $imgGMagick;
        } catch (Exception $e) {
            log_message('img', json_encode($e), 'img');
            return false;
        }
    }


    public function getDefault()
    {
        return [
            'imageFile' => '',
            'style' => [
                'location' => POSTION_RIGHT_BOTTOM,
                'width' => false,
                'height' => false
            ]
        ];
    }


    /**
     * 缩略图4种
     * 1. 等比例缩放
     * 2. 裁切
     * 3. 非等比例缩放
     * 4. 等比例缩放, 缺少的 用黑色填充
     * 5. 图片水印
     */

    /**
     * 等比例缩放, 返回 Img_Gmagick对象
     * $imgGmagick = Img_GMagick::equalScaling('xxx.png', 200, 200);
     * $imgGmagick->save('123.png');
     * @param $fileName
     * @param $w
     * @param $h
     * @return Img_GMagick
     */
    public static function equalScaling($fileName, $w, $h)
    {
        try {
            $imgGmagick = new self;
            $imgGmagick->setImage($fileName);
            $imgGmagick->thumbnailImage($w, $h);
            return $imgGmagick;
        } catch (Exception $e) {
            log_message('img', json_encode($e), 'img');
            return false;
        }
    }


    public function show()
    {
        header('Content-type: image/png');
        echo $this->getGMagick()->getImageBlob();
    }

    /**
     * 非等比例缩放
     * $imgGmagick = Img_GMagick::uequalScaling('xxx.png', 200, 200);
     * $imgGmagick->save('123.png');
     * @param $fileName
     * @param $w
     * @param $h
     * @return Img_GMagick
     */
    public static function uequalScaling($fileName, $w, $h)
    {
        try {
            $imgGmagick = new self;
            $imgGmagick->setImage($fileName);
            $imgGmagick->thumbnailImage($w, $h, true);
            return $imgGmagick;
        } catch (Exception $e) {
            log_message('img', json_encode($e), 'img');
            return false;
        }
    }

    /**
     * 等比例缩放, 不足的用背景色填充
     * @param $fileName
     * @param $w
     * @param $h
     * @param string $backgroundColor
     * @return Img_GMagick
     */
    public static function uequalScalingBackground($fileName, $w, $h, $backgroundColor = '#000000')
    {
        try {
            $backgroundImage = self::createBackgroundImage($w, $h, $backgroundColor);
            $imgGmagick = self::uequalScaling($fileName, $w, $h);
            $width = $imgGmagick->getImageWidth();
            $height = $imgGmagick->getImageHeight();
//            设置图片居中
            $offsetX = ($w - $width) / 2;
            $offsetY = ($h - $height) / 2;
            $backgroundImage->compositeImage($imgGmagick, $offsetX, $offsetY);
            $imgGmagick->destroy();
            return $backgroundImage;
        } catch (Exception $e) {
            log_message('img', json_encode($e), 'img');
            return false;
        }
    }


    /**
     * 等比例智能缩放, 会有部分裁切,但保留最大部分 返回 Img_Gmagick对象
     * $imgGmagick = Img_GMagick::intelligentScaling('xxx.png', 200, 200);
     * $imgGmagick->save('123.png');
     * @param $fileName
     * @param $w
     * @param $h
     * @return Img_GMagick
     */
    public static function intelligentScaling($fileName, $w, $h)
    {
        try {
            $imgGmagick = new self;
            $imgGmagick->setImage($fileName);
            $imgGmagick->getGMagick()
                ->cropthumbnailimage($w, $h);
            return $imgGmagick;
        } catch (Exception $e) {
            log_message('img', json_encode($e), 'img');
            return false;
        }
    }

    /**
     * 有损化压缩图片
     * @param $fileName     原图片
     * @param int $quality 质量
     * @return GMagick
     */
    public static function shrinkImg($fileName, $quality = 75)
    {
        $gm = new self();
        $gm->setImage($fileName);
        $gm->compress($quality);
        return $gm;
    }

    public function getImageBlob()
    {
        return $this->getGMagick();
    }

}