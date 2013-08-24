<?php echo $s; ?>

<?php
$logpath = APP . "tmp" . DS . "canals.log";
$from = "from ip: $ip";
$stamp =  " [" . $ip . "/" . $now . "($tz)]\n";
if (empty($_POST) && empty($_GET)) {
	error_log(
		$from . "Nothing posted here" . $stamp,
		3, 
		$logpath
	);
} else {
	error_log(
		$from . "\n" 
			. "GET:\n" . print_r($_GET, true) . "\n"
			. "POST:\n" . print_r($_POST, true) . $stamp,
		3,
		$logpath 
	);
}

if (!empty($err)) {
	$time = str_replace(" ", "", $now);
	$time = str_replace("-", "", $time);
	$time = str_replace(":", "", $time);
	error_log(
		$from . "\n" . $err . $stamp,
		3,
		APP . "tmp" . DS . "err_" . $time . ".log"
	);
}
?>