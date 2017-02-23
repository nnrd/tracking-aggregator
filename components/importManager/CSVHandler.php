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
    public $category;
    public $skipLines = 0;
    public $status;

    protected $imported = 0;

    public function process($file)
    {
        if (($handle = fopen($file->tempName, "r")) !== false)
        {
            while (($csvLine = fgetcsv($handle, 0, ",")) !== false) {

                if ($this->skipLines > 0)
                {
                    $this->skipLines--;
                    continue;
                }

                $this->addTrackingFromLine($csvLine);
            }
            fclose($handle);
        }
        return $this->imported;
    }

    protected function addTrackingFromLine($csvLine)
    {
        if (count($csvLine) >= self::LINE_LENGTH)
        {
            $tracking = Tracking::find()->andFilterWhere(['track_number' => $csvLine[self::INDEX_TRACKING]])->one();
            if ($tracking)
            {
                $tracking->order_id = $csvLine[self::INDEX_ORDER_ID];
                $tracking->first_name = $csvLine[self::INDEX_FIRST_NAME];
                $tracking->last_name = $csvLine[self::INDEX_LAST_NAME];
                $tracking->upload_id = $this->operation->id;
            }
            else
            {
                $tracking = new Tracking([
                    'order_id'     => $csvLine[self::INDEX_ORDER_ID],
                    'first_name'   => $csvLine[self::INDEX_FIRST_NAME],
                    'last_name'    => $csvLine[self::INDEX_LAST_NAME],
                    'track_number' => $csvLine[self::INDEX_TRACKING],
                    'upload_id'    => $this->operation->id,
                ]);
            }

            if (isset($this->category) && $this->category)
            {
                $tracking->category_id = $this->category->id;
            }
            if (isset($this->status))
            {
                $tracking->status = $this->status;
            }

            if ($tracking->save()) $this->imported++;
        }
    }
}
