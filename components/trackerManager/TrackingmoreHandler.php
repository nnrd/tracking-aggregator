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

    public function detectCarrier(Tracking $tracking)
    {
        $data = ['tracking_number' => $tracker->track_number];
        $response = $this->req->send([$tracking], 'post', '/carrier/detect', $data);

        if (self::responseSuccess($response) isset($response['json']->data[0]->code))
        {
            $tracking->carrier = $response['json']->data[0]->code;
            return true;
        }

        return false;
    }

    public function registerTrackings($trackings)
    {
        $data = [];
        foreach($trackings as $tracking)
        {
            $data[] = [
                'tracking_number' => $tracking->tracking_number,
                'carrier_code' => $tracking->carrier,
            ];
        }

        $response = $this->req->send($trackings, 'post', '/trackings/batch', $data);

        if (self::responseSuccess($response) && isset($response['json']->data->trackings))
        {
            $trackingMap = [];
            foreach($trackings as $tracking)
            {
                $trackingMap[$tracking->track_number] = $tracking;
            }

            foreach($response['json']->data->trackngs as $apiTracking)
            {
                if (array_key_exists($apiTracking->tracking_number, $trackingMap))
                {
                    $tracking = $trackingMap[$apiTracking->tracking_number];
                    $tracking->updateTrackerStatus($apiTracking->status);
                    $tracking->save();
                }
            }
            return true;
        }

        return false;
    }

    public function checkTracking(Tracking $tracking)
    {
        $response = $this->req->send([$tracking], 'get', "/trackings/{$tracking->carrier}/{$tracking->track_number}");

        if (self::responseSuccess($response) && isset($response['json']->data))
        {
            $tracking->updateTrackerStatus($response['json']->data->status);
            $tracking->save();
            return true;
        }

        return false;
    }

    protected static function responseSuccess($response)
    {
        return isset($response['json']->meta->type) && (stricmp($response['json']->meta->type, 'success') == 0);
    }

}
