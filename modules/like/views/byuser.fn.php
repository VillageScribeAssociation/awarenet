<?

//--------------------------------------------------------------------------------------------------
//|	checks whether some object is liked by a given user
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of a Users_User object [string]
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object [string]
//arg: refUID - UID of potentially liked object [string]
//opt: notcancelled - ignore cancelled likes, default is no (yes|no) [string]
//returns: UID of Like_Something object, empty string if not found or error [bool]

function like_byuser($args) {
	global $kapenta;

	$notCancelled = 'no';				//%	ignore cancelled likes (yes|no) [string]
	$UID = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('userUID', $args)) { return ''; }
	if (false == array_key_exists('refModule', $args)) { return ''; }
	if (false == array_key_exists('refModel', $args)) { return ''; }
	if (false == array_key_exists('refUID', $args)) { return ''; }

	if (true == array_key_exists('notcancelled', $args)) { $notCancelled = $args['notcancelled']; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModule='" . $kapenta->db->addMarkup($args['refModule']) . "'";
	$conditions[] = "refModel='" . $kapenta->db->addMarkup($args['refModel']) . "'";
	$conditions[] = "refUID='" . $kapenta->db->addMarkup($args['refUID']) . "'";
	$conditions[] = "createdBy='" . $kapenta->db->addMarkup($args['userUID']) . "'";

	if ('yes' == $notCancelled) { $conditions[] = "cancelled='no'"; }

	$range = $kapenta->db->loadRange('like_something', '*', $conditions);
	foreach($range as $item) { $UID = $item['UID']; }
	return $UID;
}

?>
