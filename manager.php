<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<style type="text/css">
			body { margin: 0 auto; }
			.wrap { margin: 0 auto; width: 800px; }
			.col { display: inline-block; width: 350px; vertical-align: top; }
		</style>
		<?php
			$page_title = "API Manager";
			// Initialize stuff
			$msg = ''; // By default, there is no message
			require_once 'includes/functions.php';
			
			$apiquery = getDb()->prepare('SELECT * FROM `members` WHERE `user_id` = ?');

			// If a character name is sent, let's proceed
			if (!empty($_POST['characterName'])) {

				// If keyID and vCode are sent, save them
				if (!empty($_POST['keyID']) && !empty($_POST['vCode'])) {

					$insert = getDb()->prepare('
						INSERT INTO `characters`
							(`characterID`, `characterName`, `keyID`, `vCode`)
						VALUES
							(?, ?, ?, ?)');

					$result = $insert->execute(array(
						$_POST['characterID'],
						$_POST['characterName'],
						$_POST['keyID'],
						$_POST['vCode']
					));

					if ($result) {
						unset($_POST);
						$msg = "New character saved";
					}
				}
				// Otherwise, try to get the ID for the name
				else {
					$name = $_POST['characterName'];

					// Build the URL we want to fetch
					$url = 'https://api.eveonline.com/eve/CharacterID.xml.aspx';
					$url .= '?names=' . urlencode($_POST['characterName']);

					// Get the needed data
					$xml = makeApiRequest($url);

					// We have an error, show it it
					if ($xml->error) {
						$msg = (string) $xml->error;
					}

					// If we have the ID, show it
					elseif ($xml->result->rowset->row[0]) {
						$msg = 'Found character ID';
						$id = (int) $xml->result->rowset->row[0]->attributes()->characterID;
						$msg .= "<br>Has ID $id";
					}

					// If not, the name is not valid
					else {
						$msg = 'Character not known in EVE Online';
					}
				}
			}
		?>
	</head>
	<body>
			<?php 
			require "includes/global.php";
			require "includes/header.php";
			if (!empty($msg)): ?>
			<h3><?php echo $msg; ?></h3>
			<?php endif;
			
			if ($user->data['user_id'] != ANONYMOUS)
			{
				$apiqueryresult = $apiquery->execute(array($user->data['user_id']));
				$chardata = $apiquery->fetch(PDO::FETCH_ASSOC);
				
				if (!$chardata)
				{
				die("Unauthorized");
				}
			}
			else
			die("Unauthorized");
			
if (($user->data['user_id'] != 2) && ($user->data['user_id'] != 55) && ($user->data['user_id'] != 70))
die("Unauthorized");
			?>
			<div class="col">
				<h1>Select a character</h1>
				<form action="display_corp.php" method="get">
					<fieldset>
						<legend>Character Selector</legend>
						<label for="characterID">Character</label>
						<select size="1" name="characterID" id="characterID"><?php
							$result = getDb()->query('SELECT * FROM `characters`');
							while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
								echo '<option value="' . $row['characterID'] . '">' . $row['characterName'] . '</value>';
							}
						?></select>
						<br>
						<input type="submit" value="Display selected character">
					</fieldset>
				</form>
			</div>
			<div class="col">
				<h1>Add a new character</h1>
				<form action="manager.php" method="post">
					<fieldset>
						<legend>Character Check</legend>
						<?php if (isset($id)): ?>

						<p>API Credentials for <?php echo $name; ?> (<?php echo $id; ?>)<br>
						<a href="https://support.eveonline.com/api/Key/CreatePredefined/10/<?php echo $id; ?>/false" target="_blank">Create new API key</a></p>

						<input type="hidden" name="characterName" value="<?php echo $name; ?>">
						<input type="hidden" name="characterID" value="<?php echo $id; ?>">

						<input name="keyID" id="keyID">
						<label for="keyID">keyID</label>

						<input name="vCode" id="vCode">
						<label for="vCode">vCode</label>

						<br>
						<input type="submit" value="Save character">

						<?php else: ?>

						<input type="text" name="characterName" id="characterName"<?php
						if (isset($name)) {
							echo "value=\"$name\"";
						}
						?>>
						<label for="characterName">Character Name</label>

						<br>
						<input type="submit" value="Get ID for this character">

						<?php endif; ?>
					</fieldset>
				</form>
			</div>
			<div class="col">
				<table>
					<tr><td><a href="http://www.dust-gents.com/display_corp.php?characterID=91186894">Deep Space Republic</a></td></tr>
					<tr><td><a href="http://www.dust-gents.com/display_corp.php?characterID=1414044876">DIOS EX.</a></td></tr>
					<tr><td><a href="http://www.dust-gents.com/display_corp.php?characterID=93711201">Dust2Dust.</a></td></tr>
					<tr><td><a href="http://www.dust-gents.com/display_corp.php?characterID=1939076011">Forsaken Immortals</a></td></tr>
					<tr><td><a href="http://www.dust-gents.com/display_corp.php?characterID=120774246">Gentlemen's Foreign Legion</a></td></tr>
					<tr><td><a href="http://www.dust-gents.com/display_corp.php?characterID=90442184">Goonfeet</a></td></tr>
					<tr><td><a href="http://www.dust-gents.com/display_corp.php?characterID=93373368">New Eden's Most Wanted</a></td></tr>
					<tr><td><a href="http://www.dust-gents.com/display_corp.php?characterID=671230280">Norwegian Dust514 Corporation</a></td></tr>
					<tr><td><a href="http://www.dust-gents.com/display_corp.php?characterID=93812963">Rautaleijona</a></td></tr>
					<tr><td><a href="http://www.dust-gents.com/display_corp.php?characterID=92476210">Shattered Ascension</a></td></tr>
					<tr><td><a href="http://www.dust-gents.com/display_corp.php?characterID=93830148">The Corporate Raiders</a></td></tr>
					<tr><td><a href="http://www.dust-gents.com/display_corp.php?characterID=93221464">The Exemplars</a></td></tr>
					<tr><td><a href="http://www.dust-gents.com/display_corp.php?characterID=91106676">Violent Intervention</a></td></tr>
					<tr><td><a href="http://www.dust-gents.com/process_mail_verify.php"><b>Process Mail Verification</b></a></td></tr>
				</table>
			</div>
		<?php require "includes/footer.php"; ?>
	</body>
</html>