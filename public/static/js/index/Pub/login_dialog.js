$("#user_userInfo_form").keypress(function(e){
	   
        var key =  e.which;
		var flag = document.getElementById('logindialog').getAttribute('flag');
        if(key == 13){
		 	if(flag==1){
				$('#login_dialog_sub').click();
			}else if(flag==2){
				$('#login_timeout_sub').click();
			}
        }
    });