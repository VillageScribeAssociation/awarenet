<? /*

[[:admin::contactinfo::tb=nav::title=Contacts:]]

<div class='block'>
	[[:theme::navtitlebox::label=Shell::toggle=divAsnShell:]]
	<div id='divAsnShell' class='navblock'>
	<input type='button' value='Launch Interactive Shell &gt;&gt;' style='width: 100%;'
		onClick="kwindowmanager.createWindow('Shell', '%%serverPath%%live/shell/', 800, 400, '%%serverPath%%modules/live/icons/console.png');" />
	</div>
	<div class='foot'></div>
</div>
<br/>

<div class='block'>
	[[:theme::navtitlebox::label=Modules::toggle=divAsnModules::hidden=yes:]]
	<div id='divAsnModules' style='visibility: hidden; display: none;'>
	<div class='spacer'></div>
	[[:admin::listmodulesnav:]]
	</div>
	<div class='foot'></div>
</div>
<br/>

<script>
	function admin_refreshDbUsage() {
		klive.removeBlock('[' + '[:admin::dbusage::refresh=yes:]]');
		klive.bindDivToBlock('divDbUse', '[' + '[:admin::dbusage::refresh=yes:]]');
		var throbberUrl = jsServerPath + 'themes/' + jsTheme + '/images/throbber-inline.gif';
		$('#divDbUse').html("<img src='" + throbberUrl + "' />");
	}
</script>

[[:admin::memcachedstats:]]

[[:cache::stats:]]

<div class='block'>
	[[:theme::navtitlebox::label=Summary::toggle=divAsnSummary:]]
	<div id='divAsnSummary'>
	<div class='spacer'></div>
	[[:admin::serversummary:]]
	</div>
	<div class='foot'></div>
</div>
<br/>

<div class='block'>
	[[:theme::navtitlebox::label=Administrators::toggle=divAdminUsers:]]
	<div id='divAdminUsers' class='navblock'>
	[[:users::administrators:]]
	</div>
	<div class='foot'></div>
</div>
<br/>

*/ ?>
