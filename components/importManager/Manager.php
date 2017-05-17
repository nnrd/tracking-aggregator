<?php
namespace app\components\importManager;

use Yii;
use yii\helpers\FileHelper;
use yii\helpers\HtmlPurifier;
use app\models\UploadOperation;
use app\models\Category;
use app\models\Tracking;

class Manager extends \yii\base\Component
{

    public $handlers = [
        'text/plain'  => CSVHandler::class, // Ok, just trust
        'text/csv'  => CSVHandler::class,
        'application/vnd.ms-excel' => ExcelHandler::class,
        'application/excel' => ExcelHandler::class,
        'application/vnd.ms-excel' => ExcelHandler::class,
        'application/vnd.msexcel' => ExcelHandler::class,
    ];

    public function process($form)
    {
        $ops = [];
        $category = false;
        if (isset($form->category_id))
        {
            $category = Category::findOne($form->category_id);
            if ($category && $form->create_subcategory && $form->subcategory_name)
            {
                $category = $category->tryAddSubcategory($form->subcategory_name);
                if (!$category || $category->hasErrors())
                {
                    $form->addError('subcategory_name', Yii::t('app', 'Failed to create subcategory {0}',  $form->subcategory_name));
                    return false;
                }
            }
        }

        $skip = 0;
        if (isset($form->skip_lines))
        {
            $skip = $form->skip_lines;
        }

        $status = Tracking::STATUS_NORMAL;
        if (isset($form->status))
        {
            $status = $form->status;
        }

        foreach($form->files as $file)
        {
            if (!$file->hasError)
            {
                $ops[] = $this->processFile($file, $category, $skip, $status);
            }
        }

        return $ops;
    }

    public function processFile($file, $category, $skip, $status)
    {
        $mime = FileHelper::getMimeType($file->tempName);
        $op = $this->createOperation($file->name, $mime);
        if (!$op->save())
        {
            return $op;
        }
        $transaction = Yii::$app->db->beginTransaction();

        try
        {
            if (array_key_exists($mime, $this->handlers))
            {
                $class = $this->handlers[$mime];
                $handler = new $class([
                    'operation' => $op,
                    'category' => $category,
                    'skipLines' => $skip,
                    'status' => $status,
                ]);
                $op->handler = $class;

                $imported = $handler->process($file);
                if ($imported == 0)
                {
                    $transaction->rollback();
                    $op->delete();
                    $op = $this->createOperation($file->name, $mime);
                    $op->addError('filename', Yii::t('app', 'File {0} has no valid tracking data', $file->name));
                    return $op;
                }


                $op->status = UploadOperation::STATUS_PROCESSED;

                if ($op->hasErrors() || !$op->save())
                {
                    $transaction->rollback();
                }
                else
                {
                    $transaction->commit();
                }
                return $op;
            }
            $op->addError('format', Yii::t('app', 'Files of {0} are can not be processed', $mime));
            $transaction->rollback();
            return $op;
        }
        catch(\Exception $e)
        {
            $transaction->rollback();
            return $op;
        }
    }

    protected function createOperation($filename, $mime)
    {
        return new UploadOperation([
            'filename' => HtmlPurifier::process($filename),
            'mime' => $mime,
            'status' => UploadOperation::STATUS_UPLOADED,
            'uploaded_by' => Yii::$app->user->id,
        ]);
    }
}
