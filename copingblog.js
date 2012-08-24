function display_visi()
{
	document.getElementById("post-visibility-select").style.display = "block"

	var x = document.getElementById("post_password")
	if (x.value != "")
	{
		x.style.border = "solid red 2px"
		document.getElementById("password-span").style.color = "red"
		document.getElementById("post_password").style.color = "red"
		document.getElementById("post_password").style.fontWeight = "bold"
		
	}

}

//window.onload = display_visi

jQuery(document).ready(function() {
	
	display_visi()
	
	//collaps admin menu on the left side
	jQuery(document.body).addClass("folded");
	setUserSetting("mfold","f")

/*    
	//experimental: ponter box to indicate password field
	jQuery('#post_password').pointer({
        content: '<h3>Passwort</h3><p>Hier wird das Passwort gesetzt</p>',
        position: {
					my: 'right right',
					at: 'left right',
					offset: '0 0'
				},
        close: function() {
            // Once the close button is hit
        }
      }).pointer('open');
*/ 


});

