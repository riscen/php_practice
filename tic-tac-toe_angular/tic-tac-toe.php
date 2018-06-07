<?php
session_start();

define("SIDE", 3);
define("TIE", -1);
define("NO_WINNER", 0);
define("X_WINNER", 1);
define("O_WINNER", 2);
define("BOARD", "board");
define("X_TURN", "x_turn");
define("TURN_COUNT", "turn_count");
define("RESTART", "restart");
define("HTML_VIEW", "tic-tac-toe.html");
define("PLAYER_1", "X");
define("PLAYER_2", "O");

$name = set_game();
create_board($name);

function set_game() {
	$data = json_decode(file_get_contents('php://input'), true);
	if (!isset($_SESSION[BOARD]) || isset($data[RESTART])) {
		$_SESSION[BOARD] = array(
			array('', '', ''),
			array('', '', ''),
			array('', '', '')
			);
		$_SESSION[X_TURN] = true;
		$_SESSION[TURN_COUNT] = 0;
	}
	elseif(isset($data[move])) {
		$_SESSION[X_TURN] = !$_SESSION[X_TURN];
		$_SESSION[TURN_COUNT]++;
		$_SESSION[BOARD] = $data[BOARD];
	}
	return $data["name"];
}

function create_board($name) {
	for ($i = 0; $i < SIDE; $i++) {
		for ($j = 0; $j < SIDE; $j++) {

			// Set value
			if (isset($name) and $name === $i.$j) {
				$_SESSION[BOARD][$i][$j]['value'] = !$_SESSION[X_TURN]? PLAYER_1: PLAYER_2;
			}
			
			//Set color and disabled
			if($_SESSION[BOARD][$i][$j]["value"] === PLAYER_2 || $_SESSION[BOARD][$i][$j]["value"] === PLAYER_1) {
				switch ($_SESSION[BOARD][$i][$j]["value"]) {
					case PLAYER_1:
						$_SESSION[BOARD][$i][$j]["color"] = "btn-primary";
						break;
					case PLAYER_2:
						$_SESSION[BOARD][$i][$j]["color"] = "btn-danger";
						break;
				}
				$_SESSION[BOARD][$i][$j]["disabled"] = true;
			}
			else {
				if ($_SESSION[X_TURN]) {
					$_SESSION[BOARD][$i][$j]["color"] = "btn-outline-primary";
				}
				else {
					$_SESSION[BOARD][$i][$j]["color"] = "btn-outline-danger";	
				}
				$_SESSION[BOARD][$i][$j]["disabled"] = false;
			}

			// Set name
			$_SESSION[BOARD][$i][$j]["name"] = $i.$j;
		}
	}

	$status = calculate_winner();
	set_game_finished_text($status);
	echo json_encode(array('board'=>$_SESSION[BOARD], 'finish_status'=>$_SESSION['finish_status'], 'finish_message'=>$_SESSION['finish_message'],
		'board_disabled'=>$_SESSION['board_disabled']));
}

function set_game_finished_text($status) {
	if ($status != NO_WINNER) {
		$_SESSION['finish_status'] = "alert alert-success";
		$_SESSION['board_disabled'] = true;
		switch ($status) {
			case TIE:
				$_SESSION['finish_message'] = "You two are too good";
				break;
			case X_WINNER:
				$_SESSION['finish_message'] = "Congratulations player ".PLAYER_1;
				break;
			case O_WINNER:
				$_SESSION['finish_message'] = "Congratulations player ".PLAYER_2;
				break;
			default:
				$_SESSION['finish_status'] = "alert alert-alert";
				$_SESSION['finish_message'] = "Something bad happened";
				break;
		}
	}
	else {
		$_SESSION['board_disabled'] = false;
		$_SESSION['finish_status'] = "";
		$_SESSION['finish_message'] = "";
	}
}

function calculate_winner() {
	if ($_SESSION[TURN_COUNT] == SIDE*SIDE) {
		return TIE;
	}
	$board = $_SESSION[BOARD];
	if ($board[0][0]['value'] != '' && $board[0][0]['value'] == $board[0][1]['value'] && $board[0][0]['value'] == $board[0][2]['value']) {
		return get_winner();
	}
	elseif ($board[0][0]['value'] != '' && $board[0][0]['value'] == $board[1][0]['value'] && $board[0][0]['value'] == $board[2][0]['value']) {
		return get_winner();
	}
	elseif ($board[0][0]['value'] != '' && $board[0][0]['value'] == $board[1][1]['value'] && $board[0][0]['value'] == $board[2][2]['value']) {
		return get_winner();
	}
	elseif ($board[0][1]['value'] != '' && $board[0][1]['value'] == $board[1][1]['value'] && $board[0][1]['value'] == $board[2][1]['value']) {
		return get_winner();
	}
	elseif ($board[0][2]['value'] != '' && $board[0][2]['value'] == $board[1][2]['value'] && $board[0][2]['value'] == $board[2][2]['value']) {
		return get_winner();
	}
	elseif ($board[1][0]['value'] != '' && $board[1][0]['value'] == $board[1][1]['value'] && $board[1][0]['value'] == $board[1][2]['value']) {
		return get_winner();
	}
	elseif ($board[2][0]['value'] != '' && $board[2][0]['value'] == $board[2][1]['value'] && $board[2][0]['value'] == $board[2][2]['value']) {
		return get_winner();
	}
	elseif ($board[2][0]['value'] != '' && $board[2][0]['value'] == $board[1][1]['value'] && $board[2][0]['value'] == $board[0][2]['value']) {
		return get_winner();
	}
	return NO_WINNER;	
}

function get_winner() {
	return !$_SESSION[X_TURN]? X_WINNER: O_WINNER;	
}

?>