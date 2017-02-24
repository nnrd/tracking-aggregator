<?php
namespace app\components\requestManager;

class  UnirestManager extends \yii\base\Component implements Requester
{
    public $baseUrl;
    public $headers;

    public function send($action, $path, $data = null)
    {
        switch($action)
        {
            case 'get':
                $response = \Unirest\Reqiest::get($this->baseUrl . $path, $this->headers, $data);
                break;
            case 'post':
                $response = \Unirest\Reqiest::post($this->baseUrl . $path, $this->headers, $data);
                break;
            case 'delete':
                $response = \Unirest\Reqiest::delete($this->baseUrl . $path, $this->headers, $data);
                break;
            default:
                throw new \Exception("Send action `$action` not supported");
        }
        return $this->normalizeResponse($response)
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
