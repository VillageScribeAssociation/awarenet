<?

	$dnUrl = 'http://mothsorchid.com/sync/getfile/file_ZGF0YS9pbWFnZXMvNC8xLzEvNDExNDA0MTA2MjYwMzY4NTc2X3RodW1ic20uanBn/';
	$result = $sync->curlGet($dnUrl, '66awarenet99');

	$fileName = $installPath . 'data/temp/some/new/dir/file.jpg';
	filePutContents($fileName, $result, 'w+');


?>
