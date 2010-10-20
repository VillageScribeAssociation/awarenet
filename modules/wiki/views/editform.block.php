<? /*
<h1>Edit: %%title%%</h1>
<form name='editArticle' method='POST' action='%%serverPath%%wiki/save/'>
<input type='hidden' name='action' value='savePage' />
<input type='hidden' name='UID' value='%%UID%%' />

<b>Title:</b> <input type='text' name='title' value='%%title%%' size='50' /><br/>
<br/>
<b>Content:</b><br/>
<textarea name='content' rows='20' cols='75'>%%contentSafe%%</textarea><br/>
<br/>
<b>Navigation:</b><br/>
<textarea name='nav' rows='10' cols='75'>%%navSafe%%</textarea><br/>
<br/>
<b>Edit Summary:</b> <input type='text' name='reason' value='' size='50' /><br/>
<small>Briefly decribe the purpose of this edit, eg 'grammar', 'remove vandalism', 
'added links', etc</small><br/>

<table noborder>
  <tr>
   <td valign='top'>
    <input type='submit' value='save' />
    </form>
   </td>
   <td>
   <form name='cDelete' method='GET' action='%%delUrl%%'>
   <input type='submit' value='Delete' />
   </form>
   </td>
 </tr>
</table>

*/ ?>
