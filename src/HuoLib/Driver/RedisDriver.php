<?php
namespace HuoLib\Driver;

//
class RedisDriver{
/// redis configs
/**
	 * 架构函数
     * @param array $options 缓存参数
     * @access public
     */
    var $options = array();
    public $error;
    public $error_msg;
    private $handler;
    public function __construct() {
        require_once 'redisConfig.php';
        $this->options =  $redisConfig;
        try{
            $this->handler  = new \Redis();
        }catch(Exception $e){
            $this->error = 1;
            $this->error_msg = "缺少redis扩展";
            return;
        }

        // 连接redis服务器
        $connectResult = $this->connect();
        if(!$connectResult){
            $this->error = 1;
            $this->error_msg = "连接redis服务器失败";
        }
    }

    /**
     * 连接redis服务器
     * @access public
     * @param 空
     * @return true or false
     */
    public function connect(){
        $func = $this->options['persistent'] ? 'pconnect' : 'connect';
        $this->options['timeout'] === false ?
            $this->handler->$func($this->options['host'], $this->options['port']) :
            $this->handler->$func($this->options['host'], $this->options['port'], $this->options['timeout']);
        try{
            $this->handler->auth($this->options['auth']);
            $this->handler->ping();
        }catch(\Exception $e){
            return false;
        }
        return true;
    }

    /**
     * 读取缓存
     * @param $name
     * @param bool|true $temp
     * @return array|bool|mixed|stdClass|string
     */
    public function get($name,$temp = true) {
        if($temp){
            $value = $this->handler->get($this->options['prefix'].$name);
        }else{
            $value = $this->handler->get($name);
        }
        $jsonData  = json_decode( $value, true );
        return ($jsonData === NULL) ? $value : $jsonData;	//检测是否为JSON数据 true 返回JSON解析数组, false返回源数据
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param integer $expire  有效时间（秒）
     * @return boolean
     */
    public function set($name, $value, $expire = null) {
        if(is_null($expire)) {
            $expire  =  $this->options['expire'];
        }
        $name   =   $this->options['prefix'].$name;
        //对数组/对象数据进行缓存处理，保证数据完整性
        $value  =  (is_object($value) || is_array($value)) ? json_encode($value) : $value;
        if(is_int($expire) && $expire) {
            $result = $this->handler->setex($name, $expire, $value);
        }else{
            $result = $this->handler->set($name, $value);
        }

        return $result;
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function rm($name) {
        return $this->handler->delete($this->options['prefix'].$name);
    }

    /**
     * 清除缓存
     * @access public
     * @return boolean
     */
    public function clear() {
        return $this->handler->flushDB();
    }

    /**
     * 插入表尾
     * @access public
     * @return boolean
     */
    public function mqInR($key,$value){
    	if($key == '' || $key == null){
    		return false;
    	}
    	if(is_array($value)){
    		$value = json_encode($value);
    	}
    	return $this->handler->rpush($key, $value);
    }
    /**
     * 插入表头
     * @access public
     * @return boolean
     */
    public function mqInL($key,$value){
        if($key == '' || $key == null){
            return false;
        }
        if(is_array($value)){
            $value = json_encode($value);
        }
        return $this->handler->lpush($key, $value);
    }

    /**
     * 取出表头值
     * @access public
     * @return boolean
     */
    public function mqOutL($key){
        if($key == '' || $key == null){
            return null;
        }
        $value = $this->handler->lpop($key);
        return $value;
    }

    /**
     * 取出表头值
     * @access public
     * @return boolean
     */
    public function mqOutR($key){
        if($key == '' || $key == null){
            return null;
        }
        $value = $this->handler->rpop($key);
        return $value;
    }

    /**
     * 取出某个队列的长度
     * @access public
     * @return int
     */
    public function mqLen($key){
        if($key == '' || $key == null){
            return 0;
        }
        $value = $this->handler->llen($key);
        return $value;
    }

    /**
     * 关闭连接
     * @access public
     * @return int
     */
    public function close(){
        $this->handler->close();
    }
    /**
     * 选择DB
     * @param $value
     */
    public function select($value){
        $this->handler->select($value);
    }

    /**
     * 随机去KEY
     */
    public function getRandom(){
        return $this->handler->randomKey();
    }

    /**
     * 获取KEY
     * @param $value
     * @return array
     */
    public function getKeys($value){
        $name = $this->options['prefix'].$value.'*';
        return $this->handler->keys($name);
    }

    /**
     * @return Redis
     */
    // public function getRedisHandler()
    // {
    //     return $this->handler;
    // }
}
