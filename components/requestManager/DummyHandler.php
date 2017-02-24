<?php
namespace app\components\requestManager;

class  DummyManager extends \yii\base\Component
{
    public $baseUrl;
    public $headers;

    public function get($path, $data)
    {
        return $this->emptyResponse();
    }

    public function post($path, $data)
    {
        return $this->emptyResponse();
    }

    public function delete($path, $data)
    {
        return $this->emptyResponse();
    }

    protected function emptyResponse()
    {
        return [
            'code'    => 200,
            'headers' => '',
            'json'    => [],
            'body'    => '',
        ];
    }
}
