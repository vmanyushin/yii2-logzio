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
use LogzIO\Transport\LogzIOGuzzle;
use LogzIO\LogzIOElasticaClient;

class Target extends \yii\log\Target
{
    private $client;

    /**
     * API key from https://app.logz.io/#/dashboard/settings/general
     * @var string
     */
    public $apiKey = '';

    /**
     * type of message
     * @var string
     */
    public $type = 'record';

    /**
     * constructor
     */
    public function init()
    {
        parent::init();

        $config              = [];
        $config['transport'] = new LogzIOGuzzle();
        $config['transport']->setToken($this->apiKey);
        $config['transport']->setType($this->type);

        $this->client = new LogzIOElasticaClient($config);
    }

    /**
     * send logs to logz.ioo
     */
    public function export()
    {
        $documents = [];

        foreach ($this->messages as $id => $message) {
            $formatMessage = $this->formatMessage($message);
            if ($formatMessage === null) {
                continue;
            }
            $documents[] = new \Elastica\Document($id, $formatMessage);
        }

        $this->client->addDocuments($documents);
    }

    /**
     * return null if string is empty
     * @param $message
     * @return array|null
     */
    public function formatMessage($message)
    {
        list($text, $level, $category, $timestamp) = $message;

        if (!is_string($text)) {
            if ($text instanceof \Throwable || $text instanceof \Exception) {
                $text = (string)$text;
            } else {
                return null;
            }
        }

        return [
            'message'  => $text,
            'level'    => $level,
            'category' => $category,
        ];
    }
}