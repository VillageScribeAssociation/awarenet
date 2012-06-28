<? /*

<form name='editThread%%UIDJsClean%%' method='POST' action='%%serverPath%%forums/savethread/'>
    <input type='hidden' name='action' value='saveThread' />
    <input type='hidden' name='UID' value='%%UID%%' />
    <table noborder='noboder'>
    <tr>
        <td><b>Title:</b> </td>
        <td><input type='text' name='title' value='%%title%%' size='50' /></td>
    </tr>
    </table>
<div class='HyperTextArea64' title='content' width='100%' height='400' refModule='forums' refModel='forums_thread' refUID='%%UID%%'>
%%content64%%
</div>
<script language='Javascript'> khta.convertDivs(); </script>
</form>
<table noborder>
  <tr>
    <td>
		<input 
			type='button' 
			value='Save' 
			onClick='khta.updateAllAreas(); document.editThread%%UIDJsClean%%.submit();'
		>
	</td>
    <td>
      <form name='cancelThread%%UIDJsClean%%' method='GET' action='%%serverPath%%forums/showthread/%%UID%%'>
        <input type='hidden' name='action' value='deleteThread' />
        <input type='hidden' name='UID' value='%%UID%%' />
        <input type='submit' value='Cancel' />
      </form>
    </td>
    <td>
      <form name='cancelThread%%UIDJsClean%%' method='POST' action='%%serverPath%%forums/confirmdeletethread/'>
        <input type='hidden' name='action' value='deleteThread' />
        <input type='hidden' name='UID' value='%%UID%%' />
        <input type='submit' value='Delete' />
      </form>
    </td>
  </tr>
</table>


*/ ?>
