<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class CatalogueFilter extends Model
{
    /** @var int - количество товаров на странице */
    public $pageSize = 10;

    /** @var $query string - поисковая строка */
    public $query;

    /** @var $filter bool - видимость фильтра по-умолчанию: 0 - свёрнут, 1 - развёрнут */
    public $filter = 0;

    public $priceLow;
    public $priceHigh;

    // todo добавить выбор валют
    public $currency;

    public function rules()
    {
        return [
            ['filter', 'in', 'range' => [0, 1]],
            [['priceLow', 'priceHigh'], 'integer'],
            [['query'], 'safe'],
        ];
    }


    public function search($params)
    {
        $query = Product::find()->joinWith(['tags', 'properties'])->where(['moderated' => true])->distinct();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => $this->pageSize],
            'sort' => [
                'defaultOrder' => ['created' => SORT_DESC],
                'attributes' => ['price', 'created'],
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // todo !!! дибильные конструкции - с ними надо быть внимательнее !!!
        $query->andFilterWhere(['or',
            ['like', 'product.name', $this->query],
            ['like', 'product.description', $this->query],
            ['like', 'tag.name', $this->query],
            ['like', 'property.name', $this->query],
            ['like', 'property.value', $this->query],
        ]);

        // todo придумать решение для фильтра по цене в зависимости от валюты
        $query->andFilterWhere(['>=', 'product.price', $this->priceLow ? $this->priceLow / 5 : null]);
        $query->andFilterWhere(['<=', 'product.price', $this->priceHigh ? $this->priceHigh / 5 : null]);

//        print_r($query->createCommand()->rawSql); // для проверки sql

        return $dataProvider;
    }
}
