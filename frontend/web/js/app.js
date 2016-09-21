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

app.service('AuthService', ['$window', '$location',
    function($window) {
        this.isAuthorized = function($scope) {
            return Boolean($window.sessionStorage.accessToken);
        };

        this.getUsername = function($scope) {
            return String($window.sessionStorage.username);
        };

        this.login = function(username, accessToken) {
            $window.sessionStorage.username = username;
            $window.sessionStorage.accessToken = accessToken;
        }

        this.logout = function($scope) {
            delete $window.sessionStorage.accessToken;
        }
    }
]);

app.config(['$routeProvider', '$httpProvider',
    function($routeProvider, $httpProvider) {
        $routeProvider
            .when('/', {
                redirectTo: '/test',
            })
            .when('/test', {
                templateUrl: 'views/site/test.html',
                controller: 'SiteTest',
                data: {
                    permissions: {
                        only: 'isAuthorized',
                        redirectTo: '/login',
                    }
                }
            })
            .when('/login', {
                templateUrl: 'views/site/login.html',
                controller: 'SiteLogin'
            })
            .when('/results', {
                templateUrl: 'views/site/results.html',
                controller: 'SiteResults',
                data: {
                    permissions: {
                        only: 'isAuthorized',
                        redirectTo: '/login',
                    }
                }
            })
            .otherwise({
                templateUrl: 'views/site/404.html'
            });
        $httpProvider.interceptors.push('authInterceptor');
    }
]);

app.factory('authInterceptor', function ($q, $window, $location) {
    return {
        request: function (config) {
            if ($window.sessionStorage.accessToken) {
                //HttpBearerAuth
                config.headers.Authorization = 'Bearer ' + $window.sessionStorage.accessToken;
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
