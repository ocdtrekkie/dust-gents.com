<?php $page_title = "View Personal Stats";
require "includes/functions.php";
require "includes/charts.class.php";
require "includes/global.php";

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

$showitquery = GetDb()->prepare('SELECT * FROM `leaderboard` WHERE `characterID` = ? ORDER BY date DESC');
$showitqueryresult = $showitquery->execute(array($chardata['characterID']));
$lbdata = $showitquery->fetchAll();

$g = new chart;
$elemx = Array();
$elemy = Array();

foreach($lbdata as $lbitem):
	$wppd = $lbitem['wppd'];
	$kdr = round($lbitem['kdr'],2);
	$sdr = round($lbitem['ds_supd'],2);
	$wlr = $lbitem['wlr'];
	$kills = $lbitem['kills'];
	$deaths = $lbitem['deaths'];
	$warpoints = $lbitem['warpoints'];
	$lastupdated = $lbitem['date'];
	
	$elemx[0][] = $lastupdated;
	$elemx[1][] = "";
	$elemy[0][] = $kdr;
	$elemy[1][] = $sdr;
endforeach;

$xcount = 0;
foreach ($elemx as $v)
	$xcount = max($xcount, count($v));
	
$ymax = 0;
foreach ($elemy as $v)
	$ymax = max($ymax,ceil(max($v)));

foreach ($elemy as $k => $v)
	foreach ($v as $kk => $vv)
	{
    	$g->xValue[$k][] = $elemx[$k][$kk];
    	$g->DataValue[$k][] = $vv;
	}

$g->Title = "Stats Over Time";
$g->SubTitle = $chardata['characterName'];
$g->Width = 900;
$g->Height = 300;
$g->ShowBullets = TRUE;

$g->LineShowCaption = FALSE; // TO BE FIXED YET
$g->LineShowTotal = FALSE;   // DEPENDS ON LineShowCaption to be TRUE
$g->LineCaption[0] = "KDR";
$g->LineCaption[1] = "SDR";
$g->LineCount = 2;

$g->xCount = $xcount;
$g->xShowValue = TRUE;
$g->xShowGrid = TRUE;

$g->yCount = 10;
$g->yCaption = "";
$g->yShowValue = TRUE;
$g->yShowGrid = TRUE;

$g->DataDecimalPlaces = 0;
$g->DataMax = $ymax;
$g->DataMin = 0;
$g->DataShowValue = TRUE;

$g->MakeLinePointChart();
?>