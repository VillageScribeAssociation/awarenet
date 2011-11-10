<? /*
<form name='editSection' method='POST' action='%%serverPath%%projects/savesection/' target='_parent'>
<input type='hidden' name='action' value='saveSection' />
<input type='hidden' name='UID' value='%%sectionUID%%' />

<h2>%%title%%</h2>

<table noborder>
  <tr>
    <td><b>Title:</b></td>
	<td><input type='text' name='title' value='%%title%%' size='50' style='width: 100%;'/></td>
  </tr>
</table>
<div class='HyperTextArea64' title='content' width='100%' height='400'>
%%content64%%
</div>
<script language='Javascript'> khta.convertDivs(); </script>

<table noborder>
  <tr>
    <td valign='top'>
     <input type='submit' value='Save Changes' />
     </form>
    </td>
    <td>
    <form name='cancelEdit' method='GET' action='%%editUrl%%'>
    <input type='submit' value='Cancel' />
    </form>
    </td>
    <td>
    <form name='deleteSection' method='GET' 
          action='%%serverPath%%projects/confirmdeletesection/UID_%%projectUID%%/section_%%sectionUID%%/' >
      <input type='submit' value='Delete This Section' />
    <form>
    </td>
 </tr>
</table>
<br/>

[[:theme::navtitlebox::label=Images::toggle=divEditImages:]]
<div id='divEditImages' style='visibility: hidden; display: none;'>
[[:images::uploadmultiple::refModule=projects::refModel=projects_project::refUID=%%projectUID%%:]]
</div>
<br/>
*/ ?>
