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
use common\models\User;
use frontend\models\StartTestForm;

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

            return [
                'username' => $user->username,
                'access_token' => $user->getAuthKey(),
            ];
        } else {
            $form->validate();
            return $form;
        }
    }
}
