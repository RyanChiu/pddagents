<?php
/**
 * the script will insert incremental data into stats from fwends_push.tracking
 */

include 'zmysqlConn.class.php';
include 'extrakits.inc.php';

if (($argc - 1) != 1) {//if there is 1 parameter and it must mean a date like '2010-04-01,12:34:56'
	exit("Only 1 parameter needed like '2010-05-01,12:34:56'.\n");
}

/*
 * the following line will make the whole script exit if date string format is wrong
 */
$date = __get_remote_date($argv[1], "GMT", -0.005, "GMT", false);
$date_l = __get_remote_date($argv[1], "GMT", -0.005, "GMT", true);

/*get the abbreviation of the site*/
$abbr = __stats_get_abbr($argv[0]);

/*find out the typeids and siteid from db by "xxxc" which is the abbreviation of the site*/
$typeids = array();
$siteid = null;
$zconn = new zmysqlConn;
__stats_get_types_site($typeids, $siteid, $abbr, $zconn->dblink);
if (empty($siteid)) {
	exit(sprintf("The site with abbreviation \"%s\" does not exist.\n", $abbr));
}
if (count($typeids) != 1) {
	exit(sprintf("The site with abbreviation \"%s\" should have 1 type at least.\n", $abbr));
}
/*get all the campaign mappings of the site*/
$sql = sprintf("select * from view_mappings where siteid = %d", $siteid);
$rs = mysql_query($sql, $zconn->dblink)
	or die ("Something wrong with: " . mysql_error());
$agents = array();
while ($row = mysql_fetch_assoc($rs)) {
	$agents += array($row['campaignid'] => $row['agentid']);
}
if (empty($agents)) {
	exit(sprintf("The site with abbreviation \"%s\" does not have any campaign ids asigned for agents.\n", $abbr));
}

/*
 * prepare the tracking data pushed by fwends.com
 */
mysql_select_db("fwends_push", $zconn->dblink)
	or die ("Something wrong with: " . mysql_error());
$sql = sprintf("select * from tracking where statDate = '%s'", $date);
$sql_fordebug = $sql; // for debug
$rs_push = mysql_query($sql, $zconn->dblink)
	or die ("Something wrong with: " . mysql_error() . "\n");

mysql_select_db("pddcake", $zconn->dblink)
	or die ("Something wrong with: " . mysql_error());
$i = $j = $f = 0;
while ($r_push = mysql_fetch_assoc($rs_push)) {
	//echo $r_push['id'] . ', ' . $r_push['statDate'] . ', ' . $r_push['trackingCode']; // for debug
	//continue; // for debug
	
	$campaignid = $r_push['trackingCode'];
	//echo $campaignid . "\n"; continue; //for debug
	
	if (in_array($campaignid, array_keys($agents))) {
		//echo $campaignid . "," . $agents[$campaignid] . ";\n"; continue;//for debug
		/*
		 * we need to insert the balance data with date and time anyway
		 */
		$conditions = sprintf('convert(trxtime, date) = "%s" and siteid = %d'
			. ' and typeid = %d and agentid = %d and campaignid = "%s"',
			$date, $siteid, $typeids[0], $agents[$campaignid], $campaignid
		);
		$sql = sprintf(
			'select sum(raws) as raws, sum(uniques) as uniques, sum(chargebacks) as chargebacks, sum(signups) as signups, sum(frauds) as frauds, sum(sales_number) as sales_number'
			. ' from stats where '. $conditions . ' group by agentid, campaignid, siteid, typeid, convert(trxtime, date)'
		);
		//echo $sql . "\n"; continue;// for debug
		$result = mysql_query($sql, $zconn->dblink)
			or die ("Something wrong with: " . mysql_error());
		if (mysql_num_rows($result) != 0) {
			$row = mysql_fetch_assoc($result);
			$sql = sprintf(
				'insert into stats'
				. ' (agentid, campaignid, siteid, typeid, raws, uniques, chargebacks, signups, frauds, sales_number, trxtime)'
				. ' values (%d, "%s", %d, %d, %d, %d, %d, %d, %d, %d, "%s")',
				$agents[$campaignid], $campaignid, $siteid, $typeids[0],
				//$item->uniques - $row['uniques'], $item->refunds - $row['chargebacks'], $item->frees - $row['signups'], null, $item->signups - $row['sales_number'],
				$r_push['clicks'] - $row['raws'], $r_push['uniqueClicks'] - $row['uniques'], $r_push['refunds'] + $r_push['chargeBacks'] + $r_push['cancels'] - $row['chargebacks'], $r_push['signups'] - $row['signups'], null, $r_push['sales'] - $row['sales_number'],
				$date_l
			);
		} else {
			$sql = sprintf(
				'insert into stats'
				. ' (agentid, campaignid, siteid, typeid, raws, uniques, chargebacks, signups, frauds, sales_number, trxtime)'
				. ' values (%d, "%s", %d, %d, %d, %d, %d, %d, %d, %d, "%s")',
				$agents[$campaignid], $campaignid, $siteid, $typeids[0],
				//$item->uniques, $item->refunds, $item->frees, null, $item->signups,
				$r_push['clicks'], $r_push['uniqueClicks'], $r_push['refunds'] + $r_push['chargeBacks'] + $r_push['cancels'], $r_push['signups'], null, $r_push['sales'],
				$date_l
			);
		}
		
		
		//echo $sql . "\n"; continue;//for debug
		mysql_query($sql, $zconn->dblink)
			or die ("Something wrong with: " . mysql_error());
		$j += mysql_affected_rows();
		$i++;
	}
}
if ($i == 0) {
	echo "No stats data exist by now.\n";
	echo $sql_fordebug . "(>>for debug<<)\n"; //for debug
}
echo $j . "(/" . $i . ") row(s) inserted.\n";
echo "Just got the stats data from the remote server at '" . $date_l . "' on the remote server.\n";
?>
