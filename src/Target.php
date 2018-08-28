<?php

namespace vman\logzio;

/**
 * Created by PhpStorm.
 * User: swop
 * Date: 28.08.2018
 * Time: 19:45
 */
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\VarDumper;
use LogzIO\LogzIOGuzzle;
use LogzIO\LogzIOElasticaClient;

class FileTarget extends Target
{
    private $client;

    /**
     * @var string
     */
    public $apiKey = '';

    /**
     * @var string
     */
    public $type = 'record';

    /**
     *
     */
    public function init()
    {
        parent::init();

        $config              = [];
        $config['transport'] = new LogzIOGuzzle();
        $config['transport']->setToken($this->apiKey);
        $config['transport']->setType($this->type);

        $client = new LogzIOElasticaClient($config);
    }

    /**
     *
     */
    public function export()
    {
        $documents = [];

        foreach ($this->messages as $id => $message) {
            $documents[] = new \Elastica\Document($id, $this->formatMessage($message));
        }

        $resp = $this->client->addDocuments($documents);
    }

    /**
     * @param $message
     * @return array
     */
    private function formatMessage($message)
    {
        list($text, $level, $category, $timestamp) = $message;

        if (!is_string($text)) {
            // exceptions may not be serializable if in the call stack somewhere is a Closure
            if ($text instanceof \Throwable || $text instanceof \Exception) {
                $text = (string)$text;
            } else {
                $text = VarDumper::export($text);
            }
        }

        return [
            'message'   => $text,
            'level'     => $level,
            'category'  => $category,
            'timestamp' => $timestamp,
        ];
    }
}