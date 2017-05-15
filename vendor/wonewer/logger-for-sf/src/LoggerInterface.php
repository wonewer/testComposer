<?php
/**
 * Shein framework project.
 * User: york <yorkding@outlook.com>
 * Date: 12/26/16
 * Time: 4:57 PM
 */
namespace sf\logger;

interface LoggerInterface
{
    public function warn($title, $message = '', $className = '', $fileName = '');

    public function notice($title, $message = '', $className = '', $fileName = '');

    public function debug($title, $message = '', $className = '', $fileName = '');

    public function info($title, $message = '', $className = '', $fileName = '');

    public function error($title, $message = '', $className = '', $fileName = '');
}