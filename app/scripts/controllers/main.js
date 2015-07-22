'use strict';

/**
 * @ngdoc function
 * @name pluginApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the pluginApp
 */
angular.module('pluginApp')
  .controller('MainCtrl', function ($http, ngNotify, reps, data) {
    var vm = this;
    vm.newMention = {};
    vm.data = data.data.messages;
    vm.reps = reps.data.reps;
    vm.updateData = function () {
      $http.get('http://www.emaildatabases.co.uk/Social/api/index.php/messages')
        .success(function (data) {
          vm.data = data.messages;
        });
    };
    vm.saveNewMention = function () {
      // Simple POST request example (passing data) :
      $http.post('http://www.emaildatabases.co.uk/Social/api/index.php/insertmessage', vm.newMention).
      success(function (data, status, headers, config) {
        // this callback will be called asynchronously
        // when the response is available
        ngNotify.set('That saved just fine..', 'success');
        vm.newMention = {};
        vm.updateData();
      }).
      error(function (data, status, headers, config) {
        // called asynchronously if an error occurs
        // or server returns response with an error status.
        ngNotify.set('That didn\'t save, try again or talk to Lewis', 'error');
      });
    };
    vm.saveForm = function (data) {
      // Simple POST request example (passing data) :
      $http.post('http://www.emaildatabases.co.uk/Social/api/index.php/messages', data).
      success(function (data, status, headers, config) {
        // this callback will be called asynchronously
        // when the response is available
        ngNotify.set('That saved just fine..', 'success');
        vm.updateData();
      }).
      error(function (data, status, headers, config) {
        // called asynchronously if an error occurs
        // or server returns response with an error status.
        ngNotify.set('That didn\'t save, try again or talk to Lewis', 'error');
      });
    };
  });
