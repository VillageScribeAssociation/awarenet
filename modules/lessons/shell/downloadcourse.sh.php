<?

	require_once('../../../shinit.php');
	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');

//--------------------------------------------------------------------------------------------------
//*	administrative shell script to repeatedly pull from a peer
//--------------------------------------------------------------------------------------------------
//ref: UID of a P2P_Peer object

	if (1 == count($argv)) { echo "Usage: downloadcourse.sh.php [courseId]\n"; die(); }

	$model = new Lessons_Course($argv[1]);

	if (false == $model->loaded) { echo "Course not found."; }

	foreach($model->documents as $document) {
		if (false == $kapenta->fs->exists($document['file'])) {

			if ('youtube://' == substr($document['downloadfrom'], 0 , 10)) {
				lessons_get_youtube(substr($document['downloadfrom'], 10), $document['file']);
			}

		} else {
			echo "OK. " . $document['file'] . "\n";
		}
	}

	function lessons_get_youtube($yId, $fileName) {
		global $kapenta;
		echo "Downloading: " . $fileName . "\n";

		//TODO - better sanitization of yID
		$yId = str_replace(array(' ', '/', '.', '|','>','<'), array('','','','','',''), $yId);

		$shellCmd = 'youtube-dl'
		 . ' --format=34'
		 . ' --output="' . $kapenta->registry->get('kapenta.installpath') . $fileName . '"'
		 . ' "http://youtube.com/watch?v=' . $yId . '"';

		echo $shellCmd . "\n";
		echo shell_exec($shellCmd);
	}

?>
