<?php
	include "save_data.php";
	
	exec("sudo killall pngview");
	exec("sudo killall omxplayer.bin");
	
	exec('/usr/bin/omxplayer --win "-66 75 796 565" ' . urldecode($_POST["runningVideoStreamUrl"]) . ' --live --deinterlace --blank --no-osd --no-keys --vol 600 --fps 25 --threshold 3');
?>
