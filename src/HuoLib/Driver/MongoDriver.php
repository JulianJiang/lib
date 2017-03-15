<?php
namespace HuoLib\Driver;

use MongoClient;

class MongoDriver{
    private $mongo;  //连接
    private $db_name;//数据库  
    private $table_name;//数据表

    public $error; //是否连接异常
    public $error_msg;

    function __construct(){
        require_once 'mongoConfig.php';
        $options = $mongoConfig;
        $host = $options['host'];
        $port = $options['port'];
        $user = $options['user'];
        $pass = $options['pass'];
    	$ul = 'mongodb://'.$user.':'.$pass.'@'.$host.':'.$port."/admin";
    	try{  
            $this->mongo = new \MongoClient($ul);
        } catch(\Exception $ex){
            $this->error = 1;
            $this->error_msg = "缺少mongo扩展";
            return;
        }  

        // 连接mongo数据库
        $connectResult = $this->connect();
        if(!$connectResult){
            $this->error = 1;
            $this->error_msg = "连接mongo数据库失败";
        }
    }
    public function __destruct() {  
        $this->close();  
    }
    //连接
    public function connect() { 
        try{  
            $this->mongo->connect();  
        } catch(\Exception $e){
            return false;
        }
        return true;
    }
    public function close(){
        $this->mongo->close();
    }
    //选择数据库
    public function select_db($db_name){  
        $this->db_name = $db_name;  
        try{  
            return $this->mongo->selectDB($db_name);  
        } catch (\Exception $ex) {
            $this->errors = $ex->getMessage();  
        }  
        
    } 
    //选择数据表
    public function select_collection($table_name){  
        $db_name = $this->db_name;  
        $this->table_name = $table_name;
        try {  
            // return  $this->mongo->$db_name->selectCollection($table_name);
            return $this->table_name;  
        } catch (\Exception $ex) {
            $this->errors = $ex->getMessage();  
        }  
    }  
    /*出错信息*/
    public function error($str, $t) {  
        echo $str;  
        exit;  
    }
    /*计算记录数*/
    public function countCollection($table_name = "",$condition=array()) {
    	$dbname = $this->db_name;
        if (empty($table_name)) {  
            $this->error("In order to retreive a count of documents from MongoDB, a collection name must be passed", 500);  
        }
        $count = $this->mongo->$dbname->$table_name->count($condition);
        return($count);  
    } 
    /*增加记录*/
    public function add($table_name,$record){
    	$dbname = $this->db_name;     
        try {     
            $this->mongo->$dbname->$table_name->insert($record, array('safe'=>true));     
            return true;     
        }     
        catch (\MongoCursorException $e)
        {     
            $this->error = $e->getMessage();     
            return false;     
        }  
    }
    /*删除记录*/
    public function delete($table_name,$record){
    	$dbname = $this->db_name;
        $options['safe'] = 1;     
        try {     
            $this->mongo->$dbname->$table_name->remove($record, $options);     
            return true;     
        }     
        catch (\MongoCursorException $e)
        {     
            $this->error = $e->getMessage();     
            return false;     
        }    
    }
    /*修改记录*/
    /*
    *condition 条件
    *newdata 新数组
    *多个集合删除一样数据
    */
    public function edit($table_name,$condition,$newdata,$options = array(),$type = '$set'){
    	$dbname = $this->db_name;    
        $options['safe'] = 1;     
        if (!isset($options['multiple']))     
        {     
            $options['multiple'] = 0;          }     
        try {     
            $this->mongo->$dbname->$table_name->update($condition,array($type => $newdata), $options);     
            return true;     
        }     
        catch (\MongoCursorException $e)
        {     
            $this->error = $e->getMessage();     
            return false;     
        }  
    }
    /*查找记录*/
    // public function find(){

    // }
    /*查找一条记录*/
    /*
    *
    *fileds 条件 条件为主
    *attr 查询字段

    */
    public function find($table_name,$fields=array(),$filter=array(),$attr=array()){
        $dbname = $this->db_name;  
        $res =  $this->mongo->$dbname->$table_name->find($fields,$attr);
        if(count($filter) > 0){
            if($filter['limit'] != ''){
                $res->limit($filter['limit']);
            }
            if($filter['skip'] != ''){
                $res->skip($filter['skip']);
            }
            if($filter['sort'] != ''){
                $res->sort($filter['sort']);
            }
        }
        $result = array();
	        try {  
	            while ($res->hasNext())     
	            {     
	                $result[] = $res->getNext();
	            }       
	        }     
	        catch (\MongoConnectionException $e)
	        {     
	            $this->error = $e->getMessage();     
	            return false;     
	        }     
	        catch (\MongoCursorTimeoutException $e)
	        {     
	            $this->error = $e->getMessage();     
	            return false;     
	        }     
	        return $result;        
    }  
    /*
    *
    *根据mongoID来检索记录
    *
    */
    public function findByObjectId($table_name, $_id){  
       $dbname = $this->db_name;
       $res =  $this->mongo->$dbname->$table_name->find(array('_id'=>( new MongoId($_id)))); 
       $result = array();
            try {  
                while ($res->hasNext())     
                {     
                    $result[] = $res->getNext();
                }       
            }     
            catch (\MongoConnectionException $e)
            {     
                $this->error = $e->getMessage();     
                return false;     
            }     
            catch (\MongoCursorTimeoutException $e)
            {     
                $this->error = $e->getMessage();     
                return false;     
            }     
            return $result;    
    }
    //获取mongo集合名
    public function getCollections(){
        $dbname = $this->db_name; 
        $res = $this->mongo->$dbname->getCollectionNames();
        return $res;
    }

}
?>