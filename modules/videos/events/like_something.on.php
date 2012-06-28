<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//|	fired when a user confirms a 'like' button or link
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object being liked [string]
//arg: refUID - UID of object being liked [string]

function videos__cb_like_something($args) {
	global $user;
	global $theme;
	global $notifications;

	//----------------------------------------------------------------------------------------------
	//	check event arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refModel', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if ('videos' != $refModule) { return false; }

	//----------------------------------------------------------------------------------------------
	//	notify on like of videos_video objects
	//----------------------------------------------------------------------------------------------

	if ('videos_video' == $refModel) {
		$model = new Videos_Video($refUID);
		if (true == $model->loaded) {

			$creatorName = '[[:users::name::userUID=' . $model->createdBy . ":]]'s";
			if ($model->createdBy == $user->UID) { $creatorName = 'their own'; }
	
			$title = $user->getName() . " likes $creatorName video '" . $model->title . "";
			$url = '%%serverPath%%videos/play/' . $model->alias;

			$content = ''
			 . "[[:like::otherusers"
			 . "::userUID=" . $user->UID 
			 . "::refModule=" . $refModule
			 . "::refModel=" . $refModel
			 . "::refUID=" . $refUID
			 . ":]]";

			$nUID = $notifications->create(
				$refModule, $refModel, $refUID, 
				'like_something', $title, $content, $url
			);

			$notifications->addUser($nUID, $user->UID);
			$notifications->addFriends($nUID, $user->UID);
			$notifications->addSchoolGrade($nUID, $user->school, $user->grade);

		}
	}

	//----------------------------------------------------------------------------------------------
	//	notify on like of videos_gallery objects
	//----------------------------------------------------------------------------------------------

	//TODO:

}

?>
