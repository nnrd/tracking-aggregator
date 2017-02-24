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
        echo "Detect $trackNumber carrier\n";
        $tracking = Tracking::find()->andWhere(['track_number' => $trackNumber])->one();
        if ($tracking)
        {
            echo "Tracking found, send request\n";
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

    public function actionDelete($trackNumber)
    {
        $tracking = Tracking::find()->andWhere(['track_number' => $trackNumber])->one();
        if ($tracking)
        {
            $this->api->deleteTracking($tracking);
        }
    }

    public function process()
    {

    }
}
