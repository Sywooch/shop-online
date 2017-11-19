<?php

namespace app\models\admin;

use app\models\Product;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * Class ProductFilter
 * @package app\models\admin
 *
 * @property int $pageSize
 * @property string $tag
 */
class ProductFilter extends Product
{
    /**
     * @var int
     */
    public $pageSize = 50;

    /**
     * @var string
     */
    public $tag;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'moderated', 'created', 'tag'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'id' => 'ID',
            'tag' => 'Тег',
        ]);
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find()->joinWith(['tags'])->distinct();

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

        $query->andFilterWhere(['=', 'product.`id`', $this->id]);
        $query->andFilterWhere(['like', 'product.`name`', $this->name]);
        $query->andFilterWhere(['=', 'product.`moderated`', $this->moderated]);
        $query->andFilterWhere(['like', 'product.`created`', $this->created]);
        $query->andFilterWhere(['like', 'tag.name', $this->tag]);

        return $dataProvider;
    }
}
