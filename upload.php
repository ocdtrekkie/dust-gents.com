<?php $page_title = "File Upload Tool";
require "includes/functions.php";
require "includes/global.php";
require "includes/header.php";

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
else
die("Unauthorized");

echo "<div class=\"col\"><p><b>This upload bin is for DUST 514/Gentlemen's Agreement-related uploads ONLY.</b> You are being logged. Abusers will be flayed.</p>

<br><form action=\"filestore.php\" method=\"post\"
enctype=\"multipart/form-data\">
<label for=\"file\">Filename:</label>
<input type=\"file\" name=\"file\" id=\"file\" /> 
<br />
<input type=\"submit\" name=\"submit\" value=\"Submit\" />
</form>
</div>";

require "includes/footer.php"; ?>