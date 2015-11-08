<?php

/**
 * Class Captcha
 * @author weitao
 */
class Captcha{
    /**
     * 定义验证码图片高度
     * @var
     */
    public static $height;
    /**
     * 定义验证码图片宽度
     * @var
     */
    public static $width;
    /**
     * 定义验证码字符个数
     * @var
     */
    public static $textNum = 4;

    /**
     * 定义验证码字符内容
     * @var
     */
    public static $textContent;

    /**
     * 定义字符颜色
     * @var
     */
    public static $fontColor;

    /**
     * 定义随机出的文字颜色
     * @var
     */
    public static $randFontColor;

    /**
     * 定义字体大小
     * @var
     */
    public static $fontSize = 10;

    /**
     * 定义字体
     * @var
     */
    public static $fontFamily = '';

    /**
     * 定义背景颜色
     * @var
     */
    public static $bgColor;

    /**
     * 定义随机出的背景颜色
     * @var
     */
    public static $randBgColor;

    /**
     * 定义字符语言
     * @var
     */
    public static $textLang = 'en';
    /**
     * 定义干扰点数量
     * @var
     */
    public static $noisePoint = 30;

    /**
     * 定义干扰线数量
     * @var
     */
    public static $noiseLine = 3;

    /**
     * 定义是否扭曲
     * @var
     */
    public static $distortion = false;

    /**
     * 定义扭曲图片源
     * @var
     */
    public static $distortionImage;

    /**
     * 定义是否有边框
     * @var
     */
    public static $showBorder = false;

    /**
     * 定义验证码图片源
     * @var
     */
    public static $image;

    /**
     * 设置字符颜色
     * @param $fc
     */
    public static function setFontColor($fc){
        self::$fontColor = sscanf($fc, '#%2x%2x%2x');
    }

    public static function setBgColor($bc){
        self::$bgColor = sscanf($bc, '#%2x%2x%2x');
    }

    public static function initImage(){
        if(empty(self::$width)){
            self::$width = floor(self::$fontSize*1.3)*self::$textNum+10;
        }
        if(empty(self::$height)){
            self::$height = self::$fontSize*2;
        }
        self::$image = imagecreatetruecolor(self::$width,self::$height);
        if(empty(self::$bgColor)){
            self::$randBgColor = imagecolorallocate(self::$image,mt_rand(100,255),mt_rand(100,255),mt_rand(100,255));
        }else{
            self::$randBgColor = imagecolorallocate(self::$image,self::$bgColor[0],self::$bgColor[1],self::$bgColor[2]);
        }
        imagefill(self::$image,0,0,self::$randBgColor);
    }

    /**
     * 产生随机字符
     * @param $type
     * @return string
     */
    public static function randText($type){
        $string = '';
        switch($type){
            case 'en':
                $str = 'ABCDEFGHJKLMNPQRSTUVWXY3456789abcdefhjklmnpqrstuvwxy';
                for($i=0;$i<self::$textNum;$i++){
                    $string = $string.','.$str[mt_rand(0,(strlen($str))-1)];
                }
                break;
            case 'cn':
                for($i=0;$i<self::$textNum;$i++){
                    $string = $string.','.chr(rand(0xB0,0xCC)).chr(rand(0xA1, 0xBB));
                }
                $string = iconv('GB2312','UTF-8',$string);  //转换编码到utf8;
                break;
        }
        return substr($string,1);
    }

    /**
     * 输出文字到验证码
     */
    public static function createText(){
        $textArray = explode(',',self::randText(self::$textLang));
        self::$textContent = join('',$textArray);
        if(empty(self::$fontColor)){
            self::$randFontColor = imagecolorallocate(self::$image,mt_rand(0,100),mt_rand(0,100),mt_rand(0,100));
        }else{
            self::$randFontColor = imagecolorallocate(self::$image,self::$fontColor[0],self::$fontColor[1],self::$fontColor[2]);
        }
        for($i=0;$i<self::$textNum;$i++){
//            $angle = mt_rand(-1,1)*mt_rand(1,20);
//            imagettftext(self::$image,self::$fontSize,$angle,5+$i*floor(self::$fontSize*1.3),floor(self::$height*.075),self::$randFontColor,self::$fontFamily,$textArray[$i]);
                imagestring(self::$image,self::$fontSize,floor((self::$width-(self::$textNum*floor(self::$fontSize*1.3)))/2)+$i*floor(self::$fontSize*1.3),floor(self::$height*0.33),$textArray[$i],self::$randFontColor);
        }
    }

    /**
     * 生成干扰点
     */
    public static function createNoisePoint(){
        for($i=0;$i<self::$noisePoint;$i++){
            $pointColor = imagecolorallocate(self::$image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imagesetpixel(self::$image,mt_rand(0,self::$width),mt_rand(0,self::$height),$pointColor);
        }
    }

    /**
     * 产生干扰线
     */
    public static function createNoiseLine(){
        for($i=0;$i<self::$noiseLine;$i++){
            $lineColor = imagecolorallocate(self::$image,mt_rand(0,255),mt_rand(0,255),20);
            imageline(self::$image,0,mt_rand(0,self::$width),self::$width,mt_rand(0,self::$height),$lineColor);
        }
    }

    /**
     * 扭曲文字
     */
    public static function distortionText(){
        self::$distortionImage = imagecreatetruecolor(self::$width,self::$height);
        imagefill(self::$distortionImage,0,0,self::$randBgColor);
        for($x = 0;$x < self::$width; $x++){
            for($y = 0;$y < self::$height; $y++){
                $rgbColor = imagecolorat(self::$image,$x,$y);
                imagesetpixel(self::$distortionImage,(int)($x + sin($y/self::$height*2*M_PI-M_PI*0.5)*3),$y,$rgbColor);
            }
        }
        self::$image = self::$distortionImage;
    }

    public static function createImage(){
        header("Content-type:image/png");
        self::initImage();         //创建基本图片
        self::createText();        //输出验证码字符
        if(self::$distortion){
            self::distortionText();
        }                          //扭曲文字
        self::createNoisePoint();  //产生干扰点
        self::createNoiseLine();   //产生干扰线
        if(self::$showBorder){
            imagerectangle(self::$image,0,0,self::$width-1,self::$height-1,self::$randFontColor);
        }                          //添加边框
        imagepng(self::$image);
        imagedestroy(self::$image);
        if(self::$distortion){
            imagedestroy(self::$distortionImage);
        }
        return strtolower(self::$textContent);
    }

}