<div style="margin-bottom:10px;border-top:0;border-left:0;border:right:0;border-bottom:solid blue 1px;width:100%;color:blue;">
<?php echo $s; ?>
</div>

<?php
$logpath = APP . "tmp" . DS . "canals.log";
$from = "from canal $n: ";
$stamp =  " [" . $ip . "/" . $now . "GMT]\n";
if (empty($_POST)) {
	error_log(
		$from . "Nothing posted here" . $stamp,
		3, 
		$logpath
	);
} else {
	error_log(
		$from . "\n" . print_r($_POST, true) . $stamp,
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