<? /*
<form name='editforums' method='POST' action='%%serverPath%%forums/save/'>
<input type='hidden' name='action' value='saveRecord' />
<input type='hidden' name='UID' value='%%UID%%' />

<table noborder>
  <tr>
    <td><b>Title:</b></td>
    <td><input type='text' name='title' value='%%forumTitle%%' size='50' /></td>
  </tr>
  <tr>
    <td><b>School:</b></td>
    <td>
		[[:schools::select::varname=school::default=%%school%%:]] &nbsp;
	    <b>Weight:</b>
	    <input type='text' name='weight' value='%%weight%%' size='4' />
	</td>
  </tr>
  <tr>
  </tr>
</table>
<br/>
<b>Description:</b> <small>what is this forum about?</small><br/>
<div class='HyperTextArea64' title='description' width='100%' height='400'>
%%description64%%
</div>
<script language='Javascript'> khta.convertDivs(); </script>

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
