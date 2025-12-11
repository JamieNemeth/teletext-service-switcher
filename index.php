<?php
	$data = file_exists("data.json") ? json_decode(file_get_contents("data.json"), true) : array();
	
	exec("pidof vbit2", $vbit2Pids);
	exec("pidof omxplayer.bin", $omxplayerPids);
	
	if (count($vbit2Pids) == 0) $data["runningService"] = "";
	if (count($omxplayerPids) == 0) $data["runningVideoStream"] = ""; $data["runningVideoStreamUrl"] = "";
	
	exec("ifconfig | grep -Eo 'inet (addr:)?([0-9]*\.){3}[0-9]*' | grep -Eo '([0-9]*\.){3}[0-9]*' | grep -v '127.0.0.1'", $hostIP);
?>
<!DOCTYPE html>
<html>
	<head>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
		<link href="https://fonts.googleapis.com/css2?family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&amp;display=swap" rel="stylesheet">
		<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
		<style>		
			.row:not(.header-row) {
				min-height: 54px;
			}
		
			.row-bordered {
				background-color: #404040;
				border-top: 1px solid #505050;
			}
			
			body {
				background-color: #0d0d0d;
				font-family: 'Titillium Web', sans-serif;
			}
			
			p {
				margin-bottom: 0.2rem;
			}
			
			.nav-link .bi, .btn .bi {
				margin-right: 4px;
			}
			
			.nav-link {
				color: rgb(181, 0, 178);
				background-color: rgb(26, 0, 25) !important;
				
				border: transparent !important;
				border-width: 0 1px 1px;
			}
			
			.nav-link:hover {
				color: white;
				background-color: rgb(41, 0, 40) !important;
				
				border: transparent !important;
				border-width: 0 1px 1px;
			}
			
			.nav-link.active {
				color: white !important;
				background-color: rgb(64, 0, 63) !important;
				font-weight: bold;
				
				border: 1px solid rgb(64, 0, 63) !important;
				border-width: 0 1px 1px;
			}
			
			.nav-tabs {
				border: transparent !important;
				border-width: 0 1px 1px;
			}
			
			.tab-content {
				padding: 1rem;
				border: 1px solid rgb(64, 0, 63);
				border-width: 0 1px 1px;
				background-image: linear-gradient(rgb(64, 0, 63), rgb(26, 0, 25));
				margin-bottom: 12px;
			}
		</style>
		
		<script>
			function onSaveDataClick() {
				var username = $("input#username").val();
				var localServicesFolder = $("input#localServicesFolder").val();
				var tvHeadendUrl = $("input#tvHeadendUrl").val();
				
				$.ajax({
					method: "POST",
					url: "save_data.php",
					data: { username: username, localServicesFolder: localServicesFolder, tvHeadendUrl: tvHeadendUrl },
					success: function() { window.location.reload(); }
				});
			}
		
			function onServiceSwitchClick(buttonElement, serviceType, runningService) {
				buttonElement.outerHTML = '<button type="button" class="btn btn-primary" disabled><i class="bi bi-play-fill"></i> Starting service...</button>';
				
				$.ajax({
					method: "POST",
					url: "switch_service.php",
					data: { serviceType: serviceType, runningService: decodeURIComponent(runningService) }
				});
				
				setTimeout(function() { window.location.reload(); }, 4000);
			}
			
			function onStopOutputClick(buttonElement) {
				buttonElement.outerHTML = '<button type="button" class="btn btn-danger" style="margin-left: 18px;" disabled><i class="bi bi-stop-fill"></i> Stopping output...</button>';
				
				$.ajax({
					method: "POST",
					url: "stop_output.php",
					success: function() { window.location.reload(); }
				});
			}
			
			function onVideoStreamSwitchClick(buttonElement, runningVideoStream, runningVideoStreamUrl) {
				buttonElement.outerHTML = '<button type="button" class="btn btn-primary" disabled><i class="bi bi-play-fill"></i> Starting video stream...</button>';
				
				$.ajax({
					method: "POST",
					url: "switch_video_stream.php",
					data: { runningVideoStream: decodeURIComponent(runningVideoStream), runningVideoStreamUrl: decodeURIComponent(runningVideoStreamUrl) }
				});
				
				setTimeout(function() { window.location.reload(); }, 2000);
			}
			
			function onStopVideoStreamClick(buttonElement) {
				buttonElement.outerHTML = '<button type="button" class="btn btn-danger" style="margin-left: 18px;" disabled><i class="bi bi-stop-fill"></i> Stopping video stream...</button>';
				
				$.ajax({
					method: "POST",
					url: "stop_video_stream.php",
					success: function() { window.location.reload(); }
				});
			}
			
			function onRefreshChannelsClick(buttonElement) {
				buttonElement.outerHTML = '<button type="button" class="btn btn-primary" disabled><i class="bi bi-arrow-clockwise"></i> Refreshing channel list...</button>';
				
				$.ajax({
					method: "GET",
					url: "tvheadend_channels_to_json.php",
					success: function() { window.location.reload(); }
				});
			}
			
			$(function() {
				if (sessionStorage.hasOwnProperty("currentTab")) {
					$("#" + sessionStorage.getItem('currentTab')).tab("show");
				}
				
				window.onbeforeunload = function () {
					sessionStorage.setItem("currentTab", $(".nav-link.active").attr("id"));
				}
			});
		</script>
	</head>
	<body class="text-white p-5">
		<div class="container">
			<h1>Teletext service switcher<span style="font-size: 22pt; color: #A0A0A0;"> @ <?php echo gethostname(); ?> (<?php echo $hostIP[0]; ?>)</span></h1>
			<ul class="nav nav-tabs" id="containerTabsNav" role="tablist">
				<li class="nav-item" role="presentation">
					<button class="nav-link active" id="container-installed-services-tab-nav" data-bs-toggle="tab" data-bs-target="#container-installed-services-tab-content" type="button" role="tab" aria-controls="container-installed-services-tab-content" aria-selected="">Installed services</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="container-local-services-tab-nav" data-bs-toggle="tab" data-bs-target="#container-local-services-tab-content" type="button" role="tab" aria-controls="container-local-services-tab-content" aria-selected="">Local services</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="container-video-streams-tab-nav" data-bs-toggle="tab" data-bs-target="#container-video-streams-tab-content" type="button" role="tab" aria-controls="container-video-streams-tab-content" aria-selected=""<?php echo (array_key_exists("tvHeadendUrl", $data) ? : ' style="display: none;"'); ?>>Video streams</button>
				</li>
				<li class="nav-item ms-auto" role="presentation">
					<button class="nav-link" id="container-settings-tab-nav" data-bs-toggle="tab" data-bs-target="#container-settings-tab-content" type="button" role="tab" aria-controls="container-settings-tab-content" aria-selected=""><i class="bi bi-gear"></i> Settings</button>
				</li>
			</ul>
			
			<div class="tab-content" id="containerTabsContent">
			
				<div class="tab-pane fade show active" id="container-installed-services-tab-content" role="tabpanel" aria-labelledby="container-installed-services-tab-nav">
					<div id="installed-services-page-container" class="container">
						<div class="row header-row p-2">
							<div class="col-8 align-self-center"><b>Service name</b></div>
							<div class="col-4"><b>Switch to service</b></div>
						</div>
						
						<?php
							$installedServicesFolderUri = array_key_exists("username", $data) ? "/home/" . $data["username"] . "/.teletext-services" : null;
						
							if ($installedServicesFolderUri && is_dir($installedServicesFolderUri))
							{
								$availableServicesUris = glob($installedServicesFolderUri . "/*", GLOB_ONLYDIR);
								
								foreach ($availableServicesUris as $availableServiceUri)
								{
									$ttiFiles = glob($availableServiceUri . "/*.tti");
									
									if (count($ttiFiles) > 0)
									{
										$availableServiceBasenameUri = basename($availableServiceUri);
										$runServiceButtonString = (array_key_exists("runningService", $data) && $availableServiceBasenameUri == $data["runningService"])
											? '<i class="bi bi-play-fill"></i> Running <button type="button" class="btn btn-danger" style="margin-left: 18px;" onclick="onStopOutputClick(this);"><i class="bi bi-stop-fill"></i> Stop output</button>'
											: '<button type="button" class="btn btn-primary" onclick="onServiceSwitchClick(this, \'installed\', \'' . urlencode($availableServiceBasenameUri) . '\');"><i class="bi bi-play-fill"></i> Start service</button>';
										
										echo <<<STR
												<div class="row row-bordered p-2 align-items-center">
													<div class="col-8">{$availableServiceBasenameUri}</div>
													<div class="col-4">{$runServiceButtonString}</div>
												</div>
											STR;
									}
								}
							}
						?>
					</div>
				</div>
				
				<div class="tab-pane fade" id="container-local-services-tab-content" role="tabpanel" aria-labelledby="container-local-services-tab-nav">
					<div id="local-services-page-container" class="container">
						<div class="row header-row p-2">
							<div class="col-8 align-self-center"><b>Service name</b></div>
							<div class="col-4"><b>Switch to service</b></div>
						</div>
						
						<?php
							if (array_key_exists("localServicesFolder", $data))
							{
								$availableLocalServiceUris = glob($data["localServicesFolder"] . "/*", GLOB_ONLYDIR);
								
								foreach ($availableLocalServiceUris as $availableLocalServiceUri)
								{
									$ttiFiles = glob($availableLocalServiceUri . "/*.tti");
									
									if (count($ttiFiles) > 0)
									{
										
										$availableLocalServiceBasenameUri = basename($availableLocalServiceUri);
										$runServiceButtonString = (array_key_exists("runningService", $data) && $availableLocalServiceBasenameUri == $data["runningService"])
											? '<i class="bi bi-play-fill"></i> Running <button type="button" class="btn btn-danger" style="margin-left: 18px;" onclick="onStopOutputClick(this);"><i class="bi bi-stop-fill"></i> Stop output</button>'
											: '<button type="button" class="btn btn-primary" onclick="onServiceSwitchClick(this, \'local\', \'' . urlencode($availableLocalServiceBasenameUri) . '\');"><i class="bi bi-play-fill"></i> Start service</button>';
										
										echo <<<STR
												<div class="row row-bordered p-2 align-items-center">
													<div class="col-8">{$availableLocalServiceBasenameUri}</div>
													<div class="col-4">{$runServiceButtonString}</div>
												</div>
											STR;
									}
								}
							}
						?>
					</div>
				</div>
				
				<div class="tab-pane fade" id="container-video-streams-tab-content" role="tabpanel" aria-labelledby="container-video-streams-tab-nav"<?php echo (array_key_exists("tvHeadendUrl", $data) ? : ' style="display: none;"'); ?>>
					<div id="video-streams-page-container" class="container">
						
						<div class="row mb-3">
							<button type="button" class="btn btn-primary" onclick="onRefreshChannelsClick(this);"><i class="bi bi-arrow-clockwise"></i> Refresh channel list</button>
						</div>
					
						<div class="row header-row p-2">
							<div class="col-8 align-self-center"><b>Service name</b></div>
							<div class="col-4"><b>Switch to service</b></div>
						</div>
						
						<?php
							$videoStreams = file_exists("video_streams.json") ? json_decode(file_get_contents("video_streams.json"), true) : array();
						
							foreach ($videoStreams as $videoStream)
							{
								$runVideoStreamButtonString = ($videoStream["name"] == $data["runningVideoStream"])
											? '<i class="bi bi-play-fill"></i> Running <button type="button" class="btn btn-danger" style="margin-left: 18px;" onclick="onStopVideoStreamClick(this);"><i class="bi bi-stop-fill"></i> Stop video stream</button>'
											: '<button type="button" class="btn btn-primary" onclick="onVideoStreamSwitchClick(this, \'' . urlencode($videoStream["name"]) . '\', \'' . urlencode($videoStream["url"]) . '\');"><i class="bi bi-play-fill"></i> Start video stream</button>';
										
										echo <<<STR
												<div class="row row-bordered p-2 align-items-center">
													<div class="col-8">{$videoStream["name"]}</div>
													<div class="col-4">{$runVideoStreamButtonString}</div>
												</div>
											STR;
							}
						?>
					</div>
				</div>
				
				<div class="tab-pane fade" id="container-settings-tab-content" role="tabpanel" aria-labelledby="container-settings-tab-nav">
					<div id="settings-page-container" class="container">
						<div class="row p-2 align-items-end">
							<div class="col-2">
								<div class="form-group">
									<label for="username">Username</label>
									<input class="form-control" id="username" type="text" placeholder="pi" value="<?php if (array_key_exists("username", $data)) echo $data["username"]; ?>">
								</div>
							</div>
							<div class="col-3">
								<div class="form-group">
									<label for="localServicesFolder">Local services root folder</label>
									<input class="form-control" id="localServicesFolder" type="text" placeholder="/" value="<?php if (array_key_exists("localServicesFolder", $data)) echo $data["localServicesFolder"]; ?>"></input>
								</div>
							</div>
							<div class="col-3">
								<div class="form-group">
									<label for="tvHeadendUrl">TVHeadend URL</label>
									<input class="form-control" id="tvHeadendUrl" type="text" placeholder="http://<IP or hostname>:9981/" value="<?php if (array_key_exists("tvHeadendUrl", $data)) echo $data["tvHeadendUrl"]; ?>"></input>
								</div>
							</div>
							<div class="col-2">
								<button type="button" class="btn btn-primary" onclick="onSaveDataClick();"><i class="bi bi-floppy"></i> Save</button>
							</div>
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</body>
</html>