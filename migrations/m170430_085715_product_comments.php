<?php

use yii\db\Migration;

class m170430_085715_product_comments extends Migration
{
    public function up()
    {
        $this->dropForeignKey('fk__link__source', 'link');
        $this->dropTable('link');

        $this->addColumn('product', 'rating', 'double default 0 comment "Рейтинг"');
        $this->createIndex('index_product_rating', 'product', 'rating');

        $this->createTable('comment', [
            'id' => 'pk',
            'product_id' => 'integer not null comment "Товар"',
            'date' => 'datetime not null comment "Дата"',
            'buyer' => 'string not null comment "Покупатель"',
            'text' => 'text comment "Текст отзыва"',
            'photos' => 'text comment "Фотографии"',
        ]);
        $this->createIndex('index_comment_product_id', 'comment', 'product_id');
        $this->addForeignKey('fk__comment__product', 'comment', 'product_id', 'product', 'id', 'cascade', 'cascade');
    }

    public function down()
    {
        $this->createTable('link', [
            'id' => 'pk',
            'url' => 'string not null comment "URL нового товара для парсинга"',
            'source_id' => 'integer null comment "Источник"',
        ]);
        $this->createIndex('index_unique_link', 'link', 'url', true);
        $this->addForeignKey('fk__link__source', 'link', 'source_id', 'source', 'id');

        $this->dropIndex('index_product_rating', 'product');
        $this->dropColumn('product', 'rating');

        $this->dropForeignKey('fk__comment__product', 'comment');
        $this->dropTable('comment');
    }

}
