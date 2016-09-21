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

        $scope.answerData = {answerData: ''};
        $scope.questionData = null;

        $http.get('api/get-question-data', $scope.answerData).success(function (data) {
            $scope.questionData = data;
        });


        $scope.error = {};

        $scope.saveAnswer = function() {
            $http.post('api/save-answer', $scope.answerData).success(function (data) {
                if (data.result == 'next_question') {
                    $route.reload();
                } else if (data.result == 'wrong_answer') {
                    $scope.error['answer_word'] = 'Неправильный ответ';
                } else if (data.result == 'test_complete') {
                    $location.path('/results').replace();
                }
            }).error(function (data) {
                angular.forEach(data, function (error) {
                    $scope.error[error.field] = error.message;
                });
            });
        }

        $scope.clearErrors = function () {
            $scope.error = [];
        }
    }
]);


controllers.controller('SiteResults', ['$scope', '$http',
    function ($scope, $http) {
        $scope.answers = [];
        $http.get('api/get-answers', $scope.answerData).success(function (data) {
            $scope.answers = data;
        });

        $scope.getTotalResult = function () {
            var correctAnswers = [];
            var totalAnswers = [];
            for (var i in $scope.answers) {
                var answer = $scope.answers[i];

                if (totalAnswers.indexOf(answer.question_number) == -1) {
                    totalAnswers.push(answer.question_number);
                }

                if (answer.is_correct == '1') {
                    if (correctAnswers.indexOf(answer.question_number) == -1) {
                        correctAnswers.push(answer.question_number);
                    }
                }
            }

            return correctAnswers.length + ' / ' + totalAnswers.length;
        }
    }
]);
