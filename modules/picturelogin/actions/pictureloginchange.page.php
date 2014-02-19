<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>onecol.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - Picture Login</title>
	<content>
		<table noborder width='100%'>
			<tr>
				<td><b>User:</b></td>
				<td><b><input type='text' id='txtUser' value='%%username%%' readonly style='width: 95%;' /></b></td>
			</tr>
			<tr>
				<td width='80px'><b>Pictures</b></td>
				<td>
					<div id="source" ondragstart="drag(event)" ondrop="dropSource(event)" ondragover="allowDropSource(event)">
						[[:picturelogin::aDataIconDefinition:]]
					</div>
				</td>
			</tr>
			<tr>
				<td width='80px'><b>Generate Pictures</b></td>
				<td>
					<form onSubmit="return captureFormPics()">
						<input type='submit' id='generatePics' value='Generate Pictures' />
						[[:picturelogin::generatePicturesScript:]]
					</form>
				</td>
		 	</tr>
			<tr>
				<td width='80px'><b>Picture Password</b></td>
				<td>
					<div id="drop1" ondragstart="drag(event)" ondrop="drop(event)" ondragover="allowDrop(event)">
					</div>
					<div id="drop2" ondragstart="drag(event)" ondrop="drop(event)" ondragover="allowDrop(event)">
					</div>
					<div id="drop3" ondragstart="drag(event)" ondrop="drop(event)" ondragover="allowDrop(event)">
					</div>
					<div id="drop4" ondragstart="drag(event)" ondrop="drop(event)" ondragover="allowDrop(event)">
					</div>
					<div id="drop5" ondragstart="drag(event)" ondrop="drop(event)" ondragover="allowDrop(event)">
					</div>
					<div id="drop6" ondragstart="drag(event)" ondrop="drop(event)" ondragover="allowDrop(event)">
					</div>
					<div id="drop7" ondragstart="drag(event)" ondrop="drop(event)" ondragover="allowDrop(event)">
					</div>
					<div id="drop8" ondragstart="drag(event)" ondrop="drop(event)" ondragover="allowDrop(event)">
					</div>
					<div id="drop9" ondragstart="drag(event)" ondrop="drop(event)" ondragover="allowDrop(event)">
					</div>
					<div id="drop10" ondragstart="drag(event)" ondrop="drop(event)" ondragover="allowDrop(event)">
					</div>
					<div id="drop11" ondragstart="drag(event)" ondrop="drop(event)" ondragover="allowDrop(event)">
					</div>
					<div id="drop12" ondragstart="drag(event)" ondrop="drop(event)" ondragover="allowDrop(event)">
					</div>
					<div id="drop13" ondragstart="drag(event)" ondrop="drop(event)" ondragover="allowDrop(event)">
					</div>
					<div id="drop14" ondragstart="drag(event)" ondrop="drop(event)" ondragover="allowDrop(event)">
					</div>
					<div id="drop15" ondragstart="drag(event)" ondrop="drop(event)" ondragover="allowDrop(event)">
					</div>
					<div id="drop16" ondragstart="drag(event)" ondrop="drop(event)" ondragover="allowDrop(event)">
					</div>
					<div id="drop17" ondragstart="drag(event)" ondrop="drop(event)" ondragover="allowDrop(event)">
					</div>
					<div id="drop18" ondragstart="drag(event)" ondrop="drop(event)" ondragover="allowDrop(event)">
					</div>
					<div id="drop19" ondragstart="drag(event)" ondrop="drop(event)" ondragover="allowDrop(event)">
					</div>
					<div id="drop20" ondragstart="drag(event)" ondrop="drop(event)" ondragover="allowDrop(event)">
					</div>
				</td>
			</tr>
			<tr>
				<td width='80px'><b>Generate Password</b></td>
				<td>
					<form onSubmit="return captureForm()">
						<input type='submit' id='generate' value='Generate'/>
						[[:picturelogin::generatePasswordScript:]]
					</form>
				</td>
		 	</tr>
			<tr>
				<td width='80px'><b>Generated Password</b></td>
				<td>
					<input id='genpass' type='text' name='Generated Password' value='' size='20' style='width: 95%;' />
				</td>
			<form name='changeUserPass' method='POST' action='%%serverPath%%users/changepassword/'>
			<input type='hidden' name='action' value='changeUserPass' />
			<input type='hidden' name='UID' value='%%UID%%' />
				<tr>
					<td><b>Current Password:</b></td>
					<td><b><input type='password' id='txtPassword' name='pwdCurrent' style='width: 95%;' /></b></td>
				</tr>
				<tr>
					<td><b>New Password:</b></td>
					<td><b><input type='password' name='pwdNew' style='width: 95%;' /></b></td>
				</tr>
				<tr>
					<td><b>Confirm New Password:</b></td>
					<td><b><input type='password' name='pwdConfirm'  style='width: 95%;' /></b></td>
				</tr>
				<tr>
					<td></td>
					<td><b><input type='submit' value='Change Password' /></b></td>
				</tr>
			</form>
		</table>
	</content>
	<nav1></nav1>
	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head></head>
	<menu1></menu1>
	<menu2></menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb></breadcrumb>
</page>

*/ ?>
