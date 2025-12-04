<?php
	$data = file_exists("data.json") ? json_decode(file_get_contents("data.json"), true) : array();
	$tvHeadendUrl = $data["tvHeadendUrl"];
	if (substr($tvHeadendUrl, -1) != "/") $tvHeadendUrl .= "/";
	
	if (!function_exists('str_starts_with')) {
		function str_starts_with(string $haystack, string $needle): bool
		{
			return strlen($needle) === 0 || strpos($haystack, $needle) === 0;
		}
	}

	$channelsM3U = file_get_contents($tvHeadendUrl . "/playlist/channels.m3u");
	if ($channelsM3U == false) die();
	
	$channelsM3ULines = explode("\n", $channelsM3U);
	
	$channelNumbers = [];
	$channelNames = [];
	$channelUrls = [];
	
	foreach ($channelsM3ULines as $channelsM3ULine)
	{
		if (str_starts_with($channelsM3ULine, "#EXTINF")) 
		{
			preg_match_all('/tvg-chno="([0-9]*)"/', $channelsM3ULine, $channelNumberMatches);
			array_push($channelNumbers, $channelNumberMatches[1][0]);
			array_push($channelNames, explode(",", $channelsM3ULine)[1]);
		}
		
		if (str_starts_with($channelsM3ULine, "http://"))
		{
			array_push($channelUrls, $channelsM3ULine);
		}
	}
	
	$videoStreamData = [];
	
	$channelCount = count($channelNumbers);
	for ($i = 0; $i < $channelCount; $i++)
	{
		array_push($videoStreamData, (object)[
			'number' => $channelNumbers[$i],
			'name' => $channelNames[$i],
			'url' => $channelUrls[$i]
		]);
	}
	
	file_put_contents("video_streams.json", json_encode($videoStreamData, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
	exit();
?>