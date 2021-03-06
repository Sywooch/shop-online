<?php

namespace app\models;

use app\components\AliExpressParser;
use yii\db\ActiveRecord;

/**
 * Class Product
 * @package app\models
 *
 * @property string $currency
 * @property double $price
 * @property string $seo_url
 * @property string $url
 * @property string $image
 * @property string $description
 * @property double $rating
 * @property bool $moderated
 * @property string $created
 * @property string $updated
 *
 * @property Comment[] $comments
 * @property Picture[] $pictures
 * @property Property[] $properties
 * @property Tag[] $tags
 */
class Product extends ActiveRecord
{
    // Валюты
    const CURRENCY_USD = "USD";
    const CURRENCY_RUB = "RUB";

    /** @var bool Постинг в соцсети */
    public $posting;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['id', 'safe'],
            [['price', 'rating'], 'double'],
            ['currency', 'in', 'range' => array_keys(self::getCurrencyList())],
            [['url'], 'unique'],
            [['url', 'image'], 'url'], // ?
            [['url', 'seo_url', 'image', 'name'], 'filter', 'filter' => 'strip_tags'],
            [['url', 'seo_url', 'image', 'name'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 65535],
            ['moderated', 'in', 'range' => [0, 1]],
            [['created', 'updated'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['name', 'url', 'price', 'currency', 'image'], 'required'],
            [['posting'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'price' => 'Цена',
            'currency' => 'Валюта',
            'name' => 'Название',
            'description' => 'Описание',
            'seo_url' => 'ЧПУ',
            'image' => 'Фото',
            'moderated' => 'Одобрен',
            'created' => 'Создан',
            'pictures' => 'Фотографии',
            'tags' => 'Теги',
            'posting' => 'Постинг в соцсети',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPictures()
    {
        return $this->hasMany(Picture::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperties()
    {
        return $this->hasMany(Property::className(), ['product_id' => 'id'])->orderBy('id');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])
            ->viaTable('tag_for_product', ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['product_id' => 'id'])->orderBy('date DESC');
    }

    /**
     * Список валют
     *
     * @return array
     */
    public static function getCurrencyList()
    {
        return [
            self::CURRENCY_USD => self::CURRENCY_USD,
            self::CURRENCY_RUB => self::CURRENCY_RUB,
        ];
    }

    /**
     * Добавление товара по URL
     *
     * @param $url
     * @return Product|null
     * @throws \yii\db\Exception
     */
    public static function add($url)
    {
        $product = new Product();
        $product->created = date("Y-m-d H:i:s");
        $parser = new AliExpressParser();
        $product->attributes = $parser->getProduct($url);

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if (!$product->save()) {
                throw new \Exception();
            }

            $pictures = $parser->getPictures($url);
            if (!empty($pictures)) {
                foreach ($pictures as $picture) {
                    Picture::add($product, $picture);
                }
            }

            $properties = $parser->getProperties($url);
            if (!empty($properties)) {
                for ($i = 0; $i < count($properties[0]); $i++) {
                    Property::add($product, $properties[0][$i], $properties[1][$i]);
                }
            }

            $transaction->commit();
            return $product;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return null;
        }
    }

    /**
     * Статистика по товарам на модерацию
     *
     * @return array|false
     */
    public static function getStatistic()
    {
        $sql = <<<sql
SELECT
    COUNT(CASE WHEN p.`moderated` = TRUE AND DATE(p.`created`) = :today THEN 1 ELSE NULL END) AS moderatedToday,
    COUNT(CASE WHEN DATE(p.`created`) = :today THEN 1 ELSE NULL END) AS totalToday,
    COUNT(CASE WHEN p.`moderated` = TRUE THEN 1 ELSE NULL END) AS moderated,
    COUNT(p.`id`) AS total
FROM `product` p
sql;
        return \Yii::$app->getDb()->createCommand($sql, [':today' => date("Y-m-d")])->queryOne();
    }

}