<?php
namespace app\components\requestManager;

use app\models\ApiOperation;

class RequesterRegistrator implements Requester
{
    protected $instance;

    public function  __construct($requesterInstance)
    {
        $this->instance = $requesterInstance;
    }

    public function send($trackings, $action, $path, $data = null)
    {
        $api_op = new ApiOperation([
            'url' => $instance->url,
            'path' => $path,
            'request' => $data,
            'status' => ApiOperation::STATUS_REQUESTED,
        ]);
        $api_op->save();

        $api_op->liskTrackings($trackings);

        $response = $this->instance->send($action, $path, $data);

        $api_op->code = $response['code'];
        $api_op->response = $response['body'];
        $api_op->status = ApiOperation::STATUS_RESPONDED;
        $api_op->save();

        $response['api_operation'] = $api_op;

        return $response;
    }


}
