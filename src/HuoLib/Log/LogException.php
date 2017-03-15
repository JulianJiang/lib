<?php
/**
 * Created by PhpStorm.
 * User: panus
 * Date: 2016/5/20
 * Time: 16:01
 */

namespace Huolib\Log;


use Exception;

class LogException extends Exception
{
    private $error_msg;

    public function __construct($message, $detail = array())
    {
        $code = 0;
        if (!empty($detail['code'])) {
            $code = $detail['code'];
        }
        $this->error_msg = $message;
        parent::__construct($message, $code);
    }

    public function getDetail()
    {
        return $this->error_msg;
    }

}