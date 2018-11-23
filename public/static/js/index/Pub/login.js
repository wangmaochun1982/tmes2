     function loginUser(uname,upass){
	    var uName = uname?uname:$('#account').val();
        var uPass = upass?upass:$('#password').val();
        var pName = $('#UserPcAddr').val();
		// 驗證賬號密碼是否為空
		if(uName=="" || uPass==""){
			$.messager.alert('提示', "賬號或密碼不能為空！");
			return;
		}
        $.ajax({
            url:'/home/Pub/checkLogin',
            type:'POST',
            data : {
                account     : uName,
                password    : uPass,
                UserPcAddr  : pName
            },
            cache : false,
            dataType : 'JSON',
            success:function(res){
            	console.log(res);
			   if(res.status==1){
					window.location.replace('/home/index/index');
					//window.location=APP+'/Index'
			   }else{
			     $.messager.alert('提示', res.message);
			   }
		    }
        });
	}
   