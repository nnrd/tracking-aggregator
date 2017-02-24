<?php
namespace app\commands;

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
            $this->api->detectCarrier($tracking);
        }
    }

    public function actionRegister($trackNumber)
    {
        $tracking = Tracking::find()->andWhere(['track_number' => $trackNumber])->one();
        if ($tracking)
        {
            $this->api->registerTrackings([$tracking]);
        }
    }

    public function actionCheck($trackNumber)
    {
        $tracking = Tracking::find()->andWhere(['track_number' => $trackNumber])->one();
        if ($tracking)
        {
            $this->api->checkTracking($tracking);
        }
    }


}
