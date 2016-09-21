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
use common\models\Test;

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
            Yii::$app->session->set('questionNumber', 1);

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
}
