<?

//-------------------------------------------------------------------------------------------------
//*	utility functions to compress and decompress files with bzip2
//-------------------------------------------------------------------------------------------------
//:	Since not all Kapenta instances have the bzip php library installed, we use a fallback to the
//:	command line bzip tool.

function p2p_bzcompress($str, $chatty = false) {
	global $kapenta;
	global $session;

	if (true == function_exists('bzcompress')) {

		//-----------------------------------------------------------------------------------------
		//	this instance has native bzip2 support
		//-----------------------------------------------------------------------------------------
		if (true == $chatty) { echo "compress using default / built in<br/>\n"; }
		return bzcompress($str);

	} else {

		//-----------------------------------------------------------------------------------------
		//	attempting to use command line bzip2
		//-----------------------------------------------------------------------------------------
		if (true == $chatty) { echo "decompress using command line tool<br/>\n"; }
		$fileName = 'data/temp/' . (string)$kapenta->time() . '_' . $kapenta->createUID() . '.tmp';
		$kapenta->fs->put($fileName, $str);

		shell_exec("bzip2 -z $fileName");

		$fileName .= ".bz2";

		if (true == $kapenta->fs->exists($fileName)) {
			$compressed = $kapenta->fs->get($fileName);
			$kapenta->fileDelete($fileName);
			return $compressed;
		} else {
			$kapenta->session->msgAdmin("Command line bzip2 compression error.");
			return '';
		}

	}
}

function p2p_bzdecompress($str, $chatty = false) {
	global $kapenta;

	if (true == function_exists('bzdecompress')) {

		//-----------------------------------------------------------------------------------------
		//	this instance has native bzip2 support
		//-----------------------------------------------------------------------------------------
		if (true == $chatty) { echo "decompress using default / built in<br/>\n"; }
		return bzdecompress($str);

	} else {

		//-----------------------------------------------------------------------------------------
		//	attempting to use command line bzip2
		//-----------------------------------------------------------------------------------------
		if (true == $chatty) { echo "decompress using command line tool<br/>\n"; }

		$fileName = ''
			. 'data/temp/'
			. (string)$kapenta->time() . '_' . $kapenta->createUID() . '.tmp.bz2';

		$kapenta->fs->put($fileName, $str);

		shell_exec("bzip2 -d $fileName");

		$fileName = substr($fileName, 0, strlen($fileName) - 4);

		if (true == $kapenta->fs->exists($fileName)) {
			$decompressed = $kapenta->fs->get($fileName);
			$kapenta->fileDelete($fileName);
			return $decompressed;
		} else {
			$kapenta->session->msgAdmin("Command line bzip2 decompression error.");
			return '';
		}

	}
}

?>
