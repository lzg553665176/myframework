<?php
/**
 * 配置文件
 * 
 */
//可用字符
$alphabet = "0123456789abcdefghijklmnopqrstuvwxyz"; 

//使用字符
if($type=='num'){   //显示数字
    $allowed_symbols = "0123456789"; #digits
}elseif($type =='normal'){//通常形式过滤掉容易误解的字母
    $allowed_symbols = "23456789abcdefhigkmnpqrstuvwxyz"; 
}else{ //使用全部可用字符
    $allowed_symbols = "0123456789abcdefghijklmnopqrstuvwxyz"; 
}

# folder with fonts
$fontsdir = 'fonts';	

# 字符数量
//$length = mt_rand(5,7); 
//$length = 4; 
//$length = 6;

//图片宽度和高度
//$width = 90;
//$height = 70;

//垂直波动幅度
$fluctuation_amplitude = 8;

//干扰点
//$white_noise_density=0; // no white noiseA
$white_noise_density=1/5;
$black_noise_density=0; // no black noise
//$black_noise_density=1/30;

//增加安全预防符号之间的空间
$no_spaces = true;

//显示域名
$show_credits = false; # set to false to remove credits line. Credits adds 12 pixels to image height
$credits = 'i.chinaskin.cn'; # if empty, HTTP_HOST will be shown

//图片颜色
//$foreground_color = array(0, 0, 0);
//$background_color = array(220, 230, 255);
//$foreground_color = array(mt_rand(0,80), mt_rand(0,80), mt_rand(0,80));
//$background_color = array(mt_rand(220,255), mt_rand(220,255), mt_rand(220,255));
$colors = array(
    array(27, 78, 181), // blue
    array(22, 163, 35), // green
    array(214, 36, 7), // red
);
$color_key        = array_rand($colors);
$foreground_color = $colors[$color_key];
$background_color = array(255, 255, 255);

//图片质量
$jpeg_quality = 90;
?>