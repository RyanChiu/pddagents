<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN">
<html>
<head>
<title><?php echo $title_for_layout; ?>
</title>
<?php

/*for default whole page layout*/
echo $html->css('main');

/*for jQuery*/
echo $javascript->link('jQuery/Datepicker/jquery-1.3.2.min');

/*for cufon*/
echo $javascript->link('cufon/cufon-yui');
echo $javascript->link('cufon/Chiller_400.font');

echo $scripts_for_layout;

?>
<script type="text/javascript">
	Cufon.replace(".header");
</script>
</head>
<body bgcolor="#ffffff">
	<div class="wrapper">
		<!-- Start Border-->
		<div id="border">
			<!-- Start Header -->
			<div class="header">
				<div style="float: left; padding: 0px 0px 0px 6px;">
					<p>&nbsp;</p>
					<a href="http://www.myspace.com/paydirtau/radio">
					<?php echo $html->image('k10387380.jpg', array('style' => 'height:90px; border: 0px;')); ?>
					</a>
				</div>
				<div style="float: right; padding: 0px 0px 0px 0px;">
					<font
						style="font-size: 48.0pt; font-weight: bold; color: #dc9e38">www</font><font
						style="font-size: 72.0pt; font-weight: bold; color: #dc9e38">.PayDirtDollars.</font><font
						style="font-size: 48.0pt; font-weight: bold; color: #dc9e38">com</font>
				</div>
			</div>
			<!-- End Header -->
			<!-- Start Right Column -->
			<div id="rightcolumn">
				<!-- Start Main Content -->
				<div class="maincontent">
					<center>
						<b><font color="red"><?php $session->flash(); ?> </font> </b>
					</center>
					<div class="content-top"></div>
					<div class="content-mid">

						<?php echo $content_for_layout; ?>

					</div>
					<div class="content-bottom"></div>
				</div>
				<!-- End Main Content -->
			</div>
			<!-- End Right Column -->
		</div>
		<!-- End Border -->
		<!-- Start Footer -->
		<div id="footer">
			<font size="2" color="white"><b>Copyright &copy; 2010 PayDirtDollars All
					Rights Reserved.&nbsp;&nbsp;</b> </font>
		</div>
		<!-- End Footer -->
	</div>
	
	<!-- To avoid delays, initialize Cufón before other scripts at the bottom -->
	<script type="text/javascript"> Cufon.now(); </script>
</body>
</html>
