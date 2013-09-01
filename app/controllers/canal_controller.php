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
		
		$ip = __getclientip();
		$tz = "EST";
		$now = new DateTime("now", new DateTimeZone($tz));
		
		/*
		 * just log every POST/GET at the very beginning
		 */
		$logpath = APP . "tmp" . DS . "canals.log";
		$from = "from ip: $ip";
		$ending =  " [" . $ip . "/" . $now->format("Y-m-d H:i:s") . "($tz)]\n";
		error_log("######\n", 3, $logpath);
		if (empty($_POST) && empty($_GET)) {
			error_log(
				$from . "Nothing posted here" . $ending,
				3,
				$logpath
			);
		} else {
			error_log(
				$from . "\n"
					. "GET:\n" . print_r($_GET, true) . "\n"
					. "POST:\n" . print_r($_POST, true) . $ending,
				3,
				$logpath
			);
		}
		
		$err = "";
		$s = "";
		/*actually save the data into stats*/
		if (true || $ip == "66.180.199.11" || $ip == "127.0.0.1") {
			$s = "from ip: $ip, accepted";
			
			$stamp = (isset($_GET['stamp']) ? trim($_GET['stamp']) : (isset($_POST['stamp']) ? trim($_POST['stamp']) : ''));
			$stamp = strtolower($stamp);
			$type = (isset($_GET['type']) ? trim($_GET['type']) : (isset($_POST['type']) ? trim($_POST['type']) : 'ill')); 
			$type = strtolower($type);
			$agent = (isset($_GET['agent']) ? trim($_GET['agent']) : (isset($_POST['agent']) ? trim($_POST['agent']) : '')); 
			$unique = (isset($_GET['unique']) ? trim($_GET['unique']) : (isset($_POST['unique']) ? trim($_POST['unique']) : ''));
			$unique = strtolower($unique);
			$ch = (isset($_GET['ch']) ? trim($_GET['ch']) : (isset($_POST['ch']) ? trim($_POST['ch']) : ''));
			$ch = intval($ch);
			$conn = new zmysqlConn();
			$sql = "select a.*, b.id as 'typeid' from view_mappings a, types b where a.username = '$agent' and a.siteid = b.siteid and a.abbr = 'cams2' ORDER BY typeid";
			$rs = mysql_query($sql, $conn->dblink);
			$i = 0;
			while ($r = mysql_fetch_assoc($rs)) {
				if ($i == $ch) {
					$typeid = $r['typeid'];
					$agid = $r['agentid'];
					$siteid = $r['siteid'];
					$campid = $r['campaignid'];
					$clicks = ($type == 'click' ? 1 : 0);
					$uniques = ($unique == 'y' ? 1 : 0);
					$sales = ($type == 'sale' ? 1 : 0);
					$trxtime = $now->format("Y-m-d H:i:s");
					if ($type == 'sale') {
						if (!empty($stamp)) {
							$ts = DateTime::createFromFormat("Ymd_His00", $stamp);
							if ($ts !== false) {
								$trxtime = $ts->format("Y-m-d H:i:s");
							} else {
								$trxtime = $now->format("Y-m-d 00:00:01");
							}
						} else {
							$trxtime = $now->format("Y-m-d 00:00:02");
						}
					}
					
					$sql = "insert into stats (agentid, raws, uniques, chargebacks, signups, frauds, sales_number, typeid, siteid, campaignid, trxtime)"
						. " values ($agid, $clicks, $uniques, 0, 0, 0, $sales, $typeid, $siteid, '$campid', '$trxtime')";
					//$err = $sql; $s .= $err; continue; //for debug;
					
					if (mysql_query($sql, $conn->dblink) === false) {
						$err = mysql_error();
					}
				}
				$i++;
			}
		} else {
			$s = "illegal visit";
		}
		
		/*
		 * log sql err if needed
		 */
		if (!empty($err)) {
			$now = $now->format("Y-m-d H:i:s");
			$time = str_replace(" ", "", $now);
			$time = str_replace("-", "", $time);
			$time = str_replace(":", "", $time);
			error_log(
				$from . "\n" . $err . $ending,
				3,
				APP . "tmp" . DS . "err_" . $time . ".log"
			);
		}
				
		$this->set(compact("s"));
	}
}
	