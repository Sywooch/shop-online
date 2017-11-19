<?php

namespace app\models\admin;

use yii\data\ActiveDataProvider;

/**
 * Class SourceFilter
 * @package app\models\admin
 *
 * @property int $pageSize
 */
class SourceFilter extends Source
{
    /**
     * @var int
     */
    public $pageSize = 30;

    /**
     * @inheritdoc
     */
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

    /**
     * @param $params
     * @return ActiveDataProvider
     */
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
