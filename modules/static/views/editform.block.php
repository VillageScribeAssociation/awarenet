<? /*
<h1>Edit Page: %%p_title%%</h1>
<form name='editStaticPage' method='POST' action='/static/save/'>
<input type='hidden' name='action' value='saveStaticPage'>
<input type='hidden' name='UID' value='%%p_UID%%'>

<table noborder>
  <tr>
    <td valign='top'><b>title:</b></td>
    <td><input type='text' name='title' value='%%p_title%%' size='60' /></td>
  </tr>
  <tr>
    <td valign='top'><b>content:</b></td>
    <td>

<input type='hidden' id='content-edit-hidden' name='content-loader' value='%%p_contentJs%%' />
<script language='JavaScript' type='text/javascript' src='/modules/editor/HyperTextArea.js'></script>
<script language='JavaScript' type='text/javascript'>
<!--
// strip temporary markup
var $refFieldName = '';
contentJSEHR = document.getElementById('content-edit-hidden');
contentJSEHR.value = contentJSEHR.value.replace(/--squote--/g, String.fromCharCode(39));
contentJSEHR.value = contentJSEHR.value.replace(/--dquote--/g, String.fromCharCode(34));
area = new HyperTextArea('content', contentJSEHR.value, 500, 400,'/modules/editor/');
//-->
</script><br/>

    </td>
  </tr>
  <tr>
    <td valign='top'><b>menu1:</b></td>
    <td><textarea name='menu1' rows='4' cols='60'>%%p_menu1%%</textarea></td>
  </tr>
  <tr>
    <td valign='top'><b>menu2:</b></td>
    <td><textarea name='menu2' rows='4' cols='60'>%%p_menu2%%</textarea></td>
  </tr>
  <tr>
    <td valign='top'><b>nav1:</b></td>
    <td><textarea name='nav1' rows='8' cols='60'>%%p_nav1%%</textarea></td>
  </tr>
  <tr>
    <td valign='top'><b>nav2:</b></td>
    <td><textarea name='nav2' rows='8' cols='60'>%%p_nav2%%</textarea></td>
  </tr>
  <tr>
    <td valign='top'><b>script:</b></td>
    <td><textarea name='script' rows='4' cols='60'>%%p_script%%</textarea></td>
  </tr>
</table>
<small>UID: %%p_UID%% createdOn: %%p_createdOn%% createdBy: %%p_createdBy%%</small>
<input type='submit' value='Save Changes' /><br/>
</form>
*/ ?>