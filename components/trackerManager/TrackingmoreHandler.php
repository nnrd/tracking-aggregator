<?php
namespace app\components\trackerManager;

use Yii;
use app\models\ApiOperation;
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

            if (self::responseSuccess($response))
            {
                if (isset($response['json']->data[0]->code))
                {
                    $tracking->carrier = $response['json']->data[0]->code;
                    $tracking->updateTracked();
                    $tracking->save();
                }
            }
            elseif (self::responseFail($response, 4032)) // Bogus carrier, don't mess up anymore
            {
                $tracking->status = Tracking::STATUS_DISABLED;
                $tracking->updateTracked();
                $tracking->save();
            }

            $transaction->commit();
            self::updateSuggestions($response['api_operation']);
            return $response['api_operation'];
        }
        catch(\Exception $e)
        {
            $transaction->rollback();
            return false;
        }
    }

    public function registerTrackings($trackings)
    {
        $transaction = Yii::$app->db->beginTransaction();
        /*
         * try
         * {
         */
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
            }

            $transaction->commit();
            self::updateSuggestions($response['api_operation']);
            return $response['api_operation'];
        /*
         * }
         * catch(\Exception $e)
         * {
         *     $transaction->rollback();
         *     return false;
         * }
         */
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
            }

            $transaction->commit();
            self::updateSuggestions($response['api_operation']);
            return $response['api_operation'];
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
                $tracking->save();
            }
            elseif (self::responseFail($response, 4017)) // Not found
            {
                $tracking->status = Tracking::STATUS_DISABLED;
                $tracking->updateTracked();
                $tracking->save();
            }

            $transaction->commit();
            self::updateSuggestions($response['api_operation']);
            return $response['api_operation'];
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

    protected static function updateSuggestions($apiOp)
    {
        if ($apiOp->code == 429)
        {
            $apiOp->suggestion = ApiOperation::SUG_HOLDOFF;
        }
    }

    protected static function responseFail($response, $code)
    {
        return isset($response['json']->meta->code) && ($response['json']->meta->code == $code);
    }

}
