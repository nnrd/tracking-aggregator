<?php

namespace app\components\importManager;

use yii\helpers\FileHelper;
use app\models\Tracking;

class CSVHandler extends \yii\base\Component
{
    const INDEX_ORDER_ID = 0;
    const INDEX_FIRST_NAME = 1;
    const INDEX_LAST_NAME = 2;
    const INDEX_TRACKING = 3;

    const LINE_LENGTH = self::INDEX_TRACKING + 1;

    public $operation;

    public function process($file)
    {
        if (($handle = fopen($file->tempName, "r")) !== false)
        {
            while (($csvLine = fgetcsv($handle, 0, ",")) !== false) {
                $this->addTrackingFromLine($csvLine);
            }
            fclose($handle);
        }
    }

    protected function addTrackingFromLine($csvLine)
    {
        if (count($csvLine) >= self::LINE_LENGTH)
        {
            if (Tracking::find()->andFilterWhere(['order_id' => $csvLine[self::INDEX_ORDER_ID]])->count() == 0)
            {
                $tracking = new Tracking([
                    'order_id'     => $csvLine[self::INDEX_ORDER_ID],
                    'firts_name'   => $csvLine[self::INDEX_FIRST_NAME],
                    'last_name'    => $csvLine[self::INDEX_LAST_NAME],
                    'track_number' => $csvLine[self::INDEX_TRACKING],
                    'upload_id'    => $this->operation->id,
                ]);
                $tracking->save();
            }
        }
    }
}
