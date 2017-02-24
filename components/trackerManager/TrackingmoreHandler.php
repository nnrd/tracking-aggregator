<?php
namespace app\components\trackerManager;

use app\models\Tracking;

class TrackingmodeHandler extends \yii\base\Component implements Tracker
{
    protected $req;

    public function init()
    {
        $this->req = Yii::$app->requestManager->getRequester();
    }

    public function getCarrier(Tracking $tracking)
    {
        $resp = $this->req->post('/carrier/detect', [
            'tracking_number' => $tracker->track_number,
        ]);



    }
}
