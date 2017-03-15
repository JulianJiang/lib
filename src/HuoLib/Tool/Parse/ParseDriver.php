<?php
/**
 * 单商户： 每个应用端都持有相同的公私钥对；
 *          请求者用私钥加密数据发送数据，响应者用私钥解签，并用公钥进行数据
 *          有效性验签；合法后发送响应数据(私钥签名)
 */
namespace HuoLib\Tool\Parse;

require "simple_html_dom.php";

abstract class ParseDriver
{
    private $url;
    protected $domOperator;
    private  $domContent;

    //java 默认生成sign 方式

    /**
     * craw constructor.
     */
    public function __construct($url)
    {
        $this->url = $url;
        $this->domOperator = new \simple_html_dom();
    }

    private function loadUrl(){
        $this->domContent = file_get_contents($this->url);
        $this->domOperator->load($this->domContent);
    }

    public function parse(){
        $this->loadUrl();
        if(empty($this->domContent)){
            return array("status"=>0,"msg"=>"商品网页无内容或已失效");
        }
        if(!$this->checkValid()){
            return array("status"=>0,"msg"=>"商品网页无内容或已失效、已下架");
        }
        $data = array();
        $data['basic'] = $this->getBasicInfo();
        $data['images'] = $this->getImages();
        $images = explode(",",$data['images']);
        $head_img = $images[0];
        $data['head_img'] = $head_img;
        $data['description'] = $this->getDescription();
        $data['specs'] = $this->getSpecs();
        return array("status"=>1,"data"=>$data);
    }

//    检查抓取过来的内容是不是有效，比如删除、404等错误
    protected function checkValid(){}

//    基本信息，比如标题、货号、子标题、价格、商家名称等
    protected function getBasicInfo(){}

//    抓取商品的图片
    protected function getImages(){}

//    抓取颜色尺码信息
    protected function getSpecs(){}

//    抓取商品的详情描述
    protected function getDescription(){}

}
