<?php
	$data = file_exists("data.json") ? json_decode(file_get_contents("data.json"), true) : array();
	if (isset($_POST["username"])) $data["username"] = trim($_POST["username"]);
	if (isset($_POST["localServicesFolder"])) $data["localServicesFolder"] = trim($_POST["localServicesFolder"]);
	if (isset($_POST["tvHeadendUrl"])) $data["tvHeadendUrl"] = trim(urldecode($_POST["tvHeadendUrl"]));
	if (isset($_POST["runningService"])) $data["runningService"] = trim(urldecode($_POST["runningService"]));
	if (isset($_POST["runningVideoStream"])) $data["runningVideoStream"] = trim(urldecode($_POST["runningVideoStream"]));
	if (isset($_POST["runningVideoStreamUrl"])) $data["runningVideoStreamUrl"] = trim(urldecode($_POST["runningVideoStreamUrl"]));
	
	file_put_contents("data.json", json_encode($data));
?>