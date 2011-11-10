<?

//--------------------------------------------------------------------------------------------------
//*	helper object for managing user and role permission sets
//--------------------------------------------------------------------------------------------------
//+	permissions field is formatted in lines: |auto/special|modulename|perm|cond| where condition
//+	may be blank or something like: %%createdBy%%=%%user.UID%%
//+	'auto' permissions are generated from module.xml.php files, 'special' permissions are manually
//+	granted by an administrator.
//+
//+	PERMISSIONS
//+
//+	Users and groups may be granted permissions on individual objects or more often on entire 
//+	classes of objects.  Permissions are defined by the module to which an object belongs, and 
//+	may be exported to other modules (ie, inherited permisisons)
//+
//+ Inheritable permissions are exported by the module which maintains an owned object, say a 
//+	comment which belongs to a blog post.  They are granted according to the relationship between
//+	the owner object and a user.  For example, a user may have permission to delete an image 
//+	belonging to a blog post because the user created that post.  These relationships between 
//+ objects and users are defined by the module which owns an object.
//+
//+	Format of basic permissions:
//+	p|module|model|permission								- blanket permission
//+	c|module|model|permission|(if)|relationship				- conditional permission
//+	
//+	Note that in the case of inherited permissions, the first object is the owner, the second
//+	is the owned object, the permission applies to the owned object, and the condition to the
//+	owner object.
//+
//+ Examples:
//+	c|blog|Blog_Post|edit|(if)|creator						- may edit their own blog posts
//+	p|blog|Blog_Post|create									- may create blog posts
//+ p|blog|Blog_Post|comment-create							- may comment on blog posts
//+	c|blog|Blog_Post|image-create|(if)|creator				- may add images to their own blog posts
//+	c|projects|Projects_Project|images-delete|(if)|member	- may delete images if member of project
//+
//+ TODO: consider adding third permission type 'e', evalutaing a member, say to allow an individual
//+	user to edit an individual document (when)|UID=1234, or to allow a role permissions on a
//+ subset of objects - say members of the 'sales' role can edit wiki pages in category 'marketing',
//+	but not in category 'accounts', or to give 'moderators' the ability to delete comments from
//+	unregistered users without review.

class Users_Permissions {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $members;			//_	array of permission definitions, grouped by module [array:dict]
	var $loaded = false;	//_	set to true when loaded [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: data - serialized permission set [string]	

	function Users_Permissions($data = '') {
		$this->members = array();
		if ('' != $data) {
			$this->members = $this->expand($data);
			$this->loaded = true;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	read [permissions] into array for authentication
	//----------------------------------------------------------------------------------------------
	//arg: serialized - permissions set as raw string [string]
	//returns: permissions set as an array [array]

	function expand($data) {
		$this->members = array();
		$lines = explode("\n", $data);
		foreach($lines as $line) {
		  if (strpos($line, '|') != false) {
			//--------------------------------------------------------------------------------------
			// parse serialized permission into array
			//--------------------------------------------------------------------------------------
			$parts = explode("|", $line);
			$perm = array();

			$perm['type'] = $parts[0];
			$perm['module'] = $parts[1];
			$perm['model'] = $parts[2];
			$perm['permission'] = $parts[3];

			if ('c' == $perm['type']) {	$perm['condition'] = $parts[5];	}
	
			if (false == array_key_exists($perm['module'], $this->members)) 
				{ $this->members[$perm['module']] = array(); }

			//--------------------------------------------------------------------------------------
			// check for and remove duplicates
			//--------------------------------------------------------------------------------------
			$duplicate = false;
			foreach($this->members[$perm['module']] as $cmp) {
				if ('p' == $cmp['type']) {
					if ( 
						($cmp['module'] == $perm['module']) && 
						($cmp['model'] == $perm['model']) &&
						($cmp['permission'] == $perm['permission']) 
					) { $duplicate = true; }
				} else {
					if (false == array_key_exists('condition', $cmp)) { $cmp['condition'] = ''; }
					if (false == array_key_exists('condition', $perm)) { $perm['condition'] = ''; }
					if ( 
						($cmp['module'] == $perm['module']) && 
						($cmp['model'] == $perm['model']) &&
						($cmp['permission'] == $perm['permission']) &&
						($cmp['condition'] == $perm['condition'])  
					) { $duplicate = true; }
				}
			}

			if (false == $duplicate) { $this->members[$perm['module']][] = $perm; }
		  }
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize to string in own format
	//----------------------------------------------------------------------------------------------
	//returns: permissions serialized as a string [string]

	function collapse() {
		$txt = '';
		if (false == is_array($this->members)) { return $txt; }

		foreach($this->members as $moduleName => $modPerms) {
			foreach($modPerms as $p) {
				$p['model'] = strtolower($p['model']);
				$txt .= $p['type'] . '|' . $p['module'] . '|' . $p['model'] . '|' . $p['permission'];
				if ('c' == $p['type']) { $txt .= "|(if)|" . $p['condition']; }
				$txt .= "\n";
			}
		}

		return $txt;
	}

	//----------------------------------------------------------------------------------------------
	//.	alias of collapsePermissions
	//----------------------------------------------------------------------------------------------
	//returns: permissions serialized as a string [string]

	function toString() {
		return $this->collapse();
	}

	//----------------------------------------------------------------------------------------------
	//.	add a permission
	//----------------------------------------------------------------------------------------------
	//arg: type - type of permission (p|c) [string]
	//arg: module - name of a module [string]
	//arg: model - type of object to which permission applies [string]
	//arg: permission - name of an action users may take [string]
	//opt: condition - a relationship, if conditional [string]
	//returns: true if permission was added, false if not [bool]

	function add($module, $model, $permission, $condition = '') {
		global $kapenta;
		global $session;

		$type = 'p';
		if ('' != $condition) { $type = 'c'; }

		if (false == $kapenta->moduleExists($module)) { 
			$session->msgAdmin("Users_Permissions::add(), no such module: $module <br/>\n", 'warn');
			return false; 
		}

		if (true == $this->has($type, $module, $model, $permission, $condition)) { return true; }

		$perm = array(
			'type' => strtolower($type), 
			'module' => strtolower($module),
			'model' => strtolower($model),
			'permission' => strtolower($permission)
		);

		if ('c' == $type) { $perm['condition'] = $condition; }

		if (false == array_key_exists($module, $this->members)) {
			$this->members[$module] = array();
		}

		$this->members[$module][] = $perm;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	remove a permission
	//----------------------------------------------------------------------------------------------
	//arg: module - name of a module [string]
	//arg: model - type of object to which permission applies [string]
	//arg: permission - name of an action users may take [string]
	//returns: true on success, false on failure [bool]

	function remove($module, $model, $permission) {
		$found = false;
		$new = array();

		echo "revoking permission: $module - $model - $permission <br/>";

		if (false == array_key_exists($module, $this->members)) { return false; }
		foreach($this->members[$module] as $perm) {
			if (($perm['model'] == $model) && ($perm['permission'] == $permission)) {
				$found = true;
				echo "found<br/>";
			} else {
				$new[] = $perm;
			}
		}	

		$this->members[$module] = $new;

		return $found;
	}

	//----------------------------------------------------------------------------------------------
	//.	add a permission
	//----------------------------------------------------------------------------------------------
	//arg: type - type of permission (p|c) [string]
	//arg: module - name of a module [string]
	//arg: model - type of object to which permission applies [string]
	//arg: permission - name of an action users may take [string]
	//opt: condition - a relationship, if conditional [string]
	//returns: true if permission was added, false if not [bool]

	function has($type, $module, $model, $permission, $condition = '') {
		if (false == is_array($this->members)) { return false; }
		foreach($this->members as $modName => $modPerms) {
			if ($modName == $module) {
				foreach($modPerms as $p) {
					if (false == array_key_exists('condition', $p)) { $p['condition'] = ''; }
					if (
						($p['type'] == $type) &&
						($p['model'] == strtolower($model))	&&
						($p['permission'] == strtolower($permission)) &&
						($p['condition'] == strtolower($condition))
					) { return true; }
				}
			}
		}
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	temp, for debugging, make this a block
	//----------------------------------------------------------------------------------------------

	function toHtml() {
		global $theme;
		global $user;
		$html = '';				//%	return value [string]

		$table = array();
		$table[] = array('Type', 'Module', 'Model', 'Permission', 'Condition');
		foreach($this->members as $modName => $modPerms) {
			foreach($modPerms as $p) {
				$condition = '';
				if ('c' == $p['type']) { $condition = $p['condition']; }
				$row = array(
					$p['type'],
					$p['module'],
					$p['model'],
					$p['permission'],
					$condition
				);
				$table[] = $row;
			}
		}
		$html .= "<h2>Role: " . $user->getName() . " (" . $user->role . ")</h2>\n";
		$html .= $theme->arrayToHtmlTable($table, true, true);
		if ('admin' == $user->role) { 	$html .= "(admin user passes all auth checks)<br/>"; }
		return $html;
	}

}

?>
