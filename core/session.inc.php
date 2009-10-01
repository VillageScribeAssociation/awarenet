<?

//--------------------------------------------------------------------------------------------------
//	handling of sessions and session variables
//--------------------------------------------------------------------------------------------------

session_start();

// sUID - a unique identifier for this session, used for debugging
if (!(array_key_exists('sUID', $_SESSION))) { $_SESSION['sUID'] = createUID(); }

// sUser, the username for the current session (username=public if not logged in)
if (!(array_key_exists('sUser', $_SESSION))) { $_SESSION['sUser'] = 'public'; }

// sUserUID, the user UID for the current session (UID=public if not logged in)
if (!(array_key_exists('sUserUID', $_SESSION))) { $_SESSION['sUserUID'] = 'public'; }

// sMessage, this is used for passing information for the user between pages
if (!(array_key_exists('sMessage', $_SESSION))) { $_SESSION['sMessage'] = ''; }

// sMessage, this is used for passing information for the user between pages
if (!(array_key_exists('sCaptcha', $_SESSION))) { $_SESSION['sCaptcha'] = array(); }

?>
