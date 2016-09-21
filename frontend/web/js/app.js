'use strict';

var app = angular.module('app', [
        'ngRoute',
        'permission',
        'permission.ng',
        'mgcrea.ngStrap',
        'controllers'
    ])
    .run(function (PermPermissionStore, AuthService) {
        PermPermissionStore.definePermission('isAuthorized', function () {
            return AuthService.isAuthorized();
        });
    });

app.service('AuthService', ['$window',
    function($window) {
      this.isAuthorized = function($scope) {
        return Boolean($window.sessionStorage.access_token);
      };
    }
]);

app.config(['$routeProvider', '$httpProvider',
    function($routeProvider, $httpProvider) {
        $routeProvider.
            when('/', {
                templateUrl: 'views/site/index.html',
                data: {
                    permissions: {
                        only: 'isAuthorized',
                        redirectTo: '/login',
                    }
                }
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
        $httpProvider.interceptors.push('authInterceptor');
    }
]);

app.factory('authInterceptor', function ($q, $window, $location) {
    return {
        request: function (config) {
            if ($window.sessionStorage.access_token) {
                //HttpBearerAuth
                config.headers.Authorization = 'Bearer ' + $window.sessionStorage.access_token;
            }
            return config;
        },
        responseError: function (rejection) {
            if (rejection.status === 401) {
                $location.path('/login').replace();
            }
            return $q.reject(rejection);
        }
    };
});