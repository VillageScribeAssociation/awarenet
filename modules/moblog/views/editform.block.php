<? /*
<br/>
<form
	name='editPost'
	id='frmEditPost%%UID%%'
	onSubmit="khta.updateAllAreas();"
	method='POST'
	action='%%serverPath%%/moblog/save/'
>
<input type='hidden' name='action' value='saveRecord' />
<input type='hidden' name='UID' value='%%UID%%' />

<table noborder style='width: 100%;'>
  <tr>
    <td><b>Title:</b></td>
    <td width='100%'><input type='text' name='title' value='%%mbTitle%%' style='width: 100%;' /></td>
  </tr>
  <tr>
    <td><b>Published: </b></td>
    <td>
      <select name='published'>
        <option value='yes'>yes</option>
        <option value='no'>no</option>
      </select>
    </td>
  </tr>
</table>
<br/>
<b>Post Body: </b><small>(use special {fold} marker at end of introduction)</small>
<div
	class='HyperTextArea64'
	title='content'
	width='-1'
	height='400'
	style='visibility: hidden; display: none'
	refModule='moblog'
	refModel='moblog_post'
	refUID='%%UID%%'
>%%content64%%</div>

<script> khta.convertDivs(); </script>

</form>
<br/>
<table noborder>
  <tr>
   <td valign='top'>
   <input
		type='button'
		value='Save'
		onClick="$('#frmEditPost%%UID%%').submit();" />
   </td>
   <td valign='top'>
     [[:tags::editbutton::refModule=moblog::refModel=moblog_post::refUID=%%UID%%:]]
   </td>
   <td>
   <form name='cDelete' method='GET' action='%%delUrl%%'>
   <input type='submit' value='Delete' />
   </form>
   </td>
 </tr>
</table>
<br/>

<br/>
*/ ?>
