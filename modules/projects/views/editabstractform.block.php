<? /*
<h1>%%projectTitle%% (Abstract)</h1>

<form name='editProject' method='POST' action='%%serverPath%%projects/changetitle/'>
<input type='hidden' name='action' value='saveChangeTitle' />
<input type='hidden' name='UID' value='%%UID%%' />

<b>Title:</b>
<input type='text' name='title' value='%%projectTitle%%' size='30' />
<input type='submit' value='Change Title' />
</form>
<hr/>
<br/>

[[:theme::navtitlebox::label=Abstract:]]
<form name='editAbstract' method='POST' action='%%serverPath%%projects/saveabstract/'>
<input type='hidden' name='action' value='saveAbstract' />
<input type='hidden' name='UID' value='%%UID%%' />

<div class='HyperTextArea64' title='abstract' width='100%' height='400'>
%%abstract64%%
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
 </tr>
</table>
<br/>

[[:theme::navtitlebox::label=Images::toggle=divAbsImages::hidden=yes:]]
<div id='divAbsImages' style='visibility: hidden; display: none;'>
[[:images::uploadmultiple::refModule=projects::refModel=projects_project::refUID=%%UID%%:]]
<br/>
</div>
<br/>
*/ ?>
