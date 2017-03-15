<?php
/**
 * Created by PhpStorm.
 * User: panus
 * Date: 2016/5/20
 * Time: 14:42
 */

namespace Huolib\Log;



class FileLog implements LoggerInterface
{
    /**
     * @var int $maxFileSize 日志文件最大容量
     */
    public $maxFileSize = 10000000;

    /**
     * @var string 日志目录
     */
    public $logDir;

    /**
     * @var string 日志文件名
     */
    protected $logFile = '';

    /**
     * @var string 日志文件扩展名
     */
    public $logExt = 'log';

    /**
     * @var int 日志文件名ID
     */
    protected static $fileId = 1;

    public function __construct($name, $dir = '')
    {
        $this->logFile = $name;
        if(!empty($dir)){
            $this->logDir = $dir;
        }else{
            $this->logDir = __DIR__."/log/";
        }
        try{
            $this->logDirFilesInit();
        }catch (LogException $e){
            return $e->getDetail();
        }
    }

    public function infoMsg($message, $context = array())
    {
        // TODO: Implement infoMsg() method.
        $this->logMsg($message, $context, self::LOG_LEVEL_INFO);
    }

    public function debugMsg($message, $context = array())
    {
        // TODO: Implement debugMsg() method.
        $this->logMsg($message, $context, self::LOG_LEVEL_DEBUG);
    }

    public function warningMsg($message, $context = array())
    {
        // TODO: Implement warningMsg() method.
        $this->logMsg($message, $context, self::LOG_LEVEL_WARNING);
    }

    public function errorMsg($message, $context = array())
    {
        // TODO: Implement errorMsg() method.
        $this->logMsg($message, $context, self::LOG_LEVEL_ERROR);
    }

    public function traceMsg($message, $context = array())
    {
        // TODO: Implement traceMsg() method.
        $this->logMsg($message, $context, self::LOG_LEVEL_TRACE);
    }

    public function logMsg($message, $context = array(), $level = self::LOG_LEVEL_INFO)
    {
        // TODO: Implement logMsg() method.
        $log = array(
            'message' => $message,
        );
        $context['time'] = date('Y-m-d H:i:s');
        $context['log_level'] = $level;

        $log = $log + $context;
        $logMsg = var_export($log, true)."\r\n";
        file_put_contents($this->logFile, $logMsg, FILE_APPEND);
    }

    /**
     * 带占位符格式化的文件命名字串
     *
     * @param string $fname 文件前缀名
     * @return string
     */
    protected function logFileNamed($fname = '')
    {
        $dateName = date('Ymd');
        if(empty($fname)){
            $fileName = $dateName.'%d.'.$this->logExt;
        }else{
            $fileName = $fname.'_'.$dateName.'%d.'.$this->logExt;
        }
        return $fileName;
    }

    /**
     * 日志目录初始化
     *
     * @throws LogException
     */
    protected function logDirFilesInit()
    {
        $nameFormat = $this->logDir.'/'.$this->logFileNamed($this->logFile);
        $this->logFile = sprintf($nameFormat, self::$fileId);
        while(true) {
            if(is_file($this->logFile) && filesize($this->logFile) < $this->maxFileSize){
                break;
            }else{
                $this->logFile = sprintf($nameFormat, self::$fileId++);
                $fileTouch = touch($this->logFile);
                if(!$fileTouch){
                    throw new LogException('无法创建日志文件:'.$this->logFile);
                }
            }
        }
    }

    /**
     * 取得当前完整日志文件中
     *
     * @return string
     */
    public function getLogFile()
    {
        return $this->logFile;
    }

    /**
     * 设置文件名
     *
     * @param string $name 文件名
     * @param bool $full 是否是完整的文件名
     */
    public function setLogFileName($name, $full = false)
    {
        if($full){
            $this->logFile = $name;
        }else{
            $fname = $this->logFileNamed($name);
            $this->logFile = $this->logDir.'/'.sprintf($fname, 1);
        }
    }

}