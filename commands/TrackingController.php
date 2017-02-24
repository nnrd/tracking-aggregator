<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Tracking;
use app\models\ApiOperation;

class TrackingController extends Controller
{
    protected $api;

    public function init()
    {
        $this->api = Yii::$app->trackerManager->getApi();
    }


    public function actionDetectCarrier($trackNumber)
    {
        $tracking = Tracking::find()->andWhere(['track_number' => $trackNumber])->one();
        if ($tracking)
        {
            $op = $this->api->detectCarrier($tracking);
            print_r(isset($op->response) ? $op->response : 'FAIL');
        }
    }

    public function actionRegister($trackNumber)
    {
        $tracking = Tracking::find()->andWhere(['track_number' => $trackNumber])->one();
        if ($tracking)
        {
            $op = $this->api->registerTrackings([$tracking]);
            print_r(isset($op->response) ? $op->response : 'FAIL');
        }
    }

    public function actionCheck($trackNumber)
    {
        $tracking = Tracking::find()->andWhere(['track_number' => $trackNumber])->one();
        if ($tracking)
        {
            $op = $this->api->checkTracking($tracking);
            print_r(isset($op->response) ? $op->response : 'FAIL');
        }
    }

    public function actionDelete($trackNumber)
    {
        $tracking = Tracking::find()->andWhere(['track_number' => $trackNumber])->one();
        if ($tracking)
        {
            $op = $this->api->deleteTracking($tracking);
            print_r(isset($op->response) ? $op->response : 'FAIL');
        }
    }

    public function actionProcess($minimumStatus = Tracking::STATUS_NORMAL, $maximumStatus = Tracking::STATUS_URGENT)
    {
        // First process higher urgency status
        for($currentStatus = $maximumStatus; $currentStatus >= $minimumStatus; $currentStatus--)
        {
            // Step 1: detect new trackings
            $query = Tracking::find()->andWhere([
                'status' => $currentStatus,
                'carrier' => null,
            ])->orderBy('updated_at ASC');

            if (isset($this->api->requestParams['limits']['detect']))
            {
                $query->limit($this->api->requestParams['limits']['detect']);
            }

            foreach($query->all() as $tracking)
            {
                $op = $this->api->detectCarrier($tracking);
                $this->apiOpSleep($op);
            }

            // Step 2: register new trackings
            $query = Tracking::find()
                ->andWhere([
                    'status' => $currentStatus,
                    'tracker_status' => null,
                ])
                ->andWhere('carrier IS NOT NULL')
                ->orderBy('updated_at ASC');

            if (isset($this->api->requestParams['limits']['register']))
            {
                $query->limit($this->api->requestParams['limits']['register']);
            }

            $trackings = $query->all();
            if ($trackings)
            {
                $op = $this->api->registerTrackings($trackings);
                $this->apiOpSleep($op);
            }
            $trackings = null;

            // Step 3: get statuses
            $query = Tracking::find()
                ->andWhere([
                    'status' => $currentStatus,
                ])
                ->andWhere('carrier IS NOT NULL')
                ->andWhere('tracker_status IS NOT NULL')
                ->orderBy('tracked_at ASC');

            if (isset($this->api->requestParams['limits']['check']))
            {
                $query->limit($this->api->requestParams['limits']['check']);
            }

            foreach($query->all() as $tracking)
            {
                $op = $this->api->checkTracking($tracking);
                $this->apiOpSleep($op);
            }
        }

    }

    public function actionCleanup()
    {
        $query = Tracking::find()->andWhere([
            'status' => Tracking::STATUS_DELETED,
        ])->orderBy('updated_at ASC');

        if (isset($this->api->requestParams['limits']['cleanup']))
        {
            $query->limit($this->api->requestParams['limits']['cleanup']);
        }
        foreach($query->all() as $tracking)
        {
            $op = $this->api->deleteTracking($tracking);
            if ($op->code == 200)
            {
                $tracking->delete();
            }
            $this->apiOpSleep($op);
        }
    }

    protected function apiOpSleep($apiOperation)
    {
        if ($apiOperation)
        {
            if ($apiOperation->suggestion == ApiOperation::SUG_HOLDOFF)
            {
                sleep(isset($this->api->requestParams['holdoff']) ? $this->api->requestParams['holdoff'] : 0);
            }
            else
            {
                sleep(isset($this->api->requestParams['pause']) ? $this->api->requestParams['pause'] : 0);
            }
        }
    }
}
