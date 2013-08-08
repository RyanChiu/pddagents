<?php
App::import('vendor', 'ExtraKits', array('file' => 'extrakits.inc.php'));
App::import('vendor', 'ZMysqlConn', array('file' => 'zmysqlConn.class.php'));
?>
<?php
class CanalController extends AppController {
	var $name = 'Canal';
	var $uses = array();
	
	/**
	 * overrides
	 */
	function beforeFilter() {
		
		parent::beforeFilter();
	}
	
	/**
	 * views
	 */
	function index() {
		$this->layout = "emptylayout";
		
		$n = -1;
		$ip = __getclientip();
		if (isset($_POST['ch'])) {
			$n = $_POST['ch'];
		}
		$now = new DateTime("now", new DateTimeZone("GMT"));
		$err = "";
		/*log it all*/
		switch ($n) {
			case -1:
			case 0:
			case 1:
				$s = "from $n, accepted";
				break;
			default:
				$s = "illegal post";
				break;
		}
		/*actually save the data into stats*/
		switch ($n) {
			case 0://force ch = 0 to site cams2
				if ($ip == "66.180.199.11" || $ip == "127.0.0.1") {
					$conn = new zmysqlConn();
					$sql = "select a.*, b.id as 'typeid' from view_mappings a, types b where a.username = '" . $_POST['agent'] . "' and a.siteid = b.siteid and a.abbr = 'cams2'";
					$rs = mysql_query($sql, $conn->dblink);
					while ($r = mysql_fetch_assoc($rs)) {
						$agid = $r['agentid'];
						$typeid = $r['typeid'];
						$siteid = $r['siteid'];
						$campid = $r['campaignid'];
						$sales = 1;
						$trxtime = $now->format("Y-m-d H:i:s");
						
						$sql = "insert into stats (agentid, raws, uniques, chargebacks, signups, frauds, sales_number, typeid, siteid, campaignid, trxtime)"
							. " values ($agid, 0, 0, 0, 0, 0, $sales, $typeid, $siteid, '$campid', '$trxtime')";
						//$err = $sql; continue; //for debug;
						
						if (mysql_query($sql, $conn->dblink) === false) {
							$err = mysql_error();
						}
					}
				}
				break;
		}
		$this->set(compact("s"));
		$this->set(compact("n"));
		$this->set(compact("ip"));
		$this->set("now", $now->format("Y-m-d H:i:s"));
		$this->set(compact("err"));
	}
}
	