jQuery(document).ready(function() {
	
var getUrlParameters = function(parameter, staticURL, decode){
   /*
    Function: getUrlParameters
    Description: Get the value of URL parameters either from 
                 current URL or static URL
    Author: Tirumal
    URL: www.code-tricks.com
   */
   var currLocation = (staticURL.length)? staticURL : window.location.search,
       parArr = currLocation.split("?")[1].split("&"),
       returnBool = true;
   
   for(var i = 0; i < parArr.length; i++){
        parr = parArr[i].split("=");
        if(parr[0] == parameter){
            return (decode) ? decodeURIComponent(parr[1]) : parr[1];
            returnBool = true;
        }else{
            returnBool = false;            
        }
   }
   
   if(!returnBool) return false;  
}

var display_visi = function ()
{
	document.getElementById("post-visibility-select").style.display = "block"

	var x = document.getElementById("post_password")
	var y =  document.getElementById("visibility-radio-password")
	var z =  document.getElementById("visibility-radio-private")
	var visi = getUrlParameters("visi", "", true);
	var form = getUrlParameters("form", "", true);
	/*
	if (visi=="private")
	{
		z.checked=true
	}
	
	if (x.value == "" & visi=="pwd")
	{
		x.value="1234"
		y.checked=true
	}
	*/
	if (x.value != "")
	{
		x.style.border = "solid red 2px"
		document.getElementById("password-span").style.color = "red"
		document.getElementById("post_password").style.color = "red"
		document.getElementById("post_password").style.fontWeight = "bold"
		
	}
	
	if (form == "reflex")
	{
		//tinymce.activeEditor.setContent("<h2>Reflexion</h2>")
	}
	
	
}

jQuery("#a_ps1").click(function(){
  jQuery("#ps1").toggle();
});
jQuery("#a_ps2").click(function(){
  jQuery("#ps2").toggle();
});

jQuery("#neu_ps1").click(function(){
	var lastid=jQuery("#ps1 div").last().attr('id')
	var j=1
	//var i=2+1
	var i= 1+parseInt(lastid.substring(lastid.search("_")+3))
	var ji=j+"_"+i
	//jQuery("#neu_ps1").before("<p>hallo</p>"+lastid)
	jQuery("#neu_ps1").before("<div id='ps_"+ji+"' style='border-bottom:1px solid;margin-left:20px;'><p>Datum:<input type='text1_"+ji+"'></p><p>Zielebene:</p><textarea name='text2_"+ji+"' cols='60' rows='5'></textarea><br><p>Konsequenzen:</p><textarea name='text3_"+ji+"' cols='60' rows='5'>hallo</textarea><br></div>")
})

//window.onload = display_visi


	//window.setTimeout(display_visi(), 10000);
	display_visi()
	
	//collaps admin menu on the left side
	//jQuery(document.body).addClass("folded");
	//setUserSetting("mfold","f")

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

