<?php
namespace app\components\requestManager;

interface Requester
{
    public function send($action, $path, $data = null);
}
