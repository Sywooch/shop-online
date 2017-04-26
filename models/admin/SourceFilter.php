<?php

namespace app\models\admin;

use yii\data\ActiveDataProvider;

class SourceFilter extends Source
{
    public $pageSize = 30;

    public function rules()
    {
        return [
            [['url', 'pattern', 'blocked', 'used'], 'safe'],
        ];
    }

//    public function attributeLabels()
//    {
//        return [
//            'url' => '',
//            'pattern' => '',
//            'blocked' => '',
//            'used' => '',
//        ];
//    }

    public function search($params)
    {
        $query = Source::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => $this->pageSize],
            'sort' => [
//                'defaultOrder' => ['used' => SORT_ASC],
                'attributes' => [
                    'id',
                    'url',
                    'pattern',
                    'used',
                    'blocked',
                ],
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'url', $this->url]);
        $query->andFilterWhere(['like', 'pattern', $this->pattern]);
        $query->andFilterWhere(['like', 'used', $this->used]);
        $query->andFilterWhere(['=', 'blocked', $this->blocked]);

        return $dataProvider;
    }
}
