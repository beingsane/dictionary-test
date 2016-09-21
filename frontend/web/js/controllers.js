'use strict';

var controllers = angular.module('controllers', []);

controllers.controller('MainController', ['$scope', '$location', '$http', 'AuthService',
    function ($scope, $location, $http, AuthService) {
        $scope.AuthService = AuthService;

        $scope.logout = function () {
            $http.post('api/logout').success(function (data) {
                AuthService.logout();
                $location.path('/login').replace();
            }).error(function (data) {
                alert('Something went wrong');
            });
        };
    }
]);

controllers.controller('SiteLogin', ['$scope', '$http', '$location', 'AuthService',
    function($scope, $http, $location, AuthService) {
        $scope.login = function () {
            $scope.submitted = true;
            $scope.error = {};

            $http.post('api/start-test', $scope.userModel).success(function (data) {
                if (data.username && data.accessToken) {
                    AuthService.login(data.username, data.accessToken);
                    $location.path('/test').replace();
                } else {
                    angular.forEach(data, function (error) {
                        $scope.error['username'] = 'Something went wrong';
                    });
                }
            }).error(function (data) {
                angular.forEach(data, function (error) {
                    $scope.error[error.field] = error.message;
                });
            });
        };
    }
]);


controllers.controller('SiteTest', ['$scope', '$rootScope', 'AuthService',
    function ($scope, $rootScope, AuthService) {
        $scope.AuthService = AuthService;
    }
]);
