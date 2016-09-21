<?php

use yii\db\Schema;

class m160921_200114_word_data extends yii\db\Migration
{
    public function up()
    {
        $words = [
            [
                'en' => 'apricot',
                'ru' => 'абрикос',
            ],
            [
                'en' => 'pineapple',
                'ru' => 'ананас',
            ],
            [
                'en' => 'orange',
                'ru' => 'апельсин',
            ],
            [
                'en' => 'watermelon',
                'ru' => 'арбуз',
            ],
            [
                'en' => 'banana',
                'ru' => 'банан',
            ],
            [
                'en' => 'grape',
                'ru' => 'виноград',
            ],
            [
                'en' => 'cherry',
                'ru' => 'вишня',
            ],
            [
                'en' => 'pomegranate',
                'ru' => 'гранат',
            ],
            [
                'en' => 'melon',
                'ru' => 'дыня',
            ],
            [
                'en' => 'strawberry',
                'ru' => 'клубника',
            ],
            [
                'en' => 'coconut',
                'ru' => 'кокос',
            ],
            [
                'en' => 'lemon',
                'ru' => 'лимон',
            ],
            [
                'en' => 'raspberry',
                'ru' => 'малина',
            ],
            [
                'en' => 'mango',
                'ru' => 'манго',
            ],
            [
                'en' => 'pear',
                'ru' => 'персик',
            ],
            [
                'en' => 'pomelo',
                'ru' => 'помело',
            ],
            [
                'en' => 'apple',
                'ru' => 'яблоко',
            ],
        ];

        $this->batchInsert('{{%word}}', [], $words);
    }

    public function down()
    {
        $this->delete('{{%word}}');
    }
}
