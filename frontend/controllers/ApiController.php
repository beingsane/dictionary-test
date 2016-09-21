<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use yii\rest\Controller;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use frontend\models\StartTestForm;
use common\models\User;
use common\models\Word;
use common\models\Test;
use common\models\Answer;

/**
 * API controller
 */
class ApiController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'only' => ['dashboard'],
        ];

        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['dashboard'],
            'rules' => [
                [
                    'actions' => ['dashboard'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Adds new user on test start and performs his login.
     *
     * @return mixed
     */
    public function actionStartTest()
    {
        $data = Yii::$app->getRequest()->getBodyParams();
        $form = new StartTestForm();

        // We use default User model, so email and password have stub values

        if ($form->load($data, '') && $form->validate()) {
            $user = User::findOne(['username' => $form->username]);
            if (!$user) {
                $user = new User();
                $user->username = $form->username;
                $user->email = $form->username;
                $user->setPassword('');
                $user->generateAuthKey();

                $user->save();
            }

            Yii::$app->user->login($user, 3600 * 24 * 30);


            // this value can be taken from parameters of testing system or test type
            // here it equals to total word count in the example word set
            $questionCount = 17;

            $test = new Test();
            $test->user_id = $user->id;
            $test->question_count = $questionCount;
            $test->save();

            Yii::$app->session->set('testId', $test->id);
            Yii::$app->session->set('questionNumber', 0);

            return [
                'username' => $user->username,
                'accessToken' => Yii::$app->session->id,
            ];
        } else {
            $form->validate();
            return $form;
        }
    }

    /**
     * Performs user logout
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return 'success';
    }

    /**
     * Returns data for current question
     */
    public function actionGetQuestionData()
    {
        $questionData = Yii::$app->session->get('questionData');
        if ($questionData) {
            return $questionData;
        }


        $testId = Yii::$app->session->get('testId');
        $questionNumber = Yii::$app->session->get('questionNumber');

        $questionNumber++;
        Yii::$app->session->set('questionNumber', $questionNumber);

        $answerCount = 4;
        $words = Word::findWordsForQuestion($testId, $answerCount);

        if (count($words) === 0) {
            return ['result' => 'error', 'message' => 'Cannot find words for question'];
        }

        $questionData = $this->generateQuestionData($words);
        Yii::$app->session->set('questionData', $questionData);

        return $questionData;
    }

    /**
     * Generate data for questions based on given words
     * @param Word[] $words
     * @return array
     */
    private function generateQuestionData($words)
    {
        $isEnglishQuestionWord = rand(0, 1);

        $questionWord = '';
        $answerWords = [];
        if ($isEnglishQuestionWord) {
            $questionWord = $words[0]->en;
            foreach ($words as $word) {
                $answerWords[] = $word->ru;
            }
        } else {
            $questionWord = $words[0]->ru;
            foreach ($words as $word) {
                $answerWords[] = $word->en;
            }
        }

        // additional shuffle so that right variant will not be always first
        shuffle($answerWords);

        $questionData = [
            'questionWord' => $questionWord,
            'answerWords' => $answerWords,
        ];

        return $questionData;
    }


    /**
     * Saves answer into database
     */
    public function actionSaveAnswer()
    {
        $data = Yii::$app->getRequest()->getBodyParams();
        $testId = Yii::$app->session->get('testId');
        $questionNumber = Yii::$app->session->get('questionNumber');
        $questionData = Yii::$app->session->get('questionData');

        $answer = new Answer();
        $answer->load($data, '');
        $answer->test_id = $testId;
        $answer->question_number = $questionNumber;
        $answer->question_word = $questionData['questionWord'];

        if (!$answer->validate()) {
            return $answer;
        }

        $answer->save();
        Yii::$app->session->remove('questionData');


        $test = Test::findOne($testId);
        if ($test && $questionNumber >= $test->question_count) {
            return ['result' => 'test_complete'];
        } else {
            return ['result' => 'next_question'];
        }
    }

}
