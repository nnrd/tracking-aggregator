<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Tracking;

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

    public function process()
    {

    }
}
