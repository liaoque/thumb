<?php
@define('ROOT', dirname(dirname(__FILE__)));
//include ROOT . '/../config/config.php';
include ROOT . '/lib/function/common.php';
spl_autoload_register('fu_autoload');


$root = '/www/ftp/';
$defaultImage = 'http://img.kxwan.com/tt.png';
if ($_GET['r']) {
    $fileName = $_GET['r'];
    if (file_exists($root . $fileName)) {
        $imageGMagick = new Img_GMagick();
        $imageGMagick->setImage($fileName)->show();
        exit();
    } else {
        if (preg_match('/([^\.]+\.(png|jpg|jpeg|gif))\.(\d+)X(\d+)([!\^\@-]?)\.(png|jpg)/i', $fileName, $m)) {
            //源文件
            $sourceFile = $m[1];
            //缩放宽度
            $width = intval($m[3]);
            //缩放高度
            $height = intval($m[4]);
            //缩放类型
            $type = $m[5];
            //生成图片的类型
            $outFileType = $m[6];
            if (!$width && !$height) {
                $type = '-';
            }
            //宽高其中一个必须是大于0的
            if ($sourceFile && file_exists($sourceFile = $root . $sourceFile)) {
                switch ($type) {
                    case '!':
                        if ($width || $height) {
                            //非等比例缩放
                            $imageGMagick = Img_GMagick::uequalScaling($sourceFile, $width, $height);
                        }
                        break;
                    case '^':
                        if ($width || $height) {
                            //等比例缩放, 不足的用背景色填充
                            $imageGMagick = Img_GMagick::uequalScalingBackground($sourceFile, $width, $height);
                        }
                        break;
                    case '@':
                        if ($width && $height) {
                            //等比例智能缩放,会有部分裁切
                            $imageGMagick = Img_GMagick::intelligentScaling($sourceFile, $width, $height);
                        }
                        break;
                    case '-':
                        $imageGMagick = new Img_GMagick();
                        $imageGMagick->setImage($sourceFile)->compress(75);
                        break;
                    default:
                        $type = '';
                        //等比例缩放
                        if ($width && $height) {
                            $imageGMagick = Img_GMagick::equalScaling($sourceFile, $width, $height);
                        }
                        break;
                }
                if ($imageGMagick) {
                    $targetFile = $sourceFile . '.' . $width . 'X' . $height . $type . '.' . $outFileType;
                    $imageGMagick->save($targetFile);
                    $imageGMagick->show();
                    exit();
                }
            }
        }
    }
}
webheader($defaultImage);



