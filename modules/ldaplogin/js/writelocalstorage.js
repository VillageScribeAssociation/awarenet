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
		username = document.getElementById("pwd").value;
		password = document.getElementById("uname").value;
		redirect = document.getElementById("redirect").value;

		if (username && password) {
			localStorage.setItem("username", username);
			localStorage.setItem("password", password);
			window.location.replace(redirect);
		}
	} else {
		alert("this browser does not support local storage! Please use either Internet Explorer, Firefox, Opera or Chrome browser!");
	}

