'use strict';

var app = angular.module('app', [
    'ngRoute',          // $routeProvider
    'mgcrea.ngStrap',   // bs-navbar, data-match-route directives
    'controllers'       // Our module frontend/web/js/controllers.js
]);

app.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
        when('/', {
            templateUrl: 'views/site/index.html'
        }).
        when('/about', {
            templateUrl: 'views/site/about.html'
        }).
        when('/contact', {
            templateUrl: 'views/site/contact.html',
            controller: 'ContactController'
        }).
        when('/login', {
            templateUrl: 'views/site/login.html',
            controller: 'LoginController'
        }).
        when('/dashboard', {
            templateUrl: 'views/site/dashboard.html',
            controller: 'DashboardController'
        }).
        otherwise({
            templateUrl: 'views/site/404.html'
        });
    }
]);
