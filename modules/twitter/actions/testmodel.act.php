<?

	require_once($kapenta->installPath . 'modules/twitter/models/tweet.mod.php');

	echo "Loading model...<br/>";
	$model = new Twitter_Tweet('567011543204906724');

	echo "content: " . $model->content . "<br/>\n";
	echo "status: " . $model->status . "<br/>";

	$model->status = 'test';
	$model->save();

	echo "saved();";
	//$model->save();

	$m2 = new Twitter_Tweet('567011543204906724');
	echo "status-reloaded: " . $m2->status . "<br/>\n";

?>
