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
            'action' => $action,
            'url' => $this->instance->baseUrl,
            'path' => $path,
            'request' => json_encode($data),
            'status' => ApiOperation::STATUS_REQUESTED,
        ]);
        if ($api_op->save())
        {
            $api_op->linkTrackings($trackings);
        }
        $response = $this->instance->send($trackings, $action, $path, $data);

        $api_op->code = $response['code'];
        $api_op->response = $response['body'];
        $api_op->status = ApiOperation::STATUS_RESPONDED;
        $api_op->save();

        $response['api_operation'] = $api_op;

        return $response;
    }


}
