<?php

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\View;

class AngularAsset extends AssetBundle
{
    public $sourcePath = '@bower';
    public $js = [
        'angular/angular.js',
        'angular-route/angular-route.js',
        'angular-strap/dist/angular-strap.js',
        'angular-permission/dist/angular-permission.js',
        'angular-permission/dist/angular-permission-ng.js',
    ];
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];
}
