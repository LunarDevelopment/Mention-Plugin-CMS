'use strict';

/**
 * @ngdoc overview
 * @name pluginApp
 * @description
 * # pluginApp
 *
 * Main module of the application.
 */
angular
  .module('pluginApp', [
    'ngAnimate',
    'ngAria',
    'ngCookies',
    'ngMessages',
    'ngResource',
    'ngRoute',
    'ngSanitize',
    'ngTouch',
    'ngNotify'
  ])
  .config(function ($routeProvider) {
    $routeProvider
      .when('/', {
        templateUrl: 'views/main.html',
        controller: 'MainCtrl',
        controllerAs: 'main', 
        resolve: { 
          data: (function ($http) { 
            return $http.get('http://www.emaildatabases.co.uk/Social/api/index.php/messages').success(function(data){
              return data;
            });
          }),
          reps: (function ($http) { 
            return $http.get('http://www.emaildatabases.co.uk/Social/api/index.php/reps').success(function(data){
              return data;
            });
          })
        }
      })
      .when('/about', {
        templateUrl: 'views/about.html',
        controller: 'AboutCtrl',
        controllerAs: 'about'
      })
      .otherwise({
        redirectTo: '/'
      });
  });
