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
                foreach(array_chunk($trackings, 10) as $chunk)
                {
                    $op = $this->api->registerTrackings($chunk);
                    $this->apiOpSleep($op);
                }
            }
            $chunk = null;
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

        // Step 1: Wipe out all deleted
        $query = Tracking::find()->andWhere([
            'status' => Tracking::STATUS_DELETED,
        ])->orderBy('updated_at ASC');

        $requested = 0;
        if (isset($this->api->requestParams['limits']['cleanup']))
        {
            $query->limit($this->api->requestParams['limits']['cleanup']);
        }
        foreach($query->all() as $tracking)
        {
            $op = $this->api->deleteTracking($tracking);
            if ($op->code == 200 || $op->code == 404)
            {
                $tracking->delete();
            }
            $requested++;
            $this->apiOpSleep($op);
        }

        if (isset($this->api->requestParams['limits']['cleanup']) && $requested >= $this->api->requestParams['limits']['cleanup'] )
        {
            return;
        }

        // Step 2: Untrack delivered
        $codes = Tracking::getTrackerStatusCodes();

        $query = Tracking::find()
            ->andWhere([
                'tracker_status' => $codes['delivered'],
                'status' => [Tracking::STATUS_NORMAL, Tracking::STATUS_URGENT],
            ])
            ->orderBy('tracked_at ASC');

        if (isset($this->api->requestParams['limits']['cleanup']))
        {
            $query->limit($this->api->requestParams['limits']['cleanup'] - $requested);
        }
        foreach($query->all() as $tracking)
        {
            $op = $this->api->deleteTracking($tracking);
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
