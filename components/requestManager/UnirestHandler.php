<?php
namespace app\components\requestManager;

class  UnirestHandler extends \yii\base\Component implements Requester
{
    public $baseUrl;
    public $headers;

    public function send($tracking, $action, $path, $data = null)
    {
        $body = \Unirest\Request\Body::json($data);

        switch($action)
        {
            case 'get':
                $response = \Unirest\Request::get($this->baseUrl . $path, $this->headers, $body);
                break;
            case 'post':
                $response = \Unirest\Request::post($this->baseUrl . $path, $this->headers, $body);
                break;
            case 'delete':
                $response = \Unirest\Request::delete($this->baseUrl . $path, $this->headers, $body);
                break;
            default:
                throw new \Exception("Send action `$action` not supported");
        }
        return $this->normalizeResponse($response);
    }

    protected function normalizeResponse($response)
    {
        return [
            'code'    => $response->code,
            'headers' => $response->headers,
            'json'    => $response->body,
            'body'    => $response->raw_body,
        ];
    }
}
