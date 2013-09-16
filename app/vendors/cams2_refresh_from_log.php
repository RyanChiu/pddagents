<?php 
/**
 * for example: php cams2_refresh_from_log.php in.log 2013-09-09
 */

include 'zmysqlConn.class.php';
include 'extrakits.inc.php';

const ONLYIP = "66.180.199.11";
if ($argc != 3) 
	exit("Please give a file name and a date, like 'in.log 2013-09-09'...\n");
$fstr = file_get_contents($argv[1]);
$day = $argv[2];
$search = ONLYIP . "/" . $day;

$count = substr_count($fstr, $search);

$conn = new zmysqlConn();
/*get the abbreviation of the site*/
$abbr = __stats_get_abbr($argv[0]);
__stats_get_types_site($typeids, $siteid, $abbr, $conn->dblink);

$i = 0;
$start = 0;
$posts = array();
while ($i < $count) {
	$pos = strpos($fstr, $search, $start);
	
	$p1 = strripos($fstr, '[type]', $pos - strlen($fstr));
	$p2 = stripos($fstr, "\n", $p1);
	$p3 = stripos($fstr, "=>", $p1);
	$type = substr($fstr, $p3 + strlen("=>"), $p2 - $p3);
	$type = trim($type);
	$type = strtolower($type);
	
	$p1 = strripos($fstr, '[agent]', $pos - strlen($fstr));
	$p2 = stripos($fstr, "\n", $p1);
	$p3 = stripos($fstr, "=>", $p1);
	$agent = substr($fstr, $p3 + strlen("=>"), $p2 - $p3);
	$agent = trim($agent);
	
	$p1 = strripos($fstr, '[ch]', $pos - strlen($fstr));
	$p2 = stripos($fstr, "\n", $p1);
	$p3 = stripos($fstr, "=>", $p1);
	$ch = substr($fstr, $p3 + strlen("=>"), $p2 - $p3);
	$ch = trim($ch);
	
	$p1 = strripos($fstr, '[unique]', $pos - strlen($fstr));
	$p2 = stripos($fstr, "\n", $p1);
	$p3 = stripos($fstr, "=>", $p1);
	$unique = substr($fstr, $p3 + strlen("=>"), $p2 - $p3);
	$unique = trim($unique);
	$unique = strtolower($unique);
	$unique = substr($unique, 0, 1);
	
	$trxtime = substr($fstr, $pos + strlen(ONLYIP) + 1, strlen("0000-00-00 00:00:00"));
	if ($type == 'sale') {
		$p1 = strripos($fstr, '[stamp]', $pos - strlen($fstr));
		$p2 = stripos($fstr, "\n", $p1);
		$p3 = stripos($fstr, "=>", $p1);
		$stamp = substr($fstr, $p3 + strlen("=>"), $p2 - $p3);
		$stamp = trim($stamp);
		if (!empty($stamp)) {
			$ts = DateTime::createFromFormat("Ymd_His00", $stamp);
			if ($ts !== false) {
				$trxtime = $ts->format("Y-m-d H:i:s");
			} else {
				$trxtime = $day ." 00:00:01";
			}
		} else {
			$trxtime = $day ." 00:00:02";
		}
	}

	$posts[$i] = array(
		'trxtime' => $trxtime,
		'type' => $type,
		'agent' => $agent,
		'ch' => $ch,
		'unique' => $unique
	);
	
	$start = $pos + strlen($search);
	$i++;
}

/*
$in = "";
for ($i = 0; $i < count($posts); $i++) {
	$in .= '"' . $posts[$i]['agent'] . '"';
	if ($i != count($posts)  - 1) $in .= ", ";
}
echo "($in)" . "[$i/$count]\n";
exit();
*/

$k = 0;
$sql = "delete from stats where convert(trxtime, date) = '$day' and siteid = $siteid";
mysql_query($sql, $conn->dblink);
echo sprintf("%d(/$count) removed.\n", mysql_affected_rows());

for ($j = 0; $j < count($posts); $j++) {
	$type = $posts[$j]['type'];
	$agent = $posts[$j]['agent'];
	$ch = intval($posts[$j]['ch']);
	$unique = $posts[$j]['unique'];
	$trxtime = $posts[$j]['trxtime'];
	
	$sql = "select a.*, g.companyid, b.id as 'typeid'" .
		" from agent_site_mappings a, sites s, accounts n, types b, agents g, companies m" .
		" where a.siteid = s.id and a.siteid = b.siteid and s.abbr = '$abbr'" .
		" and a.agentid = g.id and g.companyid = m.id" .
		" and a.agentid = n.id and n.username = '$agent'" .
		" ORDER BY typeid";
	//echo "($sql)" . "\n";continue;//for debug
	$rs = mysql_query($sql, $conn->dblink);
	if (mysql_affected_rows() == 0) echo "\n-$agent-";
	$i = 0;
	while ($r = mysql_fetch_assoc($rs)) {
		if ($i == $ch) {
			$typeid = $r['typeid'];
			$agid = $r['agentid'];
			$comid = $r['companyid'];
			$siteid = $r['siteid'];
			$campid = $r['campaignid'];
			$clicks = ($type == 'click' ? 1 : 0);
			$uniques = ($unique == 'y' ? 1 : 0);
			$sales = ($type == 'sale' ? 1 : 0);
	
			$sql = "insert into stats (agentid, companyid, raws, uniques, chargebacks, signups, frauds, sales_number, typeid, siteid, campaignid, trxtime)"
				. " values ($agid, $comid, $clicks, $uniques, 0, 0, 0, $sales, $typeid, $siteid, '$campid', '$trxtime')";
			//echo "($j)" . $sql . "\n"; break; //for debug;
	
			if (mysql_query($sql, $conn->dblink) === false) {
				$err = mysql_error();
				echo "\n-";
			} else {
				$k++;
				echo "|";
			}
		}
		$i++;
	}
}
echo "($k/$j/$count)\n";
?>