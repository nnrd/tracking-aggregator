<?php
namespace app\components\requestManager;

interface Requester
{
    public function send($trackings, $action, $path, $data = null);
}
