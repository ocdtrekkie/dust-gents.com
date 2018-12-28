<?php $page_title = "Corporation API Refresh";
			// Initialize stuff
			require "includes/global.php";
			require "includes/header.php";
			require_once 'includes/functions.php';
			include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
			$msg = '';
			$new_active_count = 0;
			
			$apiquery = getDb()->prepare('SELECT * FROM `members` WHERE `user_id` = ?');
			
			if ($user->data['user_id'] != ANONYMOUS)
			{
				$apiqueryresult = $apiquery->execute(array($user->data['user_id']));
				$chardata = $apiquery->fetch(PDO::FETCH_ASSOC);
				
				if (!$chardata)
				{
				die("Unauthorized");
				}
			}
			elseif ($_GET['key'] != "KEY_REMOVED")
			{
			die("Unauthorized");
			}

			// If a characterID is passed along as a parameter, try to get the data for it
			if (!empty($_GET['characterID'])) {

				// If we have an ID, get the data from database
				$query = getDb()->prepare('SELECT * FROM `characters` WHERE `characterID` = ?');
				$query->execute(array($_GET['characterID']));
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
							'extended' => 1
						);

						// Create the Pheal object
						$request = new Pheal($char['keyID'], $char['vCode'], 'corp');

						// Do the access check magic
						$request->detectAccess();

						// Call the CharacterSheet function
						$corpSheet = $request->CorporationSheet($arguments);
						$memberSheet = $request->MemberTracking($arguments);
						//$shareSheet = $request->Shareholders($arguments);

						// Get the result as a PHP array
						$data = $corpSheet->toArray();
						$memberData = $memberSheet->toArray();
						//$shareData = $shareSheet->toArray();

						// Cleanup the result
						if (isset($data['result'])) {
							$data = $data['result'];
						}
						if (isset($memberData['result'])) {
							$memberData = $memberData['result'];
						}
						//if (isset($shareData['result'])) {
						//	$shareData = $shareData['result'];
						//}
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

		?>
			<div style="float: left; padding: 20px;">
			
				<tbody>
				<?php
				
				if ($_GET['update'] == 1 && (($user->data['user_id'] == 2) || ($_GET['key'] == "KEY_REMOVED")))
				{
					$corpid = $data['corporationID'];
					$corpname = $data['corporationName'];
					$db = getDB();
					$check = $db->prepare('SELECT COUNT(*) FROM `members` WHERE `characterID` = :memid');
					$insert = $db->prepare('INSERT INTO `members` (`characterID`, `characterName`, `corporationID`, `roles`, `lastlog`) VALUES (?, ?, ?, ?, ?)');
					$movecheck = $db->prepare('SELECT * FROM `members` WHERE `characterID` = ?');
					$update = $db->prepare('UPDATE `members` SET `corporationID` = ?, `roles` = ?, `lastlog` = ? WHERE `characterID` = ?');
					$verify = $db->prepare('SELECT * FROM `members` WHERE `corporationID` = :corpid');
					$delete = $db->prepare('DELETE FROM `members` WHERE `characterID` = :dropid');
					$logit = $db->prepare('INSERT INTO `log` (`event`, `date`) VALUES (?, NOW())');
					$checkcorp = $db->prepare('SELECT COUNT(*) FROM `corporations` WHERE `corporationID` = :corpid');
					$insertcorp = $db->prepare('INSERT INTO `corporations` (`corporationID`, `corporationName`, `ticker`, `ceoID`, `member_count`, `lastupdated`, `description`, `taxRate`) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)');
					$updatecorp = $db->prepare('UPDATE `corporations` SET `ceoID` = ?, `member_count` = ?, `lastupdated` = NOW(), `description` = ?, `taxRate` = ? WHERE `corporationID` = ?');
					$updateactive = $db->prepare('UPDATE `corporations` SET `active_count` = ? WHERE `corporationID` = ?');
					$getcorpgroup = $db->prepare('SELECT `phpbb_id` FROM `corporations` WHERE `corporationID` = :corpid');
					
					$checkcorpresult = $checkcorp->execute(array(':corpid' => $corpid));
					$checkcorpcount = $checkcorp->fetchColumn();
					if ($checkcorpcount == 0)
					{
						echo "<b><font color='green'>New corp detected</font></b>. Adding to database...";
						$insertcorpresult = $insertcorp->execute(array($data['corporationID'], $data['corporationName'], $data['ticker'], $data['ceoID'], $data['memberCount'], $data['description'], $data['taxRate']));
						if ($insertcorpresult == 1) echo " Done!<br>"; else echo " Failed!<br>";
					}
					else
					{
						$updatecorpresult = $updatecorp->execute(array($data['ceoID'], $data['memberCount'], $data['description'], $data['taxRate'], $data['corporationID']));
					}
				}
				
					// TEST BLOCK
					if ($_GET['update'] == 2 && $user->data['user_id'] == 2)
					{
					print_r($shareData);
					}
					// END TEST BLOCK
				
				foreach ($memberData['members'] as $member):
					$memid = $member['characterID'];
					$memname = $member['name'];
					$memroles = $member['roles'];
					$memlastlog = $member['logoffDateTime'];
					
					$timesince = time() - strtotime($memlastlog);
					if (($timesince / (60 * 60 * 24)) < 14)
						$new_active_count = $new_active_count + 1;

					if (substr($memid, 0, 2) == '21' && strlen($memid) == '10')
						$dustie = true;
					else
						$dustie = false;
					if ($memroles == 9223372036854775807)
						$isdir = true;
					else
						$isdir = false;
						
					if ($dustie == false)	
						echo "<font color=\"aqua\">";
					if ($isdir == true)
						echo "<b>";
					
					echo "$memname ";
					
					if ($_GET['update'] == 1 && (($user->data['user_id'] == 2) || ($_GET['key'] == "KEY_REMOVED")))
					{
						echo "- $memid ";
					
						$checkresult = $check->execute(array(':memid' => $memid));
						
						$count = $check->fetchColumn();
						
						if ($count == 0)
						{
							echo " <b><font color='green'>is new</font></b>. Adding to database...";
							$insertresult = $insert->execute(array(
								$memid,
								$memname,
								$corpid,
								$memroles,
								$memlastlog
							));
							$logitresult = $logit->execute(array($memname . " has joined " . $corpname));
							if ($insertresult == 1) echo " Done!"; else echo " Failed!";
						}
						else
						{
							echo " already exists. Updating database...";
							$movecheckresult = $movecheck->execute(array($memid));
							$chkcorp = $movecheck->fetch(PDO::FETCH_ASSOC);
							if ($chkcorp['corporationID'] != $corpid)
							{
								echo " <font color='blue'>Corp Move Detected!</font>";
								$logitresult = $logit->execute(array($memname . " has moved corps to " . $corpname));
							}
							$updateresult = $update->execute(array(
								$corpid,
								$memroles,
								$memlastlog,
								$memid
							));
							if ($updateresult == 1) echo " Done!"; else echo " Failed!";
						}
					}
					
					if ($isdir == true)
						echo " (Director Roles)</b>";
					if ($dustie == false)	
						echo "</font>";
					
					echo "<br>";
					
				 
				endforeach;
				
				if ($_GET['update'] == 1 && (($user->data['user_id'] == 2) || ($_GET['key'] == "KEY_REMOVED")))
				{
					echo "<p>";
					
					$verifyresult = $verify->execute(array(':corpid' => $corpid));
					$dbresults = $verify->fetchAll();
					$processed = process_db_array($memberData['members'], 'characterID');
					
					foreach ($dbresults as $dbresult):
						$chkid = $dbresult['characterID'];
						$chkname = $dbresult['characterName'];
						$chkforum = $dbresult['user_id'];
						
						if (array_key_exists($chkid, $processed)) {
							echo "$chkname is still a member.";
						} else {
							$deleteresult = $delete->execute(array(':dropid' => $chkid));
							$logitresult = $logit->execute(array($chkname . " has left " . $corpname . $logforum));
							if ($chkforum != 0)
							{
								$logforum = ". Forum user ID: " . $chkforum;
								// Remove
								$getcorpgroupresult = $getcorpgroup->execute(array(':corpid' => $corpid));
								$corpgroup = $getcorpgroup->fetchColumn();
								group_user_del($corpgroup, array($chkforum));
							}
							else
							{
								$logforum = ".";
							}
							echo "$chkname <b><font color='red'>is no longer a member of $corpname</font></b>$logforum Deleting...";
							if ($deleteresult == 1) echo " Done!"; else echo " Failed!";
						}
						
						echo "<br>";
					
					endforeach;
					
					$updateactiveresult = $updateactive->execute(array($new_active_count, $data['corporationID']));
				}
				?>
				</tbody>
			
			</div>
			<div class="col">
				<!-- Corp -->
				<strong><font size="5"><?php echo $data['corporationName']; ?></font></strong>
				<p><img src="http://image.eveonline.com/Corporation/<?php echo $data['corporationID']; ?>_128.png" width="128" height="128" alt="<?php echo $data['corporationName']; ?>"></p>
				<!-- Alliance -->
				<?php if (!empty($data['allianceName'])): ?>
				<strong><font size="5"><?php echo $data['allianceName']; ?></font></strong>
				<p><img src="http://image.eveonline.com/Alliance/<?php echo $data['allianceID']; ?>_128.png" width="128" height="128" alt="<?php echo $data['allianceName']; ?>"></p>
				<?php endif; ?>
				<!-- Stats -->
				<h2>Stats</h2>
				<table>
					<thead>
						<tr>
							<td><strong>Property</strong></td>
							<td><strong>Value</strong></td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Member Count</td>
							<td><?php echo $data['memberCount']; ?></td>
						</tr>
						<tr>
							<td>Active Count</td>
							<td><?php echo $new_active_count; ?></td>
						</tr>
						<tr>
							<td>Member Limit</td>
							<td><?php echo $data['memberLimit']; ?></td>
						</tr>
						<tr>
							<td>Tax Rate</td>
							<td><?php echo $data['taxRate']; ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php endif; ?>
		<?php require "includes/footer.php"; ?>
