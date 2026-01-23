<?php
	$data = file_exists("data.json") ? json_decode(file_get_contents("data.json"), true) : die();
	$runningVideoStreamUrl = $data["runningVideoStreamUrl"];
	
	exec("pidof omxplayer.bin", $omxplayerPids);
	if (count($omxplayerPids) == 0) 
	{
		exec('/usr/bin/omxplayer --win "-66 75 796 565" ' . $runningVideoStreamUrl . ' --live --deinterlace --blank --no-osd --no-keys --vol 600 --fps 25 --threshold 0');
	};
?>
