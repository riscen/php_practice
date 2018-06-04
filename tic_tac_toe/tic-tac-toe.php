<?php
session_start();

function __autoload($class_name) {
	require_once $class_name.'.php';
}

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


set_game();
create_board();

function set_game() {
	if (!isset($_SESSION[BOARD]) or ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST[RESTART]))) {
		$_SESSION[BOARD] = array(
				array('', '', ''),
				array('', '', ''),
				array('', '', ''),
				);
		$_SESSION[X_TURN] = true;
		$_SESSION[TURN_COUNT] = 0;
	}
	elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$_SESSION[X_TURN] = !$_SESSION[X_TURN];
		$_SESSION[TURN_COUNT]++;
	}
}

function create_board() {
	$template = new Template();
	$template->load(HTML_VIEW);

	$board_contents = '';

	for ($i = 0; $i < SIDE; $i++) {
		$board_contents .= "<tr>";
		for ($j = 0; $j < SIDE; $j++) {
			$board_contents .= "<td>";
			$board_contents .= "<input type=\"submit\" class=\"btn ";

			if (isset($_POST[$i.$j])) {
				$_SESSION[BOARD][$i][$j] = !$_SESSION[X_TURN]? PLAYER_1: PLAYER_2;
			}
			
			if($_SESSION[BOARD][$i][$j] === PLAYER_2 || $_SESSION[BOARD][$i][$j] === PLAYER_1) {
				switch ($_SESSION[BOARD][$i][$j]) {
					case PLAYER_1:
						$board_contents .= "btn-primary\"";
						break;
					case PLAYER_2:
						$board_contents .= "btn-danger\"";
						break;
				}
				$board_contents .= " disabled ";
			}
			else {
				if ($_SESSION[X_TURN]) {
					$board_contents .= "btn-outline-primary\"";
				}
				else {
					$board_contents .= "btn-outline-danger\"";	
				}
			}

			$board_contents .= "name=\"".$i.$j."\" value=\"".$_SESSION[BOARD][$i][$j]."\">";
			$board_contents .= "</td>";
		}
		$board_contents .= "</tr>";
	}

	$status = calculate_winner();
	$template->replace("board_contents", $board_contents);
	$template->replace("finish_text", set_game_finished_text($status));
	$template->replace("game_finish", $status == NO_WINNER? "": "disabled");

	$template->publish();
}

function set_game_finished_text($status) {
	if ($status != NO_WINNER) {
		$finish_text = "<div class=\"alert alert-success\">";
		switch ($status) {
			case TIE:
				$finish_text .= "You two are too good";
				break;
			case X_WINNER:
				$finish_text .= "Congratulations player ".PLAYER_1;
				break;
			case O_WINNER:
				$finish_text .= "Congratulations player ".PLAYER_2;
				break;
			default:
				$finish_text = "<div class=\"alert alert-alert\">Something bad happen";
				break;
		}
		$finish_text .= "</div>";
	}
	else {
		$finish_text = "";
	}
	return $finish_text;
}

function calculate_winner() {
	if ($_SESSION[TURN_COUNT] == SIDE*SIDE) {
		return TIE;
	}
	$board = $_SESSION[BOARD];
	if ($board[0][0] != '' && $board[0][0] == $board[0][1] && $board[0][0] == $board[0][2]) {
		return get_winner();
	}
	elseif ($board[0][0] != '' && $board[0][0] == $board[1][0] && $board[0][0] == $board[2][0]) {
		return get_winner();
	}
	elseif ($board[0][0] != '' && $board[0][0] == $board[1][1] && $board[0][0] == $board[2][2]) {
		return get_winner();
	}
	elseif ($board[0][1] != '' && $board[0][1] == $board[1][1] && $board[0][1] == $board[2][1]) {
		return get_winner();
	}
	elseif ($board[0][2] != '' && $board[0][2] == $board[1][2] && $board[0][2] == $board[2][2]) {
		return get_winner();
	}
	elseif ($board[1][0] != '' && $board[1][0] == $board[1][1] && $board[1][0] == $board[1][2]) {
		return get_winner();
	}
	elseif ($board[2][0] != '' && $board[2][0] == $board[2][1] && $board[2][0] == $board[2][2]) {
		return get_winner();
	}
	elseif ($board[2][0] != '' && $board[2][0] == $board[1][1] && $board[2][0] == $board[0][2]) {
		return get_winner();
	}
	return NO_WINNER;	
}

function get_winner() {
	return !$_SESSION[X_TURN]? X_WINNER: O_WINNER;	
}

?>