<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%answer}}".
 *
 * @property integer $id
 * @property integer $test_id
 * @property integer $question_number
 * @property string $type
 * @property string $question_word
 * @property string $answer_word
 * @property string $is_correct
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
            [['test_id', 'question_number', 'question_word', 'type'], 'required'],
            [['answer_word'], 'required', 'message' => 'Выберите ответ'],
            [['test_id', 'question_number'], 'integer'],
            [['question_word', 'answer_word'], 'string', 'max' => 100],
            [['is_correct'], 'integer'],
            [['type'], 'string', 'max' => 2],
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
            'is_english_word' => Yii::t('app', 'Is English Word'),
            'question_word' => Yii::t('app', 'Question Word'),
            'answer_word' => Yii::t('app', 'Answer Word'),
            'is_correct' => Yii::t('app', 'Is Correct'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTest()
    {
        return $this->hasOne(Test::className(), ['id' => 'test_id']);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $this->is_correct = $this->isCorrect();
        return parent::beforeSave($insert);
    }

    /**
     * Determines if the answer is correct
     * Stores the results in the is_correct property
     * @param $reclac if to recalculate the result
     * @return bool
     */
    public function isCorrect($recalc = false)
    {
        if ($this->is_correct === null || $recalc) {
            $word = (
                $this->type == 'en'
                    ? Word::findOne($this->question_word)
                    : Word::findOne(['ru' => $this->question_word])
            );

            if ($word) {
                $this->is_correct = (
                    $this->type == 'en'
                        ? $word->en == $this->question_word && $word->ru == $this->answer_word
                        : $word->ru == $this->question_word && $word->en == $this->answer_word
                );
            } else {
                $this->is_correct = false;
            }
        }

        return $this->is_correct;
    }
}
