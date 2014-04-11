jQuery(document).ready(function() {
	
	var display_visi = function () {
		
		var visi_sel = document.getElementById("post-visibility-select")
			
		if ( visi_sel !== null) {
		
			var x = document.getElementById("post_password")
	
			visi_sel.style.display = "block"
			
			if (x.value != "") {
				x.style.border = "solid red 2px"
				document.getElementById("password-span").style.color = "red"
				document.getElementById("post_password").style.color = "red"
				document.getElementById("post_password").style.fontWeight = "bold"	
			}
		}
	}

	display_visi()
	
	//collaps admin menu on the left side
	//jQuery(document.body).addClass("folded");
	//setUserSetting("mfold","f")

});
