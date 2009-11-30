<?

//--------------------------------------------------------------------------------------------------
//	installer for images module (creates table)
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/users/models/users.mod.php');
require_once($installPath . 'modules/users/models/userlogin.mod.php');
require_once($installPath . 'modules/users/models/friendships.mod.php');

function install_users_module() {
	global $installPath;
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }

	$report = '';

	//------------------------------------------------------------------------------------------
	//	create users table
	//------------------------------------------------------------------------------------------

	$model = new Users();
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
