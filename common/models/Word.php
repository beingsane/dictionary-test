<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%word}}".
 *
 * @property string $en
 * @property string $ru
 */
class Word extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%word}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['en', 'ru'], 'required'],
            [['en', 'ru'], 'string', 'max' => 100],
            [['ru'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'en' => Yii::t('app', 'En'),
            'ru' => Yii::t('app', 'Ru'),
        ];
    }
}
