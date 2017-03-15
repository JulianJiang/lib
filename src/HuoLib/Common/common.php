<?php
    use HuoLib\Tool\RSASign;

    /**
     * 根据数组的某个key的值进行排序
     * @param $arr
     * @param $shortKey
     * @param int $short
     * @param int $shortType
     * @return mixed
     */
    function multiArraySort($arr, $shortKey, $short=SORT_DESC, $shortType=SORT_REGULAR)
    {
        foreach ($arr as $key => $data){
            $name[$key] = $data[$shortKey];
        }
        array_multisort($name,$shortType,$short,$arr);
        return $arr;
    }


    /**
     * 返回某个字符串的长度（包含中文字符）
     * @param $str
     * @return int
     */
    function getLen($str){
        return mb_strlen($str,'UTF8');
    }


    /**
     * 删除某个字符串里面的全部空格
     * @param $str
     * @return mixed
     */
    function trimAll($str)
    {
        $qian=array(" ","　","\t","\n","\r");
        $hou=array("","","","","");
        return str_replace($qian,$hou,$str);
    }


    /**
     * 截取字符串里面的数字，包含.，并返回
     * @param string $str
     * @return string
     */
    function findNum($str=''){
        $str=trim($str);
        if(empty($str)){return '';}
        $temp=array('1','2','3','4','5','6','7','8','9','0','.');
        $result='';
        for($i=0;$i<strlen($str);$i++){
            if(in_array($str[$i],$temp)){
                $result.=$str[$i];
            }
        }
        return $result;
    }

    /**
     * 截取字符串里面的整形数字，并返回
     * @param string $str
     * @return string
     */
    function findIntNum($str=''){
        $str=trim($str);
        if(empty($str)){return '';}
        $temp=array('1','2','3','4','5','6','7','8','9','0');
        $result='';
        for($i=0;$i<strlen($str);$i++){
            if(in_array($str[$i],$temp)){
                $result.=$str[$i];
            }
        }
        return $result;
    }


    /**
     * 将数组组装成&格式的get参数并返回
     * @param $fixedArray
     * @return string
     */
    function a2u($fixedArray){
        $url = '';
        foreach ($fixedArray as $key => $value) {
            # code...
            $url .= ($key."=".$value."&");
        }
        $url = substr($url, 0,strlen($url)-1);
        return $url;
    }


    /**
     * 将get参数切换成key-value格式的数组
     * @param $url
     * @return array
     */
    function u2a($url){
        $result = array();
        $urlArray = explode("&", $url);
        foreach ($urlArray as $key => $value) {
            # code...
            $paraArray = explode("=", $value);
            $result[$paraArray[0]] = $paraArray[1];
        }
        return $result;
    }


    /**
     * 获取客户端ip地址
     * @return string
     */
    function getIP(){

        if(!empty($_SERVER["HTTP_CLIENT_IP"])){

            $cip = $_SERVER["HTTP_CLIENT_IP"];
        }
        else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        else if(!empty($_SERVER["REMOTE_ADDR"]))
        {
            $cip = $_SERVER["REMOTE_ADDR"];
        }
        else
        {
            $cip = "";
        }
        return $cip;
    }


    /**
     * 循环删除文件夹
     * @param $dir
     * @return bool
     */
    function delDir($dir) {
        //先删除目录下的文件：
        $dh=opendir($dir);
        while ($file=readdir($dh)) {
            if($file!="." && $file!="..") {
                $fullpath=$dir."/".$file;
                if(!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    deldir($fullpath);
                }
            }
        }

        closedir($dh);
        //删除当前文件夹：
        if(rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 获取某个文件的后缀名
     * @param $file
     * @return string
     */
    function getExtension($file)
    {
        return substr(strrchr($file, '.'), 1);
    }


    /**
     * 返回文件名，如果$prefix不为空，则去掉后缀
     * @param $filename
     * @param string $prefix
     * @return mixed
     */
    function getBasename($filename, $prefix = ''){
        $filename = preg_replace('/^.+[\\\\\\/]/', '', $filename);
        if($prefix != ''){
            $filename = str_replace($prefix,'',$filename);
            return $filename;
        }else{
            return $filename;
        }
    }


    /**
     * 发送通用请求,重新定义了sign的生成方式
     * @param $url
     * @param $data
     * @return mixed
     */
    function sendRequestNew($url, $data){
        $ch = curl_init();
        $data['timeStamp'] = date("Y-m-d H:i:s");
        $data['fp'] = (string)1;
        ksort($data);

        $sign = json_encode($data);
        $sign = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $sign);
        $sign = urlencode($sign);
        $sign = md5($sign);

        $class = new RSASign();
        $sign = $class->signWithPri($sign);
        $data['sign']=$sign;
        $post_data = $data;


        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $output = curl_exec($ch);
        curl_close($ch);
        if(json_decode($output,true)){
            return json_decode($output,true);
        }
        return $output;
    }


    /**
     * @param int $time
     * @return bool|string
     */
    function currentFullTime($time=0){
        if(!$time){
            return date("Y-m-d H:i:s");
        }else{
            return date("Y-m-d H:i:s",$time);
        }
    }

?>