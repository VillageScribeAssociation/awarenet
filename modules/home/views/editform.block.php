<? /*
<h1>Edit Page: %%p_title%%</h1>
<form name='editStaticPage' method='POST' action='%%serverPath%%home/save/'>
<input type='hidden' name='action' value='saveStaticPage'>
<input type='hidden' name='UID' value='%%p_UID%%'>

<b>Title:</b> <input type='text' name='title' value='%%p_title%%' size='50' style='width: 100%;' /><br/>

<div class='HyperTextArea64' title='content' width='100%' height='400' style='visibility: hidden; display: none'>
%%p_content64%%
</div>
<script language='Javascript'> khta.convertDivs(); </script>
<input type='submit' value='Save Changes' /><br/>
<br/>

[[:theme::navtitlebox::label=Menus::toggle=divEditMenus::hidden=yes:]]
<div id='divEditMenus' style='visibility: hidden; display: none;'>
<b>Menu 1:</b> <small>(this is the primary, top level menu, usually at the top of the page)</small><br/>
<textarea name='menu1' rows='4' cols='60' style='width: 100%;'>%%p_menu1%%</textarea><br/>
<br/>
<b>Menu 2:</b> <small>(this is the secondary menu, usually context dependant)</small><br/>
<textarea name='menu2' rows='4' cols='60' style='width: 100%;'>%%p_menu2%%</textarea><br/>
<input type='submit' value='Save Changes' />
</div>
<br/>

[[:theme::navtitlebox::label=Sidebars::toggle=divEditNav::hidden=yes:]]
<div id='divEditNav' style='visibility: hidden; display: none;'>
<b>Column 1:</b> <small>(usually leftmost non-content column)</small><br/>
<textarea name='nav1' rows='8' cols='60' style='width: 100%;'>%%p_nav1%%</textarea><br/>
<br/>
<b>Column 2:</b> <small>(appears below column 1 in two column layouts)</small><br/>
<textarea name='nav2' rows='8' cols='60' style='width: 100%;'>%%p_nav2%%</textarea><br/>
<input type='submit' value='Save Changes' />
</div>
<br/>

[[:theme::navtitlebox::label=Sidebars::toggle=divEditScript::hidden=yes:]]
<div id='divEditScript' style='visibility: hidden; display: none;'>
<b>Script:</b> <small>(Javascript to run on page init)</small><br/>
<textarea name='script' rows='4' cols='60' style='width: 100%;'>%%p_script%%</textarea><br/>
<input type='submit' value='Save Changes' />
</div>
<br/>
<small>UID: %%p_UID%% createdOn: %%p_createdOn%% createdBy: %%p_createdBy%%</small>
<br/>
</form>
*/ ?>
