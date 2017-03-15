<?php
namespace HuoLib\Tool\Parse;
class Parsehznzcn extends ParseDriver
{
//    检查抓取过来的内容是不是有效，比如删除、404等错误
    protected function checkValid(){
        $title = $this->domOperator->find('div[class=detail-midtitle]');
        if($title){
            return 1;
        }else{
            return 0;
        }
    }

//    基本信息，比如标题、货号、子标题、价格、商家名称等
    protected function getBasicInfo(){
        $name = $this->domOperator->find('div[class=detail-midtitle] h1',0);
        $name = trim($name->text());

        $out_no = $this->domOperator->getElementById("productCoodsNo");
        $out_no = trim($out_no->innertext);

        $special = '';

        $price = $this->domOperator->getElementById("productShopPrice");
        $price = (float)findNum($price->innertext);

        $other_price = $this->domOperator->find('p[class=sp-xianjia-show] span',0);
        if($other_price){
            $other_price = (float)findNum($other_price->innertext);
        }else{
            $other_price = $price + 50;
        }

        $sellername = $this->domOperator->find('div[class=LbrandKr-title] h3 a',0);
        $sellername = trim($sellername->innertext);

//        $out_no = $sellername."::".$out_no;


        $weight = $this->domOperator->getElementById("ProductWeight");
        if($weight){
            $weight = findIntNum($weight->innertext)/1000;
        }else{
            $weight = 0.8;
        }

        return array(
            'name'=>$name,
            'special'=>$special,
            'price'=>$price,
            'other_price'=>$other_price,
            'out_no'=>$out_no,
            'weight'=>$weight,
        );
    }

//    抓取商品的图片
    protected function getImages(){
        $imageDoms = $this->domOperator->find('ul[id=mycarousel] li a img');
        $images = array();
        foreach ($imageDoms as $imageDom) {
            $image = $imageDom->getAttribute("id");
            $images[] = $image;
        }
        return implode(",",$images);
    }

//    抓取颜色尺码信息
    protected function getSpecs(){
        $spec1_list = array();
        $spec1Doms = $this->domOperator->find('div[id=em0] a');
        foreach($spec1Doms as $post){
            if($post->innertext != ''){
                $spec1_list[] = $post->innertext;
            }

        }
        if(count($spec1_list) == 0){
            $spec1_list[] = '图色';
        }

        $spec2_list = array();
        $spec2Doms = $this->domOperator->find('div[id=em1] a');
        foreach($spec2Doms as $post){
            if($post->innertext != ''){
                $spec2_list[] = $post->innertext;
            }

        }
        if(count($spec2_list) == 0){
            $spec2_list[] = '图色';
        }

        return array(
            'spec1_list'=>implode(",",$spec1_list),
            'spec2_list'=>implode(",",$spec2_list),
        );
    }

//    抓取商品的详情描述
    protected function getDescription(){
        $imgDoms = $this->domOperator->find("div[class=rrng1_detail] img");
        foreach ($imgDoms as &$imgDom) {
            $imgDom->setAttribute("src",$imgDom->getAttribute("data-original"));
        }
        $desc = $this->domOperator->getElementById('tab0_detail');
        $children = $desc->childNodes();
        $str = '';
        foreach ($children as $child) {
            if($child->getAttribute("id") == 'props'){
                continue;
            }
            $str .= $child->outertext();
        }
        return $str;
    }
}
