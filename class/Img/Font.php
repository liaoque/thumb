<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/6
 * Time: 15:33
 */
class Img_Font
{

    private $gmagickDraw = null;

    public $font = 'Microsoft-YaHei';
    public $fontSize = '36';
    public $fontColor = '#000000';
    public $fontWidth = '2';
    public $fillColor = '#000000';
    public $rotate = 0;
//    public $opacity = 100;
//    public $fontWidth = '2';


    public $fontStyle = Gmagick::STYLE_NORMAL;


    public function create($info)
    {
        $this->gmagickDraw = new GmagickDraw();
        foreach ($this as $key => $v) {
            $action = $key == 'rotate' ? $key : strtolower('set' . $key);
            if (method_exists($this->getGmagickDraw(), $action)) {
                $value = empty($info[$key]) ? $this->$key : $info[$key];
                $this->getGmagickDraw()->$action($value);
            }
        }
    }

    public function setFontWidth($width)
    {
        $this->fontWidth = $width;
    }

    public function setFontSize($size)
    {
        $this->fontSize = $size;
    }

    public function getFontSize()
    {
        return $this->fontSize;
    }

    /**
     * 设置字体文件
     * @param $fontName
     */
    public function setFont($fontName)
    {
        $this->font = $fontName;
    }

    /**
     * 设置字体样式
     * @var int
     * Style constants
     * Gmagick::STYLE_NORMAL (integer)
     * Gmagick::STYLE_ITALIC (integer)
     * Gmagick::STYLE_OBLIQUE (integer)
     * Gmagick::STYLE_ANY (integer)
     */
    public function setFontStyle($style = Gmagick::STYLE_NORMAL)
    {
        $this->fontStyle = $style;
    }

    /**
     * 设置描边颜色
     * @param $color
     */
    public function setStrokeColor($color)
    {
        $this->strokeColor = $color;
    }

    /**
     * 设置字体描边透明度
     */
    public function setStrokeOpacity($opacity)
    {
        $this->strokeOpacity = $opacity;
    }

    /**
     * 设置描边宽度
     * @param $width
     */
    public function setStrokeWidth($width)
    {
        $this->strokeWidth = $width;
    }

    /**
     * 设置文字旋转角度
     */
    public function setRotate($rotate)
    {
        $this->rotate = $rotate;
    }


    public function getGmagickDraw()
    {
        return $this->gmagickDraw;
    }

    /**
     * 水印文字
     * @param Img_GMagick $ImgGMagick
     * @param $string
     * @param int $offsetX
     * @param int $offsetY
     * @return mixed
     */
    public function waterMarkString(Img_GMagick $ImgGMagick, $string, $offsetX = 100, $offsetY = 1000)
    {
        $this->setString($offsetX, $offsetY, $string);
        return $ImgGMagick->drawImage($this);
    }

    /**
     * 设置文字和偏移
     * @param $offsetX
     * @param $offsetY
     * @param $string
     * @return $this
     */
    public function setString($offsetX, $offsetY, $string)
    {
        $this->getGmagickDraw()->annotate($offsetX, $offsetY, $string);
        return $this;
    }

    public function __clone()
    {
        // TODO: Implement __clone() method.
        $this->gmagickDraw = null;
    }

//    public function getGmagickDraw(){
//
//    }


    /**
     * @param $style 测试好像没啥用...可能代码不对
     * Gmagick::STYLE_NORMAL (integer)  正常
     * Gmagick::STYLE_ITALIC (integer)
     * Gmagick::STYLE_OBLIQUE (integer)
     * Gmagick::STYLE_ANY (integer)
     */
//    setFontStyle('#000000', '#ffffff', '#cccccc') ;
//    function setFontStyle($fillColor, $strokeColor, $backgroundColor) {
//        $draw = new GmagickDraw();
//        $draw->setFont('Microsoft-YaHei');
//        $draw->setStrokeColor($strokeColor);
//        $draw->setFillColor($fillColor);
//        $draw->setStrokeWidth(1);
//        $draw->setFontSize(36);
//        $draw->setFontStyle(Gmagick::STYLE_NORMAL);
//        $draw->annotate(50, 50, "Lorem Ipsum!");
//
//        $draw->setFontStyle(Gmagick::STYLE_ITALIC);
//        $draw->annotate(50, 100, "Lorem Ipsum!");
//
//        $draw->setFontStyle(Gmagick::STYLE_OBLIQUE);
//        $draw->annotate(50, 150, "Lorem Ipsum!");
//
//        $draw->setFontStyle(Gmagick::STYLE_ANY);
//        $draw->annotate(50, 200, "Lorem Ipsum!");
//
//        $imagick = new Gmagick();
//        $imagick->newImage(350, 300, $backgroundColor);
//        $imagick->setImageFormat("png");
//        $imagick->drawImage($draw);
//
//        header("Content-Type: image/png");
//        echo $imagick->getImageBlob();
//    }


}