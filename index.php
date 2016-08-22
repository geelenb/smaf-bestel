<?php
include_once 'php/database.php';
include_once 'php/password-protection.php';

$query1 = "SELECT full_name, bier.*, jaar " .
         "FROM `bier` " .
         "INNER JOIN users ON users.uid = bier.uid " .
         "INNER JOIN users_groups on users_groups.uid = bier.uid " .
         "WHERE users_groups.group_id = 14 " .
         "ORDER BY jaar ASC;";

$q1 = $mysqli->query($query1);

$query2 = "SELECT users.uid, jaar, full_name, picture, female, (bier.cola + bier.bier + 2 * bier.duvel) as score " .
          "FROM `bier` " .
          "INNER JOIN users ON users.uid = bier.uid " .
          "INNER JOIN users_groups on users_groups.uid = bier.uid " .
          "WHERE users_groups.group_id = 14 " .
          "ORDER BY score desc ;";

$q2 = $mysqli->query($query2);

$query_bollen = "SELECT uid FROM bier ORDER BY duvel DESC LIMIT 1";
$bollen_id = $mysqli->query("$query_bollen")->fetch_assoc();
if ($bollen_id) {
	$bollen_id = $bollen_id['uid'];
}

$query_groen = "SELECT uid FROM bier ORDER BY cola DESC LIMIT 1";
$groen_id = $mysqli->query("$query_groen")->fetch_assoc();
if ($groen_id) {
	$groen_id = $groen_id['uid'];
}

$query_wit = "SELECT uid FROM bier ORDER BY cola DESC LIMIT 1";
$wit_id = $mysqli->query("$query_wit")->fetch_assoc();
if ($wit_id) {
	$wit_id = $wit_id['uid'];
}

$query_ploeg = "SELECT jaar, sum(cola + bier + 2 * duvel) as score FROM bier GROUP BY jaar ORDER BY score DESC LIMIT 1";
$ploeg = $mysqli->query("$query_ploeg")->fetch_assoc();
if ($ploeg) {
	$ploeg = $ploeg['jaar'];
}


function convert_utf8( $string ) { 
    if ( strlen(utf8_decode($string)) == strlen($string) ) {   
        // $string is not UTF-8
        return iconv("ISO-8859-1", "UTF-8", $string);
    } else {
        // already UTF-8
        return $string;
    }
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>SMAF Bier</title>
		<script src="./material.min.js"></script>
		<meta charset="utf-8">
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable = no">
		<meta name="generator" content="SMAF Bier" />

		<meta name="theme-color" content="#ffc107"><!-- Chrome for Android theme color -->
		<link rel="manifest" href="manifest.json"><!-- Web Application Manifest -->

		<!-- Add to homescreen for Chrome on Android -->
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="application-name" content="SMAF Bier">
		<link rel="icon" sizes="192x192" href="images/touch/chrome-touch-icon-192x192.png">

		<!-- Add to homescreen for Safari on iOS -->
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
		<meta name="apple-mobile-web-app-title" content="SMAF Bier">
		<link rel="apple-touch-icon" href="images/touch/apple-touch-icon.png">

		<meta name="msapplication-TileImage" content="images/touch/ms-touch-icon-144x144-precomposed.png">
		<meta name="msapplication-TileColor" content="#ffc107">

		<!-- normale boel -->
		<link rel="stylesheet" href="./material.min.css" />
		<link rel="stylesheet" href="https://storage.googleapis.com/code.getmdl.io/1.2.0/material.amber-yellow.min.css" />
		<link href="https://fonts.googleapis.com/css?family=Karla:400,700" rel="stylesheet">
		<link rel="stylesheet" href="index.css" />
		<script src='index.js'></script>
		<style type="text/css">
			main { background: <?php # night theme
								if (date("H") < '06' || date("H") > '18') {
									echo '#222';
								} else {
									echo '#eee';
								}
							?> }
		</style>
	</head>
	<body style='overflow: hidden;'>
		<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header mdl-layout--fixed-tabs">
			<header class="mdl-layout__header">
				<div class="mdl-layout__header-row" style='padding: 0;'>
					<span class="mdl-layout-title">SMAF Bier</span>
				</div>
				<div class="mdl-layout__tab-bar mdl-js-ripple-effect">
					<a href="#bestellen" class="mdl-layout__tab is-active">Bestellen</a>
					<a href="#ranking" class="mdl-layout__tab">Ranking</a>
				</div>
			</header>
			<main class="mdl-layout__content" style='overflow-y: scroll;'>
			<section class="mdl-layout__tab-panel is-active" id="bestellen" style="padding-bottom: 32px;">
				<div class="page-content">
					<div class="mdl-card mdl-shadow--2dp">
						<div class="mdl-textfield mdl-js-textfield" style='margin: auto'>
							<input class="mdl-textfield__input" type="text" id="sample1" onkeyup="filter(this)">
							<label class="mdl-textfield__label" for="sample1">Zoeken...</label>
						</div>
					</div>

					<div class="mdl-card mdl-shadow--2dp jaarkaart">
						<table>
							<tbody>
								<?php
									$prev_jaar = 0;
									while($u = $q1->fetch_assoc()) {
										if ($prev_jaar !== 0 && $prev_jaar != $u['jaar']) {
											echo '</tbody></table></div>';
											echo '<div class="mdl-card mdl-shadow--2dp jaarkaart"><table><tbody>';
										}

										$prev_jaar = $u['jaar'];
										
										echo '<tr data-naam=" ' . strtoupper(convert_utf8($u['full_name'])) . "\">\n";
										# naam
										echo '<td>' .
										     htmlentities(convert_utf8($u['full_name'])) .
										     "</td>\n";
										# cola-knop
										echo '<td class="icon-td">' .
											 	'<a href="#" ' .
													'onclick="iconClicked(this)" ' .
													'class="mdl-badge mdl-badge--overlap" ' .
													'data-drank="cola" ' .
													'data-badge="' . $u['cola'] . '" ' .
													'data-uid="' . $u['uid'] . '" ' .
													'data-naam="' . htmlentities(convert_utf8($u['full_name'])) . '">' .
													'<img class="drink-icon" src="img/cola.jpg" />' .
											 	'</a>' .
											"</td>\n";
										# bier-knop
										echo '<td class="icon-td">' .
											 	'<a href="#" ' .
													'onclick="iconClicked(this)" ' .
													'class="mdl-badge mdl-badge--overlap" ' .
													'data-drank="bier" ' .
													'data-badge="' . $u['bier'] . '" ' .
													'data-uid="' . $u['uid'] . '" ' .
													'data-naam="' . htmlentities(convert_utf8($u['full_name'])) . '">' .
													'<img class="drink-icon" src="img/bier.png" />' .
											 	'</a>' .
											"</td>\n";
										# duvel-knop
										echo '<td class="icon-td">' .
											 	'<a href="#" ' .
													'onclick="iconClicked(this)" ' .
													'class="mdl-badge mdl-badge--overlap" ' .
													'data-drank="duvel" ' .
													'data-badge="' . $u['duvel'] . '" ' .
													'data-uid="' . $u['uid'] . '" ' .
													'data-naam="' . htmlentities(convert_utf8($u['full_name'])) . '">' .
													'<img class="drink-icon" src="img/duvel.png" />' .
											 	'</a>' .
											"</td>\n";
										echo "</tr>\n";
									}
								?>
							</tbody>
						</table>
					</div>
				</div>
				<div id="progressbar" class="mdl-progress mdl-js-progress mdl-progress__indeterminate"></div>
				<div id="snackbar" class="mdl-js-snackbar mdl-snackbar">
				  <div class="mdl-snackbar__text"></div>
				  <button id="snackbarbutton" class="mdl-snackbar__action" type="button"></button>
				</div>
			</section>
			<section class="mdl-layout__tab-panel" id="ranking">
				<div class="page-content">
					<div class="mdl-shadow--2dp" id="refreshbar" style="display: none">
						<span><a id='refreshlink' href="./">Ververs</a> de pagina om de nieuwe stand te zien.</span>
					</div>
					<div class="mdl-card mdl-shadow--2dp" style="min-height: 0">
						<ul class="demo-list-control mdl-list">
							<?php
								$i = 1;
								$wit_found = false;
								$roze_found = false;
								while($u = $q2->fetch_assoc()) {
									echo "\n" .
									     '<li class="mdl-list__item mdl-list__item--two-line">' . 
									     '<span class="mdl-list__item-primary-content">' .
									     '<i class="material-icons mdl-list__item-avatar" style="background: url(http://smaf.be/' . $u['picture'] . ')"></i>';
										
									if ($i === 1) {
										echo '<i class="mdl-list__item-avatar" style="background: url(img/geel.png); float:right"></i>';
									}
								
									if ($wit_found === false && $u['uid'] === 1998) { 
										$wit_found = true;
										echo '<i class="mdl-list__item-avatar" style="background: url(img/wit.png); float:right"></i>';
									}
								
									if ($roze_found === false && $u['female']) { 
										$roze_found = true;
										echo '<i class="mdl-list__item-avatar" style="background: url(img/roze.png); float:right"></i>';
									}
								
									if ($u['uid'] === $bollen_id) {
										echo '<i class="mdl-list__item-avatar" style="background: url(img/bollen.png); float:right"></i>';
									}
								
									if ($u['uid'] === $groen_id) {
										echo '<i class="mdl-list__item-avatar" style="background: url(img/groen.png); float:right"></i>';
									}
								
									if ($u['jaar'] === $ploeg) {
										echo '<i class="mdl-list__item-avatar" style="background: url(img/team.png); float:right"></i>';
									}
											
									echo '<span>' . $i . '. ' . convert_utf8($u['full_name']) . '</span>';
									echo '<span class="mdl-list__item-sub-title">' . $u['score'] . ' km</span>';
									echo '</span></li>';
									$i++;
								}
							?>
						</ul>
					</div>
				</section>
			</main>
		</div>
	</body>
</html>