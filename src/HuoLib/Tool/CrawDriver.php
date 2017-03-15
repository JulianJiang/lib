<?php
namespace HuoLib\Tool;


/**
 * 抓取商品网页信息
 * Class CrawDriver
 * @package HuoLib\Driver
 */
class CrawDriver
{
    public $url;
    public $id;
    public $type;
    private $parseDriver;

    /**
     * craw constructor.
     */
    public function __construct($url)
    {
        $this->url = $url;
        $this->checkUrl();
    }

    public function checkUrl(){
        $url = $this->url;
        if(empty($this->url)){
            $this->id = 0;
            $this->type = '';
            return;
        }
        if(strpos($url,"17huo.com")){
            $str = str_replace("17huo","",$url);
            $id = (int)findIntNum($str);
            if($id){
                $this->id = $id;
                $this->type = '17huo';
            }else{
                $this->id = 0;
                $this->type = '';
                return;
            }
        }elseif(strpos($url,"wsy.com")){
            $id = (int)findIntNum($url);
            if($id){
                $this->id = $id;
                $this->type = 'wsy';
            }else{
                $this->id = 0;
                $this->type = '';
                return;
            }
        }elseif(strpos($url,"hznzcn.com")){
            $id = (int)findIntNum($url);
            if($id){
                $this->id = $id;
                $this->type = 'hznzcn';
            }else{
                $this->id = 0;
                $this->type = '';
                return;
            }
        }else{
            $this->id = 0;
            $this->type = '';
            return;
        }
        $parseName = "\\HuoLib\\Tool\\Parse\\Parse".$this->type;

        $this->parseDriver = new $parseName($url);
    }

    public function craw(){
        if(!$this->id){
            return array("status"=>0,"msg"=>"链接无法识别");
        }
        $result = $this->parseDriver->parse();
        return $result;
    }



}
