<? /*

<form name='editSubscription%%UID%%' method='POST' action='%%serverPath%%newsletter/savesubscription/'>
    <input type='hidden' name='action' value='saveSubscription' />
    <input type='hidden' name='UID' value='%%UID%%' />
	<table noborder='noboder' width='100%'>
    <tr>
        <td><b>email address</b></td>
        <td><input type='text' name='email' value='%%email%%' style='width: 100%;' /></td>
    </tr>
    <tr>
        <td><b>status</b></td>
        <td>
			[[:newsletter::selectsubscriptionstatus::status=%%status%%:]]
		</td>
    </tr>
    </table>
</form>
<table noborder>
  <tr>
    <td><input type='button' value='Save' onClick='document.editSubscription%%UID%%.submit()'></td>
    <td><input type='button' value='Cancel' onClick='kwnd.closeWindow();' /></td>
    <td>
      <form name='cancelSubscription%%UID%%' method='POST' action='%%serverPath%%newsletter/deletesubscription/'>
        <input type='hidden' name='action' value='deleteSubscription' />
        <input type='hidden' name='UID' value='%%UID%%' />
        <input type='submit' value='Delete' />
      </form>
    </td>
  </tr>
</table>


*/ ?>
