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


controllers.controller('SiteTest', ['$scope', '$http', '$route', '$location',
    function ($scope, $http, $route, $location) {

        $scope.answerData = {
            answer_word: ''
        };

        $scope.questionData = {
            questionWord: '',
            answerWords: []
        };

        $http.get('api/get-question-data', $scope.answerData).success(function (data) {
            $scope.questionData = data;
        });


        $scope.error = {};

        $scope.saveAnswer = function() {
            $http.post('api/save-answer', $scope.answerData).success(function (data) {
                if (data.result == 'next_question') {
                    $route.reload();
                } else if (data.result == 'test_complete') {
                    $location.path('/results').replace();
                }
            }).error(function (data) {
                angular.forEach(data, function (error) {
                    $scope.error[error.field] = error.message;
                });
            });
        }
    }
]);
