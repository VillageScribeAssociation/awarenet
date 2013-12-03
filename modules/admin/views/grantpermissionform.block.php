<? /*

<form name='grantPermission' method='POST' action='%%serverPath%%users/grant/'>
	<input type='hidden' name='action' value='grantPermission' />
	<input type='hidden' name='module' value='%%module%%' />
	<input type='hidden' name='model' value='%%model%%' />
	[[:users::selectrole:]]
	[[:admin::selectpermission::module=%%module%%::model=%%model%%:]] <b>if</b>
	[[:admin::selectrelationship::module=%%module%%::model=%%model%%:]]
	<input type='submit' value='Grant &gt;&gt;' />
</form>

*/ ?>
