<?php
	include "save_data.php";
	if (!array_key_exists("username", $data))
	{
		if ($_POST["serviceType"] == "local" && !array_key_exists("localServicesFolder"))
		include "stop_output.php";
		die();
	}
	
	exec("pidof vbit2", $vbit2Pids);
	exec("pidof teletext", $teletextPids);
	
	if (count($vbit2Pids) > 0) 
	{
		foreach ($vbit2Pids as $vbit2Pid)
		{
			exec("sudo kill -9 " . $vbit2Pid);
		}
	}
	
	if (count($teletextPids) > 0)
	{
		foreach ($teletextPids as $teletextPid)
		{
			exec("sudo kill -9 " . $teletextPid);
		}
	}
	
	$installedServicesFolderUri = "/home/" . $data["username"] . "/.teletext-services";
	
	if ($_POST["serviceType"] == "installed")
	{		
		if (is_dir($installedServicesFolderUri . "/" . $data["runningService"] . "/.git"))
		{
			exec(' echo "*/5 * * * * ' . $data["username"] . ' git -C ' . $installedServicesFolderUri . '/' . $data["runningService"] . ' pull --depth 120" | sudo tee /etc/cron.d/teletext-service-switcher');
		}
		else if (is_dir($installedServicesFolderUri . "/" . $data["runningService"] . "/.svn"))
		{
			exec(' echo "*/5 * * * * ' . $data["username"] . ' svn update ' . $installedServicesFolderUri . '/' . $data["runningService"] . '" | sudo tee /etc/cron.d/teletext-service-switcher');
		}
	}
	else
	{
		exec('sudo rm /etc/cron.d/teletext-service-switcher || true');
	}
	
	exec('sudo /home/' . $data["username"] . '/raspi-teletext/./tvctl on');
	exec('/home/' . $data["username"] . '/vbit2/vbit2 --dir "' . ($_POST["serviceType"] == "installed" ? $installedServicesFolderUri : $data["localServicesFolder"]) . '/' . $data["runningService"] . '" | /home/' . $data["username"] . '/raspi-teletext/teletext -');
?>