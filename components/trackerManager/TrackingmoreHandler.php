<?php
namespace app\components\trackerManager;

use Yii;
use app\models\Tracking;

class TrackingmoreHandler extends \yii\base\Component implements Tracker
{
    public $requestParams;
    protected $req;

    public function init()
    {
        $this->req = Yii::$app->requestManager->getRequester($this->requestParams);
    }

    public function detectCarrier(Tracking $tracking)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try
        {
            $data = ['tracking_number' => $tracking->track_number];
            $response = $this->req->send([$tracking], 'post', '/carriers/detect', $data);

            var_dump($response['json']);
            if (self::responseSuccess($response) && isset($response['json']->data[0]->code))
            {
                $tracking->carrier = $response['json']->data[0]->code;
                $tracking->updateTracked();
                $tracking->save();
                $transaction->commit();
                return true;
            }

            $transaction->commit();
            return false;
        }
        catch(\Exception $e)
        {
            $transaction->rollback();
            print_r($e->getMessage());
        }
    }

    public function registerTrackings($trackings)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try
        {
            $data = [];
            foreach($trackings as $tracking)
            {
                $data[] = [
                    'tracking_number' => $tracking->track_number,
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

                foreach($response['json']->data->trackings as $apiTracking)
                {
                    if (array_key_exists($apiTracking->tracking_number, $trackingMap))
                    {
                        $tracking = $trackingMap[$apiTracking->tracking_number];
                        $tracking->updateTrackerStatus($apiTracking->status);
                        $tracking->updateTracked();
                        $tracking->save();
                    }
                }
                $transaction->commit();
                return true;
            }

            $transaction->commit();
            return false;
        }
        catch(\Exception $e)
        {
            $transaction->rollback();
            return false;
        }
    }

    public function checkTracking(Tracking $tracking)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try
        {
            $response = $this->req->send([$tracking], 'get', "/trackings/{$tracking->carrier}/{$tracking->track_number}");

            if (self::responseSuccess($response) && isset($response['json']->data))
            {
                $tracking->updateTrackerStatus($response['json']->data->status);
                $tracking->updateTracked();
                $tracking->save();
                $transaction->commit();
                return true;
            }

            $transaction->commit();
            return false;
        }
        catch(\Exception $e)
        {
            $transaction->rollback();
            return false;
        }
    }

    public function deleteTracking(Tracking $tracking)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try
        {
            $response = $this->req->send([$tracking], 'delete', "/trackings/{$tracking->carrier}/{$tracking->track_number}");

            if (self::responseSuccess($response))
            {
                $tracking->updateTracked();
                $transaction->commit();
                return true;
            }

            $transaction->commit();
            return false;
        }
        catch(\Exception $e)
        {
            $transaction->rollback();
            return false;
        }
    }

    protected static function responseSuccess($response)
    {
        return isset($response['json']->meta->type) && (strcasecmp($response['json']->meta->type, 'success') == 0);
    }

}
