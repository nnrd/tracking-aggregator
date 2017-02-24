<?php
namespace app\components\requestManager;

class  UnirestManager extends \yii\base\Component
{
    public $baseUrl;
    public $headers;

    public function get($path, $data)
    {
        return $this->normalizeResponse(\Unirest\Reqiest::get($this->baseUrl . $path, $this->headers, $data));
    }

    public function post($path, $data)
    {
        return $this->normalizeResponse(\Unirest\Reqiest::post($this->baseUrl . $path, $this->headers, $data));
    }

    public function delete($path, $data)
    {
        return $this->normalizeResponse(\Unirest\Reqiest::delete($this->baseUrl . $path, $this->headers, $data));
    }

    protected function normalizeResponse($response)
    {
        return [
            'code'    => $result->code,
            'headers' => $result->headers,
            'json'    => $result->body,
            'body'    => $result->raw_body,
        ];
    }
}
