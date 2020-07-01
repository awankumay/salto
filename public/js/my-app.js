var app = angular.module('app', [])
app.config(function($interpolateProvider) {
  // To prevent the conflict of `{{` and `}}` symbols
  // between Blade template engine and AngularJS templating we need
  // to use different symbols for AngularJS.

  $interpolateProvider.startSymbol('<%=');
  $interpolateProvider.endSymbol('%>');
});
app.controller('SelectFileController', function ($scope) {
    $scope.SelectFile = function (e) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $scope.PreviewImage = e.target.result;
            $scope.$apply();
        };

        reader.readAsDataURL(e.target.files[0]);
    };
});
