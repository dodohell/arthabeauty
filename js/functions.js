
function checkEmailAddress(email){
	var pattern=/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_.-])+\.([a-zA-Z])*([a-zA-Z])/;
	 if(pattern.test(email)){         
		return true;
    }else{   
		return false;
    }
}