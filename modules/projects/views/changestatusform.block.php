<? /*

[[:theme::navtitlebox::label=Project Status::toggle=divProjectStatus::hidden=yes:]]
<div id='divProjectStatus' style='visibility: hidden; display: none;'>
<ul>
  <li><b>Open</b> projects are still ongoing and can be edited by members.</li>
  <li><b>Closed</b> projects are complete, their membership and content can't be changed.</li>
  <li><b>Locked</b> projects have been frozen by a project admin, but are not yet 
	finished and you can still ask to join.</li>
</ul>

<form name='changeStatus' method='POST' action='%%serverPath%%projects/status/'>
	<input type='hidden' name='action' value='changeStatus' />
	<input type='hidden' name='UID' value='%%UID%%' />
	%%selectStatus%%
	<input type='submit' value='Change Status >>' />
</form>
<hr/>
</div>
<br/>

*/ ?>
