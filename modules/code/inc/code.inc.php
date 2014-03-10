<?php

//==================================================================================================
//*	utility functions
//==================================================================================================

//--------------------------------------------------------------------------------------------------
//	get UID of folder this file is in (create folders if necessary)
//--------------------------------------------------------------------------------------------------

function codeGetFolderUID($path, $projectUID) {

	$path = str_replace("//", "/", $path);

	echo "[i] Getting Folder UID: path=$path project=$projectUID <br/>\n";

	$project = new CodeProject($projectUID);
	$parentUID = $project->getRootFolder();
	echo "project root folder: $parentUID <br/>\n";

	$parts = explode('/', $path);
	foreach($parts as $title) {
	  if ($title != '') {
		$title = $title . '/';
		$subs = codeListSubFolders($parentUID);
		$found = false;
		
		// check against subfolders
		foreach($subs as $subUID => $subTitle) { 
			if ($title == $subTitle) { 
				$found = $subUID; 
			} 
		}

		// create it if it does not exist
		if ($found == false) { 
			$found = codeMkDir($parentUID, $projectUID, $title); 
			//echo "[i] Making directory $title (parent: $parentUID project: $projectUID)<br/>\n";
		}

		// and on to the next one
		$parentUID = $found;

	  }
	}

	return $parentUID;
}

//--------------------------------------------------------------------------------------------------
//	making a new folder node
//--------------------------------------------------------------------------------------------------

function codeListSubFolders($folderUID) {
	global $kapenta;

	$subs = array();
	$sql = "select * from code where type='folder' and parent='" . $folderUID . "'";
	$result = $kapenta->db->query($sql);

	while ($row = $kapenta->db->fetchAssoc($result)) { 
			$row = $kapenta->db->rmArray($row);
			$subs[$row['UID']] = $row['title']; 
	}

	return $subs;
}

//--------------------------------------------------------------------------------------------------
//	making a new folder node
//--------------------------------------------------------------------------------------------------

function codeMkDir($parentUID, $projectUID, $title) {
	echo "creating $title subdirectory on $parentUID (project: $projectUID)<br/>";
	$model = new Code();
	$model->data['UID'] = $kapenta->createUID();
	$model->data['project'] = $projectUID;
	$model->data['parent'] = $parentUID;
	$model->data['type'] = 'folder';
	$model->data['title'] = $title;
	$model->data['version'] = '1';
	$model->data['revision'] = '0';
	$model->data['description'] = "Imported $title folder";
	$model->data['content'] = '';
	$model->data['author'] = 'cron-bot';
	$model->data['createdOn'] = mysql_datetime();
	$model->data['hash'] = sha1($title);
	$model->save();
	return $model->data['UID'];
}

//--------------------------------------------------------------------------------------------------
//	tidy long string (move to /core/utils.inc.php?)
//--------------------------------------------------------------------------------------------------

function base64wrap($txt) {
	$retVal = '';
	while (strlen($txt) > 110) {
		$retVal .= substr($txt, 0, 110) . "\n";		
		$txt = substr($txt, 110);
	}
	return $retVal . $txt;
}

?>
