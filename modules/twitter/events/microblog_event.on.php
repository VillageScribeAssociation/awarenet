<?

require_once($kapenta->installPath . 'modules/twitter/models/tweet.mod.php');

//-------------------------------------------------------------------------------------------------
//|	fired by module events, indicates a microblog post needs to be made
//-------------------------------------------------------------------------------------------------
//arg: refModule - name of module which raised this event
//arg: refModel - type of object this event is about
//arg: refUID - UID of object this event is about
//arg: message - UID of the new comment

function twitter__cb_microblog_event($args) {
		global $kapenta;
		global $kapenta;
		global $kapenta;

	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refModel', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }
	if (false == array_key_exists('message', $args)) { return false; }

	if (false == $kapenta->moduleExists($args['refModule'])) { return false; }
	if (false == $kapenta->db->objectExists($args['refModel'], $args['refUID'])) { return false; }

	$message = substr($args['message'], 0, 140);

	$model = new Twitter_Tweet();
	$model->refModule = $args['refModule'];
	$model->refModel = $args['refModel'];
	$model->refUID = $args['refUID'];
	$model->content = $message;
	$model->status = 'new';
	$report = $model->save();

	if ('' != $report) { return false; }
	return true;
}

//-------------------------------------------------------------------------------------------------
?>
