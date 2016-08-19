<?php 
include_once 'database.php';

$mysqli->query("SHOW TABLES LIKE 'bier'");
if($mysqli->affected_rows == 1) {
	echo 'bestond al.';
	exit();
}

$query = "CREATE TABLE IF NOT EXISTS `bier` ( " .
         "  `uid` int(10) NOT NULL PRIMARY KEY, " .
         "  `bier` int(11) DEFAULT '0', " .
         "  `cola` int(11) DEFAULT '0', " .
         "  `duvel` int(11) DEFAULT '0', " .
         "  `jaar` int(11) NOT NULL DEFAULT '1998'" .
         ") ENGINE=InnoDB; ";
$mysqli->query($query);

if ($stmt = $mysqli->prepare("INSERT INTO `bier` (`uid`, `bier`, `cola`, `duvel`) VALUES (?, '0', '0', '0')")) {
	$q = $mysqli->query("SELECT uid FROM users");
	while($r = $q->fetch_assoc()) {
		$stmt->bind_param('i', $r['uid']);
		$stmt->execute();
	}
}	

$query = "UPDATE bier SET jaar = 1992";        $mysqli->query($query);
$query = "UPDATE bier SET jaar = 1993 WHERE uid > 49"; $mysqli->query($query);
$query = "UPDATE bier SET jaar = 1994 WHERE uid > 67"; $mysqli->query($query);
$query = "UPDATE bier SET jaar = 1995 WHERE uid > 87"; $mysqli->query($query);
$query = "UPDATE bier SET jaar = 1996 WHERE uid > 100"; $mysqli->query($query);
$query = "UPDATE bier SET jaar = 1997 WHERE uid > 117"; $mysqli->query($query);
$query = "UPDATE bier SET jaar = 1998 WHERE uid > 140"; $mysqli->query($query);
$query = "UPDATE bier SET jaar = 1999 WHERE uid > 165"; $mysqli->query($query);

echo 'klaar';

$mysqli->close();
?>