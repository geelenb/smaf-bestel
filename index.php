<?php # TODO : wachtwoord-shit
include_once 'php/database.php';
include_once 'php/password-protection.php';

$query1 = "SELECT full_name, bier.*, jaar " .
         "FROM `bier` " .
         "INNER JOIN users ON users.uid = bier.uid " .
         "INNER JOIN users_groups on users_groups.uid = bier.uid " .
         "WHERE users_groups.group_id = 14 " .
         "ORDER BY jaar ASC;";

$q1 = $mysqli->query($query1);

$query2 = "SELECT full_name, picture, (bier.cola + bier.bier + 2 * bier.duvel) as score " .
         "FROM `bier` " .
         "INNER JOIN users ON users.uid = bier.uid " .
         "INNER JOIN users_groups on users_groups.uid = bier.uid " .
         "WHERE users_groups.group_id = 14 " .
         "ORDER BY score desc ;";

$q2 = $mysqli->query($query2);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>SMAF Bier</title>
		<script src="./material.min.js"></script>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<link rel="stylesheet" href="./material.min.css" />
		<link rel="stylesheet" href="https://storage.googleapis.com/code.getmdl.io/1.2.0/material.amber-yellow.min.css" />
		<link href="https://fonts.googleapis.com/css?family=Karla:400,700" rel="stylesheet">
		<link rel="stylesheet" href="index.css" />
		<script src='index.js'></script>
		<style type="text/css">
			main {
				background: <?php 
								if (date("H") < '06' || date("H") > '18') {
									echo '#222';
								} else {
									echo '#eee';
								}
							?>
			}
			
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
								?>
								<tr>
									<td>
										<?php echo $u['full_name']?>		
									</td>
									<td>
										<a href="#" 
										   onclick="iconClicked(this)" 
										   class='mdl-badge mdl-badge--overlap' 
										   data-badge='<?php echo$u['cola'];?>' 
										   data-uid='<?php echo $u['uid'];?>'
										   data-drank='cola'
										   data-naam='<?php echo $u['full_name']?>'>
											<img class='drink-icon' src="img/cola.jpg" />
										</a>
									</td>
									<td>
										<a href="#" 
										   onclick="iconClicked(this)" 
										   class='mdl-badge mdl-badge--overlap' 
										   data-badge='<?php echo $u['bier'];?>' 
										   data-uid='<?php echo $u['uid'];?>'
										   data-drank='bier'
										   data-naam='<?php echo $u['full_name']?>'>
											<img class='drink-icon' src="img/bier.png" />
										</a>
									</td>
									<td>
										<a href="#" 
										   onclick="iconClicked(this)" 
										   class='mdl-badge mdl-badge--overlap' 
										   data-badge='<?php echo$u['duvel'];?>'
										   data-uid='<?php echo $u['uid'];?>'
										   data-drank='duvel'
										   data-naam='<?php echo $u['full_name']?>'>
											<img class='drink-icon' src="img/duvel.png" />
										</a>
									</td>
								</tr>
								<?php
									}
								?>
							</tbody>
						</table>
					</div>
				</div>
				<div id="progressbar" class="mdl-progress mdl-js-progress mdl-progress__indeterminate"></div>
				<div id="snackbar" class="mdl-js-snackbar mdl-snackbar">
				  <div class="mdl-snackbar__text"></div>
				  <button id='snackbarbutton' class="mdl-snackbar__action" type="button"></button>
				</div>
			</section>
			<section class="mdl-layout__tab-panel" id="ranking">
				<div class="page-content">
					<div class="mdl-card mdl-shadow--2dp" style="min-height: 0">
					<ul class="demo-list-control mdl-list">
						<?php
							$i = 1;
							while($u = $q2->fetch_assoc()) {

						?>
							<li class="mdl-list__item mdl-list__item--two-line">
								<span class="mdl-list__item-primary-content">
									<i class="material-icons mdl-list__item-avatar" style="background: url(http://smaf.be/<?php echo $u['picture']?>)"></i>
									
									<span><?php echo $i . '. 	' . $u['full_name'] ?></span>
									<span class="mdl-list__item-sub-title"><?php echo $u['score'] ?> km</span>
								</span>
							</li>
						<?php
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