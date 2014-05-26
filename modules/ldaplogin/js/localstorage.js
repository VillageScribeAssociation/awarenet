//--------------------------------------------------------------------------------------------------
//	Code that supports local storage handling 
//--------------------------------------------------------------------------------------------------
	function supports_html5_storage() {
		try {
			return 'localStorage' in window && window['localStorage'] !== null;
		} catch (e) {
		return false;
		}
	}	

	if(supports_html5_storage()){
		var username = localStorage.getItem("username");
		var password = localStorage.getItem("password");
		if (username && password) {
			document.getElementById("ls_user").value = username;
			document.getElementById("ls_pass").value = password;
			document.getElementById("ldapLogin").submit();
		}
	} else {
		alert("this browser does not support local storage! Please use either Internet Explorer, Firefox, Opera or Chrome browser!");
	}

