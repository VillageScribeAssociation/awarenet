<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list project members for the nav (300 px wide)
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or projects entry [string]
//opt: editmode - set to yes to show 'remove member' links, js for ajax version (yes|no|js) string]
//opt: projectUID - overrides raUID [string]

function projects_listmembersnav($args) {
	global $kapenta;
	global $kapenta;
	global $cache;
	global $theme;

	$editmode = 'no';		//%	for editing membership (yes|no|js) [string]
	$isAdmin = false;		//%	set to true if current user is project admin [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check view cache for this block and context
	//----------------------------------------------------------------------------------------------

	$html = $cache->get($args['area'], $args['rawblock']);
	if ('' != $html) { return $html; }

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	if ('public' == $kapenta->user->role) { return '[[:users::pleaselogin:]]'; }
	if (true == array_key_exists('editmode', $args)) { $editmode = $args['editmode']; }
	if (true == array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (false == array_key_exists('raUID', $args)) { return false; }

	$model = new Projects_Project($args['raUID']);

	if (false == $kapenta->user->authHas('projects', 'projects_project', 'show', $model->UID)) { 
		return ''; 
	}

	if (true == $kapenta->user->authHas('projects', 'projects_project', 'editmembers', $model->UID)) { 
		$isAdmin = true; 
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$members = $model->memberships->getMembers();

	foreach($members as $userUID => $urole) {
		$block = '[[:users::summarynav'
			 . '::userUID=' . $userUID
			 . '::extra=(' . $urole . ")"
			 . '::target=_parent:]]';

		//--------------------------------------------------------------------------------------
		//	basic HTML option for removing members
		//--------------------------------------------------------------------------------------
		if ((true == $isAdmin) && ('yes' == $editmode)) {
			$rmUrl = ''
			 . $kapenta->serverPath . "projects/editmembers/"
			 . "removemember_" . $userUID . "/"
			 . $model->UID;

			$html .= "<a href='" . $rmUrl . "'>[ remove member &gt;&gt; ]</a><br/>";
		}

		//--------------------------------------------------------------------------------------
		//	call to AJAX client for removing members
		//--------------------------------------------------------------------------------------
		if ((true == $isAdmin) && ('js' == $editmode)) {
			//TODO: tidy this, perhaps make this a block
			$imgUrl = ''
			 . $kapenta->serverPath . 'themes/'
			 . $kapenta->defaultTheme . '/images/icons/arrow_x_green.png';

			$html .= "
				<table noborder>
					<tr>
						<td valign='top'>
							<a 
								href='javascript:void(0);'
								onClick=\"memberConsole.removeMember('" . $userUID . "');\" 
								title='Remove member.'
							><img src='$imgUrl' /></a>
						</td>
						<td valign='top'>
							$block
							<div id='divMemberStatus" . $userUID . "'></div>
						</td>
					</tr>
				</table>\n";
		}

		//------------------------------------------------------------------------------------------
		//	display memberships only
		//------------------------------------------------------------------------------------------
		if (('' == $editmode) || ('no' == $editmode)) {
			$html .= $block . "\n";
		}

	}

	//----------------------------------------------------------------------------------------------
	//	save to cache
	//----------------------------------------------------------------------------------------------
	$html = $theme->ntb($html, 'Members', 'divAllProjectMembers', 'show');
	$html = $theme->expandBlocks($html, $args['area']);
	$cache->set('projects-membersnav-' . $model->UID, $args['area'], $args['rawblock'], $html);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

