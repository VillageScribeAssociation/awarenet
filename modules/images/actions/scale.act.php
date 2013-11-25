<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//-------------------------------------------------------------------------------------------------
//*	send an image at the specified size, managing browser cache, etc
//-------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	$size = 'full';									//%	preset image size [string]

	if (true == array_key_exists('size', $kapenta->request->args)) { $size = $kapenta->request->args['size']; }
	if (true == array_key_exists('s', $kapenta->request->args)) { $size = $kapenta->request->args['s']; }
	if (true == array_key_exists('p', $kapenta->request->args)) { $size = $kapenta->request->args['p']; }

	if ('' == $kapenta->request->ref) { $page->do404('Image not specified.'); }

	$model = new Images_Image($kapenta->request->ref);			//%	[object]

	if (false == $model->loaded) { $page->do404('Image not found'); }
	if ('' == $model->fileName) { $page->do404('File missing.'); }
	if (false == $model->transforms->presetExists($size)) { $page->do404('Invalid size.'); }	

	$lmDate = date(DATE_RFC1123, $kapenta->strtotime($model->createdOn));
	$eTag = md5($lmDate . $size . $model->hash);
	$fileName = '';

	//echo "eTag: $eTag<br/>\n";

	//----------------------------------------------------------------------------------------------
	//	check for If-Modified-Since header
	//----------------------------------------------------------------------------------------------

	if (
		(true == array_key_exists('HTTP_IF_MODIFIED_SINCE', $_SERVER)) ||
		(true == array_key_exists('HTTP_IF_NONE_MATCH', $_SERVER)) 
	) {

		if ($eTag == $_SERVER['HTTP_IF_NONE_MATCH']) {
			header('Last-Modified: ' . $lmDate);
			header("ETag: \"" . $eTag . "\"");
			header('Cache-Control: max-age=3600');
	 	    header('HTTP/1.0 304 Not Modified');
			echo ""; flush(); die();
		}
	}

	//----------------------------------------------------------------------------------------------
	//	scale down fixed width if mobile browser (try match screen size)
	//----------------------------------------------------------------------------------------------

	if ('true' == $session->get('mobile')) {
		$maxWidth = (int)$session->get('contentWidth');
		if (0 == $maxWidth) { $maxWidth = 320; }			//TODO: registry setting

		if (true == array_key_exists($size, $model->transforms->presets)) {
			$meta = $model->transforms->presets[$size];
			
			if (('fixed_width' == $meta['type']) && ($meta['width'] > $maxWidth)) {
				$size = 'width' . $maxWidth;
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	create the transform if it does not exist
	//----------------------------------------------------------------------------------------------
	if (false == $model->transforms->has($size)) {
		$model->transforms->make($size);
	}

	if (true == $model->transforms->has($size)) {
		$fileName = $model->transforms->fileName($size);
	}

	if ('' == $fileName) {
		$fileName = 'data/images/unavailable/unavailable_' . $size . '.jpg';
		$page->do302($fileName);
	}

	//----------------------------------------------------------------------------------------------
	//	send to client
	//----------------------------------------------------------------------------------------------
	header('Content-Type: image/jpeg');
	header('Last-Modified: ' . $lmDate);
	header("ETag: \"" . md5($lmDate . $size) . "\"");
	header('Cache-Control: max-age=3600');
	header('Content-Length: ' . $kapenta->fs->size($fileName));
	readfile($kapenta->installPath . $fileName);	

?>
