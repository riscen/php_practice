var app = angular.module('app', []);

app.controller('ticTacToeController', function($scope, $http){
	$scope.board = [];
	$scope.finish_status = '';
	$scope.finish_message = '';
	$scope.board_disabled = false;

	$http.post('tic-tac-toe.php', {
		board: $scope.board,
		}).then(function(response){
			$scope.board = response.data['board'];
		});

	$scope.selectCell = function(name){
		$http.post('tic-tac-toe.php', {
			board: $scope.board,
			name: name,
			move: true,
		}).then(function(response){
			$scope.board = response.data['board'];
			$scope.finish_status = response.data['finish_status'];
			$scope.finish_message = response.data['finish_message'];
			$scope.board_disabled = response.data['board_disabled'];
		});
	}

	$scope.restart = function() {
		$http.post('tic-tac-toe.php', {
			restart: true,
		}).then(function(response){
			$scope.board = response.data['board'];
			$scope.finish_status = response.data['finish_status'];
			$scope.finish_message = response.data['finish_message'];
			$scope.board_disabled = response.data['board_disabled'];
		});
	}

});
