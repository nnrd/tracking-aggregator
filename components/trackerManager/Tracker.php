<?php
namespace app\components\trackerManager;

use app\models\Tracking;

interface Tracker
{
    public function detectCarrier(Tracking $tracking);
    public function registerTrackings($trackings);
    public function checkTracking(Tracking $tracking);
}
