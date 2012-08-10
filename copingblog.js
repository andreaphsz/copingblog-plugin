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

window.onload = display_visi
