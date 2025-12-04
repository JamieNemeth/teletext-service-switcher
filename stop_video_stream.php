<?php
	$_POST["runningVideoStream"] = "";
	include "save_data.php";
	
	exec("sudo killall omxplayer.bin");
?>