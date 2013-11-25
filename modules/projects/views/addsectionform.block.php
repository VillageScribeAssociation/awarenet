<? /*

<div id='divProjectAddSection' class='block'>
[[:theme::navtitlebox::label=Add Section::toggle=divAddSectionForm::hidden=yes:]]
<div id='divAddSectionForm' style='visibility: hidden; display: none;'>
<form name='addProjectSection' method='POST' action='%%serverPath%%projects/addsection/'>
<input type='hidden' name='action' value='addSection' />
<input type='hidden' name='UID' value='%%UID%%' />
<input type='hidden' name='projectUID' value='%%UID%%' />

<table noborder width='100%'>
  <tr>
    <td width='50px'><b>Title: </b></td>
	<td><input type='text' name='title' value='' size='20' style='width: 100%;' /></td>
  </tr>
</table>
[[:editor::add::name=content::refModule=projects::refModel=projects_project::refUID=%%UID%%::height=200:]]
<input type='submit' value='Add Section >>' />
</form>
</div>
</div>

*/ ?>
