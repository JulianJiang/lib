<?php
/**
 * Created by PhpStorm.
 * User: panus
 * Date: 2016/5/20
 * Time: 11:47
 */

namespace Huolib\Log;


interface LoggerInterface
{
    const LOG_LEVEL_INFO = 'info';

    const LOG_LEVEL_DEBUG = 'debug';

    const LOG_LEVEL_WARNING = 'warning';

    const LOG_LEVEL_ERROR = 'error';

    const LOG_LEVEL_TRACE = 'trace';

    public function infoMsg($message, $context = array());

    public function debugMsg($message, $context = array());

    public function warningMsg($message, $context = array());

    public function errorMsg($message, $context = array());

    public function traceMsg($message, $context = array());

    public function logMsg($message, $context = array(), $level = self::LOG_LEVEL_INFO);

}