<?php

namespace app\models;

use creocoder\nestedsets\NestedSetsQueryBehavior;

class CategoryQuery extends \yii\db\ActiveQuery
{
    public function behaviors() {
        return [
            NestedSetsQueryBehavior::className(),
        ];
    }

    /**
     * Add a query condition to find category by a given title
     *
     * @retun CategoryQuery
     */
    public function byTitle($title)
    {
        return $this->andWhere(['category.title' => $title]);
    }

    public function init()
    {
        parent::init();
        $this->orderBy = [ 'category.title' => SORT_ASC ];
    }

}
