<?php
/**
 * Shein framework project.
 * User: york <yorkding@outlook.com>
 * Date: 3/8/17
 * Time: 9:25 AM
 */

namespace sf\logger;

class Logger implements LoggerInterface
{
    /**
     * @var Redis
     */
    private $client;
    private $path;
    private $fileName;
    private $recorder;
    private $app;
    private $request;
    private $serverName;

    const INFO   = 'INFO';
    const NOTICE = 'NOTICE';
    const DEBUG  = 'DEBUG';
    const WARN   = 'WARN';
    const ERROR  = 'ERROR';

    const FILE_RECORDER = 'file';
    const REDIS_RECORDER = 'redis';

    public function __construct(Redis $client,$serverName)
    {
        $this->client = $client;
        $this->serverName = $serverName;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function setApp($app)
    {
        $this->app = $app;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    public function setRecorder($recorder)
    {
        $this->recorder = $recorder;
    }

    private function record($level, $context, $fileName){
        if($this->recorder !== null){
            if(in_array($level, $this->recorder[self::FILE_RECORDER])){
                $this->toFile($this->path . $fileName, $context);
            }
            if(in_array($level, $this->recorder[self::REDIS_RECORDER])){
                $this->toLogCenter($context);
            }
        }else{
            $this->toFile($this->path . $fileName, $context);
        }
    }

    public function warn($title, $message = '', $className = '', $fileName = '')
    {
        $context = $this->setLogBody($className, $title, $message, self::WARN);
        $fileName = $this->getFileName($fileName);
        $this->record(self::WARN, $context, $fileName);
    }

    public function notice($title, $message = '', $className = '', $fileName = '')
    {
        $context = $this->setLogBody($className, $title, $message, self::NOTICE);
        $fileName = $this->getFileName($fileName);
        $this->record(self::NOTICE, $context, $fileName);
    }

    public function debug($title, $message = '', $className = '', $fileName = '')
    {
        $context = $this->setLogBody($className, $title, $message, self::DEBUG);
        $fileName = $this->getFileName($fileName);
        $this->record(self::DEBUG, $context, $fileName);
    }

    public function info($title, $message = '', $className = '', $fileName = '')
    {
        $context = $this->setLogBody($className, $title, $message, self::INFO);
        $fileName = $this->getFileName($fileName);
        $this->record(self::INFO, $context, $fileName);
    }

    public function error($title, $message = '', $className = '', $fileName = '')
    {
        $context = $this->setLogBody($className, $title, $message, self::ERROR);
        $fileName = $this->getFileName($fileName);
        $this->record(self::ERROR, $context, $fileName);
    }

    public function getFileName($fileName)
    {
        if($fileName === ''){
            $name = $this->fileName;
        }else{
            $name = $fileName;
        }
        return $name . date('Ymd') . '.log';
    }

    private function toFile($path, $context){
        $logData = '';
        if(is_scalar($context)){
            $logData .= "{$context}\n";
        }
        else{
            $logData .= print_r($context, true)."\n";
        }

        if(!file_exists(dirname($path))){
            mkdir(dirname($path), 0777, true);
            //chown(dirname($path), 'nobody');
        }

        if(!file_exists($path)){
            touch($path);
            chmod($path, 0777);
        }

        if (false === file_put_contents($path, $logData, FILE_APPEND)) {
            throw new \Exception('Can not write to file');
        }
        return true;
    }

    private function toLogCenter($context){
        $this->client->rPush('lt-warn-log', json_encode($context) . "\r\n");
    }

    private function setLogBody($className, $title, $message, $level){
        $spanName = '';
        $span = '';
        $parent = '';
        $trace = '';
        return [
            'service' => $this->serverName,
            'timestamp' => date('Y-m-d\TH:i:s\.000O'),
            'level' => $level,
            'thread' => 'main',
            'trace' => $trace,
            'span' => $span,
            'parent' => $parent,
            'logger' => $className,
            'title' => $title,
            'message' => $message,
        ];
    }
}