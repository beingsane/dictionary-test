'use strict';

var controllers = angular.module('controllers', []);

controllers.controller('MainController', ['$scope', '$location', 'AuthService',
    function ($scope, $location, AuthService) {
        $scope.AuthService = AuthService;

        $scope.logout = function () {
            AuthService.logout();
            $location.path('/login').replace();
        };
    }
]);

controllers.controller('SiteLogin', ['$scope', '$http', '$location', 'AuthService',
    function($scope, $http, $location, AuthService) {
        $scope.login = function () {
            $scope.submitted = true;
            $scope.error = {};

            $http.post('api/start-test', $scope.userModel).success(
                function (data) {
                    if (data.username && data.access_token) {
                        AuthService.login(data.username, data.access_token);
                        $location.path('/test').replace();
                    } else {
                        angular.forEach(data, function (error) {
                            $scope.error['username'] = 'Something went wrong';
                        });
                    }
            }).error(
                function (data) {
                    angular.forEach(data, function (error) {
                        $scope.error[error.field] = error.message;
                    });
                }
            );
        };
    }
]);
