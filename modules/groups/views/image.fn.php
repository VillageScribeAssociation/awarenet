<?

	require_once($kapenta->installPath . 'modules/images/views/default.mod.php');

//--------------------------------------------------------------------------------------------------
//|	find the group's logo/picture (300px) or a blank image   (DEPRECATED)
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Groups_Group object [string]
//opt: groupUID - overrides raUID [string]
//opt: size - width100, width200, width300, width570, thumb, thumbsm or thumb90 [string]
//opt: link - link to larger image (yes|no) [string]
//TODO: use images::default for this

function groups_image($args) { return images_default($args); }

//--------------------------------------------------------------------------------------------------

?>
