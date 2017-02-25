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
    public function getPath()
    {
        static $memoize = [];

        if (array_key_exists($this->id, $memoize)) {
            return $memoize[$this->id];
        }

        $path = $this->parents($this->depth)->asArray()->all();
        $path[] = $this->toArray();

        $memoize[$this->id] = \yii\helpers\ArrayHelper::map($path, 'id', 'title');
        return $memoize[$this->id];
    }

    /**
     * Returns array of fullpaths to all childrens
     *
     * @return string[]
     */
    public static function getPathsList($condition = [])
    {
        $roots = self::find()->andFilterWhere($condition)->roots()->all();
        if (!$roots) return [];

        $result = [];

        foreach($roots as $root)
        {
            $result[$root->id] = $root->title;
            self::walkNodes($root, $result);
        }
        return $result;
    }

    private static function walkNodes($node, &$result)
    {
        foreach($node->children(1)->all() as $subnode)
        {
            $result[$subnode->id] = implode(' / ', $subnode->getPath());
            self::walkNodes($subnode, $result);
        }
    }

    public function tryAddSubcategory($title)
    {
        $category = self::find()->andWhere(['title' => $title])->one();
        if ($category) return $category;

        $category = new Category(['title' => $title]);
        $category->appendTo($this, false);
        return $category;

    }

}
