<?php
namespace HuoLib\Tool;
class Captcha
{
    private $width;
    private $height;
    private $num;
    private $img;
    private $storageType;
    private $expireTime;

    public function __construct(){
        $this->checkGd();
    }

    public function captchaInit($width=200,$height=80,$num=4,$storageType = 'session',$expireTime = 60){
        $this->width = $width;
        $this->height = $height;
        $this->num = $num;
        $this->storageType = $storageType;
        $this->expireTime = $expireTime;
        $this->img = imagecreate($this->width,$this->height);
    }

    private function checkGd(){
        if( !extension_loaded('gd')){
            exit('gd库未加载成功,请配置好php的GD库环境!');
        }
    }

    /**
     * setBackground 设置图片的背景色
     * 
     * @access private
     * @return void
     */
    private function setBackground(){
        $bgs_dir = dirname(__FILE__);
        $bgs_dir .= "/captcha/bgs/";
        $bgs_rand = 4;

        $bg = $bgs_dir.$bgs_rand.'.jpg';
        $img_bg = imagecreatefromjpeg($bg);
        //return $img_bg;
        imagecopy($this->img,$img_bg,0,0,0,0,$this->width,$this->height);
    }

    private function setString(){
        $string = '23456789abcdefghijkmnpqrstwxyzABCEFGHJKLMNPQRSTWXYZ';
        $len = strlen($string)-1;
        $tmp = '';
        for( $i=0 ; $i<$this->num;$i++){
            $rand = mt_rand(0,$len);
            $tmp .= $string[$rand];
        }
        if($this->storageType == 'session'){
            session_start();
            $_SESSION['captcha'] = $tmp;
            $_SESSION['createTime'] = time();
        }
        if($this->storageType == 'cookie'){
            $domain = $_SERVER['HTTP_HOST'];
            setcookie("captcha",$tmp,$this->expireTime,'/',$domain);
        }
        return $tmp;
    }

    private function setFonts(){
        $ft_dir = dirname(__FILE__);
        $ft_dir .= '/Captcha/ttfs/';
        $ft = $ft_dir.'1.ttf';
        $ft_size =$this->height/2+5;

        $ft_color = imagecolorallocate($this->img,mt_rand(1,180),mt_rand(1,180),mt_rand(1,180));

        $str = $this->setString();


        for($i = 0 ; $i<$this->num; $i++){
            $text = $str[$i];
            $x = $i*($ft_size-5)+10;
            $y = $this->height/2+10;
            imagettftext($this->img,$ft_size,0,$x,$y,$ft_color,$ft,$text);
        }
    }


    public function showCaptcha($type){
        $this->setBackground();
        $this->setFonts();
        ob_clean();
        header("content-type: image/png");
        imagepng($this->img);
    }

    public function checkCaptcha($captcha){
        if(empty($captcha)){
            return "验证码不能为空";
        }
        if($this->storageType == 'session'){
            session_start();
            if($captcha == $_SESSION['captcha']){
                if(time() - $_SESSION['createTime'] > $this->expireTime){
                    return "验证码已过期";
                }
            }else{
                return "验证码不正确";
            }
        }
        if($this->storageType == 'cookie'){
            $cookieCaptcha = $_COOKIE['captcha'];
            if($cookieCaptcha != $captcha){
                return "验证码不正确";
            }
        }
        return;
    }

    public function __destruct(){
        imagedestroy($this->img);
    }

}
