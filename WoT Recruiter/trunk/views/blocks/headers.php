<!DOCTYPE html>
<html>
<head>
	<title>WoT Team Creator</title>
	<meta http-equiv=Content-Type content="text/html;charset=UTF-8">
	<style>
		
	</style>
	<link rel="stylesheet" type="text/css" media="screen,print" href="style.css" />
	
	<script type="text/javascript" src="js/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.10.4.custom.min.js"></script>
	<script type="text/javascript" src="js/vehiclesinterview.js"></script>
	<script type="text/javascript" src="js/tankopedia.js"></script>
	<script type="text/javascript" src="js/i18n/vehicles_ru.js"></script>
	<script type="text/javascript" src="js/vehiclestemplate.js"></script>
	<script type="text/javascript" src="js/candidatesstat.js"></script>
	<script type="text/javascript" src="js/squads.js"></script>
	<script type="text/javascript" src="js/sortabletable.js"></script>
	<script type="text/javascript">
		$(function(){
			$('.spoiler').click(function(){
				$(this).children('.spoiler-body').toggle(500);
			});
		});

		var tankopedia = new Tankopedia();
	</script>
</head>
<body>