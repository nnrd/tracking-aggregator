<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile[]
     */
    public $files;
    public $category_id;
    public $subcategory_name;
    public $create_subcategory;

    public function rules()
    {
        return [
            [['category_id', 'create_subcategory'], 'integer'],
            [['subcategory_name'], 'string', 'max' => 255],
            [['files'], 'file', 'skipOnEmpty' => false,
             'extensions' => ['csv', 'xls', 'xlsx'], 'maxFiles' => 20,
             'checkExtensionByMimeType' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'category_id' => Yii::t('app', 'Category'),
            'subcategory_name' => Yii::t('app', 'New subcategory name'),
            'create_subcategory' => Yii::t('app', 'Create new subcategory for uploaded trackings'),
            'files' => Yii::t('app', 'Upload files'),
        ];
    }


    public function upload()
    {
        if ($this->validate())
        {
            return Yii::$app->importManager->process($this);
        }
        return false;
    }

}
