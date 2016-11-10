<?php
namespace ZFDebug\Controller\Plugin\Debug\Plugin\Log;

use ZFDebug\Controller\Plugin\Debug\Plugin\Log;

/**
 * Class Writer
 *
 * @package ZFDebug\Controller\Plugin\Debug\Plugin\Log
 * @author  Octavian Matei <octav@octav.name>
 * @since   10.11.2016
 */
class Writer extends \Zend_Log_Writer_Abstract
{
    /** @var int */
    protected $errors = 0;

    /** @var array */
    protected $messages = [];

    public static function factory($config)
    {
        return new self();
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function getErrorCount()
    {
        return $this->errors;
    }

    /**
     * Write a message to the log.
     *
     * @param array $event event data
     */
    protected function _write($event)
    {
        $output = '<tr>';
        $output .= '<td style="color:%color%;text-align:right;padding-right:1em">%priorityName%</td>';
        $output .= '<td style="color:%color%;text-align:right;padding-right:1em">%memory%</td>';
        $output .= '<td style="color:%color%;">%message%</td></tr>'; // (%priority%)
        $event['color'] = '#C9C9C9';
        // Count errors
        if ($event['priority'] < 7) {
            $event['color'] = 'green';
        }
        if ($event['priority'] < 6) {
            $event['color'] = '#fd9600';
        }
        if ($event['priority'] < 5) {
            $event['color'] = 'red';
            $this->errors++;
        }

        if ($event['priority'] == Log::ZFLOG) {
            $event['priorityName'] = $event['message']['time'];
            $event['memory'] = $event['message']['memory'];
            $event['message'] = $event['message']['message'];
        } else {
            $event['message'] = $event['priorityName'] . ': ' . print_r($event['message'], 1);
            $event['priorityName'] = '&nbsp;';
            $event['memory'] = '&nbsp;';
        }
        foreach ($event as $name => $value) {
            if ('message' == $name) {
                if ((is_object($value) && !method_exists($value, '__toString'))) {
                    $value = gettype($value);
                } elseif (is_array($value)) {
                    $value = $value[1];
                }
            }
            $output = str_replace("%$name%", $value, $output);
        }
        $this->messages[] = $output;
    }
}
