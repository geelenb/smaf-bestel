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
		<!-- <link rel="stylesheet" href="https://storage.googleapis.com/code.getmdl.io/1.2.0/material.amber-yellow.min.css" /> -->
		<link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet" async>
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
			<?php if (isset($_GET['utm_source']) && strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false) { ?>
				body {
					padding-top:20px; 
					background: #ffc107;
				}
			<?php } ?>
		</style>
	</head>
	<body style='overflow: hidden;'>
		<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header mdl-layout--fixed-tabs">
			<header class="mdl-layout__header">
				<div class="mdl-layout__header-row" style='padding: 0;'>
					<span class="mdl-layout-title">SMAF Bier</span>
					<?php if (isset($_GET['utm_source'])) { ?>
						<button class="mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect" onClick="window.location.reload()" style="position: absolute; right: 24px;">
							<svg fill="#fff" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
							    <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
							    <path d="M0 0h24v24H0z" fill="none"/>
							</svg>
					    </button>
				    <?php } ?>
				</div>
				<div class="mdl-layout__tab-bar mdl-js-ripple-effect" style="transition: height 0.5s;">
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
										
										echo '<tr data-naam="' . strtoupper(convert_utf8($u['full_name'])) . "\">\n";
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
						<span><a id='refreshlink' onclick="window.location.reload()">Ververs</a> de pagina om de nieuwe stand te zien.</span>
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
		<?php 
			if (!isset($_GET['utm_source']) && isset($_POST['wachtwoord']) && strpos(strtoupper($_SERVER['HTTP_USER_AGENT']), 'ANDROID') !== false) { ?>
				<div style="background-color: #fff; position: fixed; right: 0; z-index: 5; width: initial; padding: 12px; margin: 12px;" class="mdl-card  mdl-shadow--2dp" onclick="this.remove()">
					<p style="font-weight: bold;">Installeren als app? Klik</p>
					<p style="margin: 0">
						<svg fill="#424242" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
						    <path d="M0 0h24v24H0z" fill="none"/>
						    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
						</svg>
						<svg fill="#424242" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
						    <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
						    <path d="M0 0h24v24H0z" fill="none"/>
						</svg>
						Toevoegen aan startscherm
					</p>
				</div>
			<?php }
			if (!isset($_GET['utm_source']) && isset($_POST['wachtwoord']) && strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false) { ?>
				<div style="background-color: #ffc107; position: fixed; bottom: 12px; z-index: 5; width: 95%; max-width: 320px; padding: 12px; left: 50%; transform: translateX(-50%); margin: 0" class="mdl-card mdl-shadow--2dp" onclick="this.remove()">
					<p style="font-weight: bold; font-size:larger; text-align: center;">Installeren als app? Klik</p>
					<div style="position: relative; left: 50%; transform: translateX(-50%); display: table;">
						<div style="float:left; height: 15vmin; width: 15vmin; background: white; border-radius: 20%;">
							<img style="position: relative; left: 50%; top: 50%; height: 90%; transform: translate(-50%, -50%);" src="img/add-to-homescreen_action-icon-ios7.png">
						</div>
						<p style="float:left; margin: 12px;">
							en dan
						</p>
						<div style="float:left; height: 15vmin; width: 15vmin; background: white; border-radius: 20%;">
							<svg style="position: relative; left: 50%; top: 50%; height: 90%; transform: translate(-50%, -50%)" enable-background="new 0 0 50 50" height="50px" id="Layer_1" version="1.1" viewBox="0 0 50 50" width="50px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
								<rect fill="none" height="50" width="50"/>
								<line fill="none" stroke="#424242" stroke-miterlimit="10" stroke-width="4" x1="9" x2="41" y1="25" y2="25"/><line fill="none" stroke="#424242" stroke-miterlimit="10" stroke-width="4" x1="25" x2="25" y1="9" y2="41"/>
							</svg>
						</div>
					</div>
			<?php 
				} 
			?>
	</body>
</html>