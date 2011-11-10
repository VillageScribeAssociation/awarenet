
//==================================================================================================
//*	Javascript for use on edit profile page
//==================================================================================================

//--------------------------------------------------------------------------------------------------
//|	set a user's default profile picture
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of a Users_User object [string]
//arg: imgUID - UID of an Images_Image object [string]

function users_setDefaultPicture(userUID, imgUID) {
	var url = jsServerPath + 'images/makedefaultjs/';
	var params = 'action=makeDefault&UID=' + imgUID;

	var cbFn = function(responseText, status) {
		var blockTag = '[[:users::chooseavatar::userUID=' + userUID + ':]]';
		klive.removeBlock(blockTag, false);							// clear cache
		klive.bindDivToBlock('divChooseAvatar', blockTag, false);	// force reload
	}

	kutils.httpPost(url, params, cbFn);
}
