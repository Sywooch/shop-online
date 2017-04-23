<?php

namespace app\models\admin;

use app\models\Product;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class ProductFilter extends Product
{
    public $pageSize = 30;

    public $tag;

    public function rules()
    {
        return [
            [['id', 'name', 'moderated', 'created', 'tag'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'id' => 'ID',
            'tag' => 'Тег',
        ]);
    }

    public function search($params)
    {
        $query = self::find()->joinWith(['tags']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => $this->pageSize],
            'sort' => [
                'defaultOrder' => ['created' => SORT_DESC],
                'attributes' => [
                    'id',
                    'name',
                    'moderated',
                    'created',
                ],
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['=', 'id', $this->id]);
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['=', 'moderated', $this->moderated]);
        $query->andFilterWhere(['like', 'created', $this->created]);
        $query->andFilterWhere(['like', 'tag.name', $this->tag]);

        return $dataProvider;
    }
}
