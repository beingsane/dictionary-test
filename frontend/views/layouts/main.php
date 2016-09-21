<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" ng-app="app">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title>Словарь</title>
        <?php $this->head() ?>
    </head>

    <body ng-controller="MainController">
        <?php $this->beginBody() ?>

        <div class="wrap">

            <nav class="navbar-inverse navbar-fixed-top navbar" role="navigation" bs-navbar>
                <div class="container">
                    <div class="navbar-header">
                        <button ng-init="navCollapsed = true" ng-click="navCollapsed = !navCollapsed" type="button" class="navbar-toggle">
                            <span class="sr-only">Переключить навигацию</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#/">Словарь</a>
                    </div>

                    <div ng-class="!navCollapsed && 'in'" ng-click="navCollapsed=true" class="collapse navbar-collapse">
                        <ul class="navbar-nav navbar-right nav">
                            <li data-match-route="/test">
                                <a href="#/test">Тест</a>
                            </li>
                            <li ng-class="{active:isActive('/logout')}" ng-show="AuthService.isAuthorized()" ng-click="logout()"  class="ng-hide">
                                <a href="">Выход ({{AuthService.getUsername()}})</a>
                            </li>
                            <li data-match-route="/login" ng-hide="AuthService.isAuthorized()">
                                <a href="#/login">Вход</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="container">
                <div ng-view></div>
            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <p class="pull-left">&copy; Словарь <?= date('Y') ?></p>

                <p class="pull-right"><?= Yii::powered() ?></p>
            </div>
        </footer>

        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
