/**
 * PWCI: a Php Web Chat Integrator
 *
 * BY:  * Reygaert Omar
 * Infolink: https://github.com/OperationsResearch/pwci
 *
 * Client side AngularJS app
 */
var chatApp = angular.module('chatApp', ['ngAnimate', 'ngEmoticons']);

chatApp.controller('chatCtrl', function($scope, Api, $timeout) {
  var RECHECK_TIMEOUT = 2000;
  var INITIAL_SEND_TIMEOUT = 3000;
  if (typeof ajax_object.debug === 'undefined' || ajax_object.debug !== true) {
    $scope.DEBUG = false;
  } else {
    $scope.DEBUG = true;
  }
  $scope.POLLING = false;
  $scope.ERROR = false;
  $scope.total = 0;
  $scope.messages = [];
  $scope.form = {chatMessage: ""};

  Api.init().then(function(response) {
    console.log("Initialization:", response);
    if (response.data && Array.isArray(response.data)) {
      // If session exist resume chat
      var updates = _.map(response.data, function(m) {
        return { src: m.src, text: m.text };
      });
      $scope.messages = $scope.messages.concat(updates);
      $scope.total = $scope.messages.length;
      $scope.POLLING = true;
    } else {
      if (response.data && response.data != 'null') {
        $scope.messages.push({src: "admin", text: "There is an error in the chat. Try to contact the administrator by mail so he can solve it."});
        $scope.total = $scope.messages.length;
        $scope.ERROR = true;
      } else {
        // No session so wait a bit to start new chat or wair for user
        if (typeof ajax_object.startmessage !== 'undefined') {
          $timeout(function() {
            $scope.messages.push({src: "admin", text: ajax_object.startmessage});
            $scope.total = 1;
          }, INITIAL_SEND_TIMEOUT);
        }
      }
    }
  }, function(error) {
    console.warn("ERROR:", error);
  }).finally(function() {
    // Begin polling
    if ($scope.POLLING && !$scope.DEBUG && !$scope.ERROR) {
      $timeout(function() {
        checkForNewMessages();
      }, RECHECK_TIMEOUT);
    }
  });

  $scope.submitMessage = function(text, src) {
    // Get message
    var message = { src: src, text: text };
    $scope.form = {chatMessage: ""};
    $scope.messages.push(message);
    $scope.total = $scope.messages.length;
    Api.sendMessage(message).then(function(response) {
      console.log("Response Of Sent:", response);
      // If message sent start again except in debugmode
      if (!$scope.DEBUG) {
        checkForNewMessages();
      }
    }, function(error) {
      console.warn("ERROR:", error);
    });
  };

  var checkForNewMessages = function() {
    // Check if a new message has sent to client
    Api.getUpdates().then(function(response) {
      console.log("Response of check:", response);
      if (response.data && Array.isArray(response.data)) {
        var updates = _.map(response.data, function(m) {
          return { src: m.src, text: m.text	};
        });
        $scope.messages = $scope.messages.concat(updates);
        $scope.total = $scope.messages.length;
      } else {
        if (response.data && response.data != 'null') {
          $scope.messages.push({src: "admin", text: "There is an error in the chat. Try to contact the administrator by mail so he can solve it."});
          $scope.total = $scope.messages.length;
          $scope.ERROR = true;
        }
      }
    }, function(error) {
      console.warn("ERROR:", error);
    }).finally(function() {
      if (!$scope.DEBUG && !$scope.ERROR) {
        // Check again for new messages
        $timeout(function() {
          checkForNewMessages();
        }, RECHECK_TIMEOUT);
      }
    });
  };

  if ($scope.DEBUG) {
    // In debug mode checkForNewMessages excute only once
    $scope.getUpdates = function() {
      checkForNewMessages();
    };
  }

});

chatApp.factory('Api', function($http, $httpParamSerializerJQLike) {
	var chatRequest = function(action, data) {
    // Param for url that can change depending location of application
		var url = ajax_object.ajax_url;
		var payload = {
			data: data,
			action: action
		};
		return $http({
			url: url,
			method: 'POST',
			data: $httpParamSerializerJQLike(payload),
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded'
			}
		});
	};
	return {
		sendMessage: function(message) {
			//message.host = 'remote';
			return chatRequest('send_message', message);
		},
		getUpdates: function() {
			return chatRequest('check_messages', {host: 'remote'});
		},
		init: function() {
			return chatRequest('check_messages', {init: true});
		}
	}
});

chatApp.directive('scroll2Bottom', function () {
	return {
		scope: {
			scroll2Bottom: "="
		},
		link: function (scope, element) {
			scope.$watch('scroll2Bottom', function (newValue) {
				if (newValue) {
					var el = angular.element(element);
					el.scrollTop(el[0].scrollHeight);
				}
			});
		}
	}
});
