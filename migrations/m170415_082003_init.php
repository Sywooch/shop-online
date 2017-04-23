<?php

use yii\db\Migration;

class m170415_082003_init extends Migration
{
    public function up()
    {
        $this->createTable('source', [
            'id' => 'pk',
            'url' => 'string not null comment "URL для парсинга"',
            'pattern' => 'string not null comment "Шаблон ссылок"',
            'blocked' => 'boolean default 0 comment "Блокировка"',
            'used' => 'timestamp not null default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP comment "Время последнего обращения"',
        ]);
        $this->createIndex('index_unique_source', 'source', 'url', true);


        $this->createTable('link', [
            'id' => 'pk',
            'url' => 'string not null comment "URL нового товара для парсинга"',
            'source_id' => 'integer null comment "Источник"',
        ]);
        $this->createIndex('index_unique_link', 'link', 'url', true);
        $this->addForeignKey('fk__link__source', 'link', 'source_id', 'source', 'id');


        $this->createTable('product', [
            'id' => 'pk',
            'currency' => 'enum("USD","RUB") comment "Валюта"',
            'price' => 'double not null comment "Цена"',
            'seo_url' => 'string not null comment "Ссылка для ЧПУ"',
            'url' => 'string not null comment "Ссылка на товар"',
            'image' => 'string not null comment "Фото"',
            'name' => 'string comment "Название"',
            'description' => 'text comment "Описание"',
            'moderated' => 'boolean default 0 comment "Прошел модерацию"',
            'created' => 'datetime not null comment "Создан"',
            'updated' => 'timestamp not null default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP comment "Обновлен"',
        ]);
        $this->createIndex('index_unique_product', 'product', 'url', true);
        $this->createIndex('index_product_moderated', 'product', 'moderated');


        $this->createTable('picture', [
            'id' => 'pk',
            'product_id' => 'integer not null comment "Товар"',
            'src' => 'string not null comment "URL фото"',
        ]);
        $this->createIndex('index_product_id', 'picture', 'product_id');
        $this->addForeignKey('fk__picture__product', 'picture', 'product_id', 'product', 'id', 'cascade', 'cascade');


        $this->createTable('property', [
            'id' => 'pk',
            'product_id' => 'integer not null comment "Товар"',
            'name' => 'string(64) comment "Наименование"',
            'value' => 'string(64) comment "Значение"',
//            'index' => 'integer comment "Порядок"'
        ]);
        $this->createIndex('index_property_product_id', 'property', 'product_id');
//        $this->createIndex('index_property_index', 'property', 'index');
        $this->addForeignKey('fk__property__product', 'property', 'product_id', 'product', 'id', 'cascade', 'cascade');


        $this->createTable('tag', [
            'id' => 'pk',
            'name' => 'string not null comment "Тег товара"',
        ]);
        $this->createIndex('index_unique_tag', 'tag', 'name', true);


//        $this->createTable('tag_for_source', [
//            'source_id' => 'integer not null comment "Источник"',
//            'tag_id' => 'integer not null comment "Тег"',
//        ]);
//        $this->addPrimaryKey('pk__tag_for_source', 'tag_for_source', ['source_id', 'tag_id']);
//        $this->addForeignKey('fk__tag_for_source__source', 'tag_for_source', 'source_id', 'source', 'id', 'cascade', 'cascade');
//        $this->addForeignKey('fk__tag_for_source__tag', 'tag_for_source', 'tag_id', 'tag', 'id', 'cascade', 'cascade');


        $this->createTable('tag_for_product', [
            'product_id' => 'integer not null comment "Товар"',
            'tag_id' => 'integer not null comment "Тег"',
        ]);
        $this->addPrimaryKey('pk__tag_for_product', 'tag_for_product', ['product_id', 'tag_id']);
        $this->addForeignKey('fk__tag_for_product__product', 'tag_for_product', 'product_id', 'product', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fk__tag_for_product__tag', 'tag_for_product', 'tag_id', 'tag', 'id', 'cascade', 'cascade');


        $this->createTable('city', [
            'url' => 'string(32) not null comment "ссылка для ЧПУ"',
            'name' => 'string(32) not null comment "Название"',
            'v' => 'string(32) not null comment "в ..."',
            'po' => 'string(32) not null comment "по ..."',
        ]);
        $this->addPrimaryKey('pk__city', 'city', 'url');
    }

    public function down()
    {
        $this->dropTable('city');

        $this->dropForeignKey('fk__link__source', 'link');
        $this->dropTable('link');

//        $this->dropForeignKey('fk__tag_for_source__source', 'tag_for_source');
//        $this->dropForeignKey('fk__tag_for_source__tag', 'tag_for_source');
//        $this->dropTable('tag_for_source');

        $this->dropIndex('index_unique_source', 'source');
        $this->dropTable('source');

        $this->dropForeignKey('fk__property__product', 'property');
        $this->dropIndex('index_property_product_id', 'property');
//        $this->dropIndex('index_property_index', 'property');
        $this->dropTable('property');

        $this->dropForeignKey('fk__picture__product', 'picture');
        $this->dropIndex('index_product_id', 'picture');
        $this->dropTable('picture');

        $this->dropForeignKey('fk__tag_for_product__product', 'tag_for_product');
        $this->dropForeignKey('fk__tag_for_product__tag', 'tag_for_product');
        $this->dropTable('tag_for_product');

        $this->dropIndex('index_product_moderated', 'product');
        $this->dropIndex('index_unique_product', 'product');
        $this->dropTable('product');

        $this->dropIndex('index_unique_tag', 'tag');
        $this->dropTable('tag');
    }
}
