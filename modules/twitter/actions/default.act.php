<?

//--------------------------------------------------------------------------------------------------
//*	default action for twitter module is the configuration page
//--------------------------------------------------------------------------------------------------
//+	note that this is an extremely basic twitter integration, account credentials are stored in 
//+	plain text (never good), however the alernetive is to store a private key to sign OAuth
//+	messages with, so the result is more or less the the same in terms of security and this is 
//+	simpler.

	require_once($kapenta->installPath . 'modules/twitter/actions/settings.act.php');

?>
