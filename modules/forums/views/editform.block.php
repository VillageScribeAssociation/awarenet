<? /*
<form name='editforums' method='POST' action='/forums/save/'>
<input type='hidden' name='action' value='saveRecord' />
<input type='hidden' name='UID' value='%%UID%%' />

<table noborder>
  <tr>
    <td><b>Title:</b></td>
    <td><input type='text' name='title' value='%%forumTitle%%' size='50' /></td>
  </tr>
</table>
<br/>
<b>Description:</b> <small>what is this forum about?</small><br/>
%%descriptionJs64%%
[[:editor::base64::jsvar=descriptionJs64::name=description:]]
<br/>

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
