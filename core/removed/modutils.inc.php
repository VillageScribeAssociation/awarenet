<?

//--------------------------------------------------------------------------------------------------
//*	utility functions for working with modules
//--------------------------------------------------------------------------------------------------
//+	This is deprecated, should be moved to images module

//==================================================================================================
//	images
//==================================================================================================

//--------------------------------------------------------------------------------------------------
//|	finds the default image owned by some object, if any, and returns the image record
//--------------------------------------------------------------------------------------------------
//arg: refModule - module name [string]
//arg: refUID - UID of object which may own images [string]
//returns: the whole record as associative array or false if no such image [array]
//: TODO: find any remaing instances of this and remove them

function imgGetDefault($refModule, $refUID) {	
	global $session;

	$session->msg("Function removed: imgGetDefault");
}

//--------------------------------------------------------------------------------------------------
//|	finds the default image owned by some object, if any, and returns image URL
//--------------------------------------------------------------------------------------------------
//arg: refModule - module name [string]
//arg: refUID - UID of object which may own images [string]
//arg: size - any standard transform size supported by the image module, eg, thumb90 [string]
//returns: full URL to image at given size, or URL to 'not found' image [string]
//: TODO: find any remaing instances of this and remove them

function imgGetDefaultUrl($refModule, $refUID, $size) {
	global $session;

	$session->msg("DEPRECATED: imgGetDefaultUrl => block images, defaulturl");
	$row = imgGetDefault($refModule, $refUID);
	if ($row == false) { return '%%serverPath%%/themes/%%%defaultTheme%%/unavailable/' . $size; }
	return '%%serverPath%%/images/' . $size . '/' . $row['recordAlias'];
}

//--------------------------------------------------------------------------------------------------
//|	finds the default image owned by some object, if any, and returns image UID
//--------------------------------------------------------------------------------------------------
//arg: refModule - module name [string]
//arg: refUID - UID of object which may own images [string]
//returns: UID of image record or false if no default image [string] [bool]
//: TODO: find any remaing instances of this and remove them

function imgGetDefaultUID($refModule, $refUID) {
	global $session;
	$session->msg("DEPRECATED: imgGetDefaultUrl => block images, defaulturl");

}

?>
