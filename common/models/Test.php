<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%test}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $question_count
 *
 * @property Answer[] $answers
 * @property User $user
 */
class Test extends \yii\db\ActiveRecord
{
    // this values can be taken from parameters of testing system or test type
    const DEFAULT_QUESTION_COUNT = 3;  // 17  // here it equals to total word count in the example word set
    const MAX_WRONG_ANSWERS = 3;
    const ANSWER_COUNT = 4;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%test}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'question_count'], 'required'],
            [['user_id', 'question_count'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'question_count' => Yii::t('app', 'Question Count'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswers()
    {
        return $this->hasMany(Answer::className(), ['test_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function calcWrongAnswerCount()
    {
        return Answer::find()->where(['test_id' => $this->id, 'is_correct' => 0])->count();
    }
}
