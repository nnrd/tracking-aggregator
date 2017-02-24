<?php
namespace app\components\trackerManager;

use app\models\Tracking;

class DummyHandler extends \yii\base\Component implements Tracker
{
    public function detectCarrier(Tracking $tracking)
    {
        return true;
    }

    public function registerTrackings($trackings)
    {
        return true;
    }

    public function checkTracking(Tracking $tracking)
    {
        return true;
    }
}
