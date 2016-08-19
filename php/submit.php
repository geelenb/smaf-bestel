<?php 
include_once 'database.php';
include_once 'http_response_codes.php';

if (!isset($_POST, $_POST['what'], $_POST['uid'], $_POST['drank'])) {
	http_response_code(400);
	exit();
}

if (($_POST['what'] !== 'add' && $_POST['what'] !== 'undo') || 
	($_POST['drank'] !== 'bier' && $_POST['drank'] !== 'cola' && $_POST['drank'] !== 'duvel')) {
	http_response_code(400);
	exit();
}

$operator = '#';

if ($_POST['what'] === 'add') {
	$operator = '+';
} else {
	$operator = '-';
}

if ($stmt = $mysqli->prepare("UPDATE bier SET " . $_POST['drank'] . " = " . $_POST['drank'] . " " . $operator . " 1 WHERE uid = ?")) {
	$stmt->bind_param("i", $_POST['uid']);
	$stmt->execute();

	if ($stmt->affected_rows !== 1) {
		http_response_code(400);
		exit();
	}

	$stmt->close();
}

if ($stmt = $mysqli->prepare("SELECT " . $_POST['drank'] . " FROM bier WHERE uid = ?")) {
	$stmt->bind_param("i", $_POST['uid']);
	$stmt->execute();

	$new_num = -1;
	$stmt->bind_result($new_num);
	$stmt->fetch();

	$stmt->close();
	
	echo $new_num;
	exit();
}

http_response_code(400);
exit();
?>