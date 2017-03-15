<?php
namespace HuoLib\Tool\Parse;

class Parse17huo extends ParseDriver
{
//    检查抓取过来的内容是不是有效，比如删除、404等错误
    protected function checkValid(){
        $title = $this->domOperator->find('div[class=good_name]');
        if($title){
            $xiajia = $this->domOperator->find('a[class=unsale]');
            if($xiajia){
                return 0;
            }
            return 1;
        }else{
            return 0;
        }
    }

//    基本信息，比如标题、货号、子标题、价格、商家名称等
    protected function getBasicInfo(){
        $name = $this->domOperator->find('div[class=good_name] h1 p',0);
        $name = trim($name->text());

        $out_no = $this->domOperator->find('div[class=more_sameflaggoods]',0);
        $out_no = $out_no->prev_sibling();
        $out_no = trim(str_replace("货号：","",$out_no->innertext));

        $special = '';

        $price = $this->domOperator->find('div[class=good_prise] span[class=red]',0);
        $price = (float)findNum($price->innertext);

        $other_price = $this->domOperator->find('div[class=fprice]',0);
        if($other_price){
            $other_price = (float)findNum($other_price->innertext);
        }else{
            $other_price = $price + 50;
        }

        $sellername = $this->domOperator->find('div[class=fwb]',0);
        $sellername = trim($sellername->innertext);

//        $out_no = $sellername."::".$out_no;

        return array(
            'name'=>$name,
            'special'=>$special,
            'price'=>$price,
            'other_price'=>$other_price,
            'out_no'=>$out_no,
            'weight'=>0.8,
        );
    }

//    抓取商品的图片
    protected function getImages(){
        $imageDoms = $this->domOperator->find('ul[class=img_list] li a div[class=lit_img]');
        $images = array();
        foreach ($imageDoms as $imageDom) {
            $image = $imageDom->getAttribute("data-bigger");
            $images[] = $image;
        }
        return implode(",",$images);
    }

//    抓取颜色尺码信息
    protected function getSpecs(){
        $spec1_list = array();
        $spec1Dom = $this->domOperator->find('div[class=color_c]',0);
        foreach($spec1Dom->childNodes() as $post){
            if($post->innertext != ''){
                $spec1_list[] = $post->innertext;
            }

        }
        if(count($spec1_list) == 0){
            $spec1_list[] = '图色';
        }

        $spec2_list = array();
        $spec2Dom = $this->domOperator->find('div[class=color_c]',1);
        foreach($spec2Dom->childNodes() as $post){
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
        $desc = $this->domOperator->getElementById('product-detail-original');
        $desc = $desc->innertext();
        return $desc;
    }
}
