<?php
/**
 * it'll take only 1 param which switch from:
 * 1,remotely register all the agnets no mater what on "all"
 * or
 * 2,remotely register only those agents who's not mapped into agent_site_mappings on "needed"
 */

include 'zmysqlConn.class.php';
include 'extrakits.inc.php';

/*get the abbreviation of the site*/
$abbr = __stats_get_abbr($argv[0]);

// create a new cURL resource
$ch = curl_init();

if ($argc != 2) {
	exit("should give 'all' or 'needed' as param.\n");
}
$sql = "";
if ($argv[1] == 'all') {
	$sql = "select distinct username from view_agents";
} else if ($argv[1] == 'needed') {
	$sql = "select distinct username from view_agents where username not in (select distinct username from view_mappings where abbr = '$abbr')";
} else {
	exit("should give 'all' or 'needed' as param.\n");
}
$conn = new zmysqlConn;
$rs = mysql_query($sql, $conn->dblink)
	or die ("Something wrong with: " . mysql_error());

while ($r = mysql_fetch_assoc($rs)) {
	$agname = $r['username'];
	// set URL and other appropriate options
	curl_setopt($ch, CURLOPT_URL, "http://bigbucksrevenue.com/_scripts/update-chan/addchan.php?a=webmasters@paydirtdollars.com&channel=$agname&code=$agname");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	// grab URL and pass it to the browser
	if (!curl_exec($ch)) {
		echo ("error (number" . curl_errno($ch) . ") occured when adding $agname\n");
	} else {
		echo "[$agname]\n";
	}
	sleep(2);
}

// close cURL resource, and free up system resources
curl_close($ch);
?>