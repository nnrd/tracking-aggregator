<?php

namespace app\models;

use Yii;
use creocoder\nestedsets\NestedSetsBehavior;

/**
 * This is the model class for table "category".
 *
 * @property integer $id
 * @property integer $tree
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property string $title
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tree', 'lft', 'rgt', 'depth'], 'integer'],
            [['lft', 'rgt', 'depth', 'title'], 'required'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tree' => Yii::t('app', 'Tree'),
            'lft' => Yii::t('app', 'Lft'),
            'rgt' => Yii::t('app', 'Rgt'),
            'depth' => Yii::t('app', 'Depth'),
            'title' => Yii::t('app', 'Title'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::class,
                'treeAttribute' => 'tree',
                'leftAttribute' => 'lft',
                'rightAttribute' => 'rgt',
                'depthAttribute' => 'depth',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }

    /**
     * Returns array of breadcrumbs path from current node (including) to it's root (including)
     *
     * @return string[]
     */
    public function getPath($withRoot = false)
    {
        $path = $this->parents($this->depth - ($withRoot ? 0 : 1))->asArray()->all();

        if($this->depth > 0 || $withRoot)
        {
            $path[] = $this->toArray();
        }

        return \yii\helpers\ArrayHelper::map($path, 'id', 'title');
    }

    /**
     * Returns array of fullpaths to all childrens
     *
     * @return string[]
     */
    public static function getPathsList($condition = [], $withRoot = false)
    {
        $node = self::find()->andFilterWhere($condition)->limit(1)->one();

        $result = $withRoot
            ? $result = [$node->id => implode(' / ', $node->getPath($withRoot))]
            : [];

        $nodes = ($node instanceof Category && !empty($condition))
            ? $node->children()->all()
            : self::find()->leaves()->all();

        foreach($nodes as $subnode)
        {
            $result[$subnode->id] = implode(' / ', $subnode->getPath($withRoot));
        }

        return $result;
    }
}
