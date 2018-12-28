<?php $page_title = "CREST District Info";
	// Initialize stuff
	require "includes/global.php";
	require "includes/header.php";
	require_once 'includes/functions.php';
	include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
	$msg = '';
	
$insert = getDB()->prepare('INSERT INTO `alliance_ref` (`corporation_id`, `alliance_id`, `alliance_name`) VALUES (?, ?, ?)');
$cleartab = getDB()->prepare('TRUNCATE TABLE `alliance_ref`');
	
$urlChar = "https://api.eveonline.com/eve/AllianceList.xml.aspx";	
$data = file_get_contents($urlChar);
$list = new SimpleXMLElement($data);

if ($data != NULL)
	{
	
	$clearresult = $cleartab->execute();

foreach ($list->result->rowset->row as $row):
	echo $row['name'] . " " . $row['allianceID'];
	
	foreach ($row->rowset->row as $corp):
		echo "<br>" . $corp['corporationID'];
		$insertresult = $insert->execute(array($corp['corporationID'], $row['allianceID'], $row['name']));
	endforeach;
	echo "<p>";
endforeach;

}

	require "includes/footer.php";
?>