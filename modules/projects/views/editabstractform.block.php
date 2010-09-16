<? /*
<form name='editAbstract' method='POST' action='/projects/save/'>
<input type='hidden' name='action' value='saveAbstract' />
<input type='hidden' name='UID' value='%%UID%%' />

<h1>%%projectTitle%% (Abstract)</h1>

<form name='editProject' method='POST' action='%%serverPath%%projects/save/'>
<input type='hidden' name='action' value='saveChangeTitle' />
<input type='hidden' name='UID' value='%%UID%%' />

<table noborder>
  <tr>
    <td><b>Project Title:</b></td>
    <td><input type='text' name='title' value='%%projectTitle%%' size='40' /></td>
    <td><input type='submit' value='Change Title' /></td>
  </tr>
</table>
</form>
<br/>

<form name='editAbstract' method='POST' action='%%serverPath%%projects/save/'>
<input type='hidden' name='action' value='saveAbstract' />
<input type='hidden' name='UID' value='%%UID%%' />

%%abstractJs64%%
[[:editor::base64::jsvar=abstractJs64::name=abstract:]]
<br/>

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
 </tr>
</table>

<h2>Images</h2>
[[:images::uploadmultiple::refModule=projects::refModel=Projects_Project::refUID=%%UID%%:]]
<br/>UID:%%UID%%<br/>
*/ ?>
