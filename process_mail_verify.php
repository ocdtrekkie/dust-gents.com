<?php $page_title = "Automatic Activation Processor";
	// Initialize stuff
	require "includes/global.php";
	require "includes/header.php";
	require_once 'includes/functions.php';
	include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
	$msg = '';
	
	if (($user->data['user_id'] != 2) && ($_GET['key'] != "KEY_REMOVED"))
		die("Unauthorized!");
	
	$characterID = 93543845;
	
	if (!empty($characterID)) {

		// If we have an ID, get the data from database
		$query = getDb()->prepare('SELECT * FROM `characters` WHERE `characterID` = ?');
		$query->execute(array($characterID));
		$char = $query->fetch(PDO::FETCH_ASSOC);
		unset($query);

		// If we get a result from the database, we have a valid character
		if (!empty($char)) {

			try {
				
				// Load pheal
				loadPheal();

				// Prepare the arguments
				$arguments = array(
					'characterID' => $char['characterID'],
				);

				// Create the Pheal object
				$request = new Pheal($char['keyID'], $char['vCode'], 'char');

				// Do the access check magic
				$request->detectAccess();

				// Call the CharacterSheet function
				$mailMsg = $request->MailMessages($arguments);

				// Get the result as a PHP array
				$mailData = $mailMsg->toArray();

				// Cleanup the result
				if (isset($mailData['result'])) {
					$mailData = $mailData['result'];
				}

				$id = (int) $char['characterID'];
			}

			// If an error occurs during the request, put it on display
			catch (Exception $e) {
				$msg = "Error {$e->getCode()}: {$e->getMessage()}";
			}
		}
	}
	
	// If the $msg variable is not empty, there must have been an error
	if (!empty($msg)):

		echo "<h3>$msg</h3>";

	// If there is no ID, the characterID is missing or invalid
	elseif (empty($id)):

		echo '<h1>Error</h1><p>No character with this ID found</p>';

	else:
	
		$verify = GetDB()->prepare('SELECT * FROM `members` WHERE `characterID` = ?');
		$vercorp = GetDB()->prepare('SELECT * FROM `corporations` WHERE `corporationID` = ?');
		$idconnect = GetDB()->prepare('UPDATE `members` SET `user_id` = ? WHERE `characterID` = ?');
		$logit = GetDB()->prepare('INSERT INTO `log` (`event`, `date`) VALUES (?, NOW())');
	
		foreach ($mailData['messages'] as $mailItem):
		
			echo "<br>";
			if (($mailItem['toCharacterIDs'] == $characterID) && (($mailItem['title'] == 'Refresh') || ($mailItem['title'] == 'refresh') || ($mailItem['title'] == 'REFRESH')))
			{
				echo " <b>Refresh requested.</b> ";
				$verifyresult = $verify->execute(array($mailItem['senderID']));
				$chardata = $verify->fetch(PDO::FETCH_ASSOC);
				if (!$chardata)
					echo "<font color=\"red\">Character not in database.</font> ";
				else
				{
					echo "<font color=\"green\">Character record located (" . $chardata['characterName'] . ").</font> ";
					$vercorpresult = $vercorp->execute(array($chardata['corporationID']));
					$corpdata = $vercorp->fetch(PDO::FETCH_ASSOC);
					if (!$corpdata)
						echo "<font color=\"red\">Corporation not in database.</font> ";
					else
					{
						echo "<font color=\"green\">Corporation record located (" . $corpdata['corporationName'] . ").</font> ";
						if ((int)$corpdata['standings'] < 10)
							echo "<font color=\"red\">Corporation standings insufficient.</font> ";
						else
						{
							$useridarray = array();
							$usernamearray = array($chardata['characterName']);
							user_get_id_name($useridarray, $usernamearray, false);
							if (!$useridarray['0'])
								echo "<font color=\"red\">Forum account not found.</font> ";
							else
							{
								echo "<font color=\"green\">Forum account located (ID " . $useridarray['0'] . ").</font> ";
								if ($chardata['user_id'] != '0')
									echo "<font color=\"yellow\">Forum account already attached to roster.</font> ";
								else
								{
									$logitresult = $logit->execute(array($chardata['characterName'] . " has registered"));
									$idconnectresult = $idconnect->execute(array($useridarray['0'], $chardata['characterID']));
									echo "<font color=\"green\">Forum account attached to roster.</font> ";
								}
								$usergroupsarray = group_memberships(false, $useridarray);
								$usergroupsarray = process_db_array($usergroupsarray, 'group_id');
								if (array_key_exists($corpdata['phpbb_id'], $usergroupsarray))
									echo "<font color=\"yellow\">Forum account already a member of corporation group.</font> ";
								else
								{
									$logitresult = $logit->execute(array($chardata['characterName'] . " has been added to the group " . $corpdata['corporationName']));
									group_user_add($corpdata['phpbb_id'], $useridarray, false, false, true);
									echo "<font color=\"green\">Forum account joined to corporation group.</font> ";
								}
							}
						}
					}
				}
			}
			else
				echo "<b>Unrecognized request.</b> (" . $mailItem['title'] . ") ";
		
		endforeach;
	
	endif;
	require "includes/footer.php";
?>