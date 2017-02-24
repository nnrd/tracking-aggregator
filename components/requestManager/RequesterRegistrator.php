<?php
namespace app\components\requestManager;

class RequesterRegistrator implements Requester
{
    protected $instance;

    public function  __construct($requesterInstance)
    {
        $this->instance = $requesterInstance;
    }

    public function send($action, $path, $data = null)
    {
        return $this->instance->send($action, $path, $data);
    }
}
