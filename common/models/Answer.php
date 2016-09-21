<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%answer}}".
 *
 * @property integer $id
 * @property integer $test_id
 * @property integer $question_number
 * @property string $question_word
 * @property string $answer_word
 *
 * @property Test $test
 */
class Answer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%answer}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['test_id', 'question_number', 'question_word'], 'required'],
            [['answer_word'], 'required', 'message' => 'Выберите ответ'],
            [['test_id', 'question_number'], 'integer'],
            [['question_word', 'answer_word'], 'string', 'max' => 100],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => Test::className(), 'targetAttribute' => ['test_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'test_id' => Yii::t('app', 'Test ID'),
            'question_number' => Yii::t('app', 'Question Number'),
            'question_word' => Yii::t('app', 'Question Word'),
            'answer_word' => Yii::t('app', 'Answer Word'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTest()
    {
        return $this->hasOne(Test::className(), ['id' => 'test_id']);
    }

    public function isCorrect()
    {
        $word = Word::findOne($this->question_word);
        // maybe better store isEnglishQuestionWord attribute
        $isCorrect = (
               $word->en == $this->question_word && $word->ru == $this->answer_word
           ||  $word->ru == $this->question_word && $word->en == $this->answer_word
        );

        return $isCorrect;
    }
}
