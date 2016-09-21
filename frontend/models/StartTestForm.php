<?php
namespace frontend\models;

use yii\base\Model;

/**
 * Start test form
 */
class StartTestForm extends Model
{
    public $username;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'string', 'min' => 2, 'max' => 255],
        ];
    }
}
