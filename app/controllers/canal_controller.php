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
		if (isset($_GET['ch'])) {
			$n = $_GET['ch'];
		} else if (isset($_POST['ch'])) {
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
				$s = "illegal visit";
				break;
		}
		/*actually save the data into stats*/
		if (true || $ip == "66.180.199.11" || $ip == "127.0.0.1") {
			$type = (isset($_GET['type']) ? trim($_GET['type']) : (isset($_POST['type']) ? trim($_POST['type']) : 'ill')); 
			$type = strtolower($type);
			$agent = (isset($_GET['agent']) ? trim($_GET['agent']) : (isset($_POST['agent']) ? trim($_POST['agent']) : '')); 
			$unique = (isset($_GET['unique']) ? trim($_GET['unique']) : (isset($_POST['unique']) ? trim($_POST['unique']) : ''));
			$unique = strtolower($unique);
			$conn = new zmysqlConn();
			$sql = "select a.*, b.id as 'typeid' from view_mappings a, types b where a.username = '$agent' and a.siteid = b.siteid and a.abbr = 'cams2'";
			$rs = mysql_query($sql, $conn->dblink);
			while ($r = mysql_fetch_assoc($rs)) {
				$agid = $r['agentid'];
				$typeid = $r['typeid'];
				$siteid = $r['siteid'];
				$campid = $r['campaignid'];
				$clicks = ($type == 'click' ? 1 : 0);
				$uniques = ($unique == 'y' ? 1 : 0);
				$sales = ($type == 'sale' ? 1 : 0);
				$trxtime = $now->format("Y-m-d H:i:s");
				
				$sql = "insert into stats (agentid, raws, uniques, chargebacks, signups, frauds, sales_number, typeid, siteid, campaignid, trxtime)"
					. " values ($agid, $clicks, $uniques, 0, 0, 0, $sales, $typeid, $siteid, '$campid', '$trxtime')";
				//$err = $sql; continue; //for debug;
				
				if (mysql_query($sql, $conn->dblink) === false) {
					$err = mysql_error();
				}
			}
		}
				
		$this->set(compact("s"));
		$this->set(compact("n"));
		$this->set(compact("ip"));
		$this->set("now", $now->format("Y-m-d H:i:s"));
		$this->set(compact("err"));
	}
}
	