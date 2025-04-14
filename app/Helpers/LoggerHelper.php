<?php

namespace App\Helpers;

use CodeIgniter\Log\Handlers\FileHandler;
use CodeIgniter\Log\Logger;
use Config\Services;

class LoggerHelper
{
    protected $logger;
    protected string $module;
    public int $webId = 0;

    public function __construct(string $module = '', int $webId = 0, string $logFileName = '')
    {
        $config = config('Logger');
        $config->threshold = 9;
        $config->handlers[FileHandler::class]['logFileName'] = $logFileName;
        $this->logger = new Logger($config);
        $this->module = $module;
        $this->webId = $webId;
    }

    private function buildMessage(string $message, array $logData = []): string
    {
        $moduleMessage = '';
        $webIdMessage = '';
        if (!empty($this->module)) {
            $moduleMessage = 'Module:' . $this->module . "；";
        }

        if (!empty($this->webId)) {
            $webIdMessage = 'WebId:' . $this->webId . "；";
        }

        $messageResult = "{$moduleMessage}{$webIdMessage}Info:{$message}";

        if (!empty($logData)) {
            $messageResult .= "；logData：" . json_encode($logData, JSON_UNESCAPED_UNICODE);
        }

        return $messageResult;
    }

    public function info($message, array $logData = [])
    {
        $message = $this->buildMessage($message, $logData);
        $this->logger->info($message);
    }
    public function error($message, array $logData = [])
    {
        $message = $this->buildMessage($message, $logData);
        $this->logger->error($message);
    }

    public function CLIInfo($message, array $logData = [])
    {
        $this->info($message, $logData);
        // 同步输出内容
        ob_flush();
        flush();

        echo $message . PHP_EOL;
    }
}