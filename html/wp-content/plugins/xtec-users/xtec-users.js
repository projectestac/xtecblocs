function display_step_2(x)
{
	if (document.getElementById("errors")) {
		document.getElementById("errors").style.display='none';
	}
	if (document.getElementById("message")) {
			document.getElementById("message").style.display='none';
	}
	
	document.getElementById("step_2").style.display='';
	document.getElementById("username").readOnly=false;
	document.getElementById("username").value='';
	
	document.getElementById("check_user").style.display='';
	
	if(x=='xtec') {
		document.getElementById("username_info").innerHTML='<small>Introdueix el nom d\'usuari/ària de la XTEC.</small>';
	}
	else { // x == 'other'
		document.getElementById("username_info").innerHTML='<small>El nom d\'usuari/ària ha de tenir un mínim de 9 caràcters.</small>';
	}
	
	if (document.getElementById("confirm_user")) {
		document.getElementById("confirm_user").style.display='none';
	}
	if (document.getElementById("step_3_add")) {
		document.getElementById("step_3_add").style.display='none';
	}
	if (document.getElementById("step_3_create_add")) {
		document.getElementById("step_3_create_add").style.display='none';
	}
	

}