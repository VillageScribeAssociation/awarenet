<?

//--------------------------------------------------------------------------------------------------
//	installer for images module (creates table)
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/users/models/user.mod.php');
require_once($installPath . 'modules/users/models/userlogin.mod.php');
require_once($installPath . 'modules/users/models/friendship.mod.php');

function install_users_module() {
	global $installPath;
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }

	$report = '';

	//------------------------------------------------------------------------------------------
	//	create users table
	//------------------------------------------------------------------------------------------

	$model = new User();
	$report .= $model->install();

	//------------------------------------------------------------------------------------------
	//	create userlogin table
	//------------------------------------------------------------------------------------------
	
	$model = new UserLogin();
	$this->report .= $model->install();	

	//------------------------------------------------------------------------------------------
	//	create friendships table
	//------------------------------------------------------------------------------------------
	
	$model = new Friendship();
	$this->report .= $model->install();	

	return $report;
}

?>
