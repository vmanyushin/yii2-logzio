# yii2-logzio
Yii2 Logger to logz.io

## usage

```
'log'    => [
    'traceLevel'    => YII_DEBUG ? 3 : 0,
    'flushInterval' => 1,
    'targets'       => [
        [
            'exportInterval' => 1,
            'class'          => 'vman\logzio\Target',
            'levels'         => ['error', 'warning', 'info'],
            'logVars'        => [],
            'apiKey'         => 'APIKEY'
        ],
        ...
```