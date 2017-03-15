<?php
namespace HuoLib\Tool\Parse;
class Parsewsy extends ParseDriver
{
//    检查抓取过来的内容是不是有效，比如删除、404等错误
    protected function checkValid(){
        $title = $this->domOperator->find('div[class=item-mb] a');
        if($title){
            $xiajia = $this->domOperator->find('div[class=item-dizhi-xiajia]');
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
        $name = $this->domOperator->find('div[class=item-mb] a');
        $name = $name[0]->innertext;

        $out_no = $this->domOperator->find('div[class=item-mb] span');
        $out_no = trim($out_no[0]->innertext);

        $special = '';

        $price = $this->domOperator->find('div[class=item-p1] em span');
        $price = (float)$price[0]->innertext;

        $other_price = $price + 50;

        $sellername = $this->domOperator->find('div[class=shop-name]');
        $sellername = trim($sellername[0]->innertext);

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
        $imageDoms = $this->domOperator->find('div[class=tb-pic tb-s50] a');
        $images = array();
        foreach ($imageDoms as $imageDom) {
            $image = $imageDom->href;
            $images[] = $image;
        }
        return implode(",",$images);
    }

//    抓取颜色尺码信息
    protected function getSpecs(){
        $spec1_list = array();
        foreach($this->domOperator->find('ul[data-property=颜色] li') as $post){
            if($post->title != ''){
                $spec1_list[] = $post->title;
            }

        }
        if(count($spec1_list) == 0){
            $spec1_list[] = '图色';
        }

        $spec2_list = array();
        foreach($this->domOperator->find('table[class=table-sku] tr[data-color='.$spec1_list[0].'] td[class=name] span') as $post){
            $spec2_list[] = $post->innertext;
        }
        if(count($spec2_list) == 0){
            $spec2_list[] = '均码';
        }

        return array(
            'spec1_list'=>implode(",",$spec1_list),
            'spec2_list'=>implode(",",$spec2_list),
        );

    }

//    抓取商品的详情描述
    protected function getDescription(){
        $desc = $this->domOperator->find('textarea[id=J_DivItemDesc2]');
        $desc = $desc[0]->innertext();
        return $desc;
    }
}
