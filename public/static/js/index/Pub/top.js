	function logoutFun(b) {
		var tips = document.getElementById('pub_top').getAttribute('tips');
        $.ajax({
        type:"post",
        url:"/home/Pub/logout",
        dataType:"json",
        //data:"dbId="+dbId,
        success:function(jsonData){
        	//console.log(jsonData);
            if(jsonData.status==1){
              location.replace('/home/pub/login');
            }else{
                $.messager.show({
    				title : tips,
    				msg : jsonData.message
    			});
            }
         }
        });
	}
	function userInfoFun() {
		$('<div/>').dialog({
			href : '/home/Pub/userInfo',
			width : 800,
			height :550,
			modal : true,
			title : '{$Think.lang.LANG_INDEXTOP_CONTROLPANEL_USER}',
			id : 'IndexTopUserInfo',
			buttons : [ {
				text : '{$Think.lang.LANG_SAVE}',
				iconCls : 'icon-filesave',
				handler : function() {
					$('#user_userInfo_form').form('submit', {
						url : '/home/Pub/changeInfo',
						success : function(result) {
							try {
								var r = $.parseJSON(result);
								if (r.status) {
									$('#IndexTopUserInfo').dialog('destroy');
								}
								$.messager.show({
									title : '{$Think.lang.LANG_TIPS}',
									msg : r.info
								});
							} catch (e) {
								$.messager.alert('{$Think.lang.LANG_TIPS}', result);
							}
						}
					});
				}
			} ],
			onClose : function() {
				$(this).dialog('destroy');
			},
			onLoad : function() {
			}
		});
	}
    function changeUPwd() {
    	var chgpass = document.getElementById('pub_top').getAttribute('chgpass');
    	var topsave = document.getElementById('pub_top').getAttribute('topsave');
    	var tips = document.getElementById('pub_top').getAttribute('tips');
		$('<div/>').dialog({
			href : '/home/Pub/password',
			width : 300,
			height :180,
			modal : true,
            iconCls : 'icon-key',
			title : chgpass,
			id : 'IndexTopPwd',
			buttons : [ {
				text : topsave,
				iconCls : 'icon-filesave',
				handler : function() {
					$('#userChgPwdForm').form('submit', {
						url : '/home/Pub/changePwd',
						success : function(result) {
							try {
								
								var r = $.parseJSON(result);
								//alert(r);
								if (r.status) {
									$('#IndexTopPwd').dialog('destroy');
								}
								$.messager.show({
									title : tips,
									msg : r.message
								});
							} catch (e) {
								$.messager.alert(tips, result);
							}
						}
					});
				}
			} ],
			onClose : function() {
				$(this).dialog('destroy');
			},
			onLoad : function() {
			}
		});
	}
	function sysInfoFun() {
		var jqver = jQuery.fn.jquery;
		var jeauiver; //= $.easyui.getVersion();
		$('<div/>').dialog({
			href : '/home/Pub/main?jqver='+jqver+'&jeauiver='+jeauiver,
			width : 490,
			height :380,
			modal : true,
			title : '{$Think.lang.LANG_INDEXTOP_LOGOUT_ABOUT}',
			onClose : function() {
				$(this).dialog('destroy');
			},
			onLoad : function() {
			}
		});
	}
    function changeLoginUser() {
    	var swuser = document.getElementById('pub_top').getAttribute('swuser');
		var tips = document.getElementById('pub_top').getAttribute('tips');
		var btn = document.getElementById('pub_top').getAttribute('btn');
		$('<div/>').dialog({
			href : '/home/Pub/login_dialog/flag/1',
			width : 350,
			height :250,
			modal : true,
			title : swuser,
			id : 'IndexTopLoginDia',
            iconCls:'icon-user_go',
			buttons : [ {
				text : btn,
				id : 'login_dialog_sub',
				iconCls : 'icon-user_go',
				handler : function() {
					$('#user_userInfo_form').form('submit', {
						url : '/home/Pub/checkLogin',
						success : function(result) {
							try {
								var r = $.parseJSON(result);
								if (r.status) {
									$('#IndexTopLoginDia').dialog('destroy');
                                    location.replace('/home/Index/index');
								}
								$.messager.show({
									title : tips,
									msg : r.info
								});
							} catch (e) {
								$.messager.alert('{$Think.lang.LANG_TIPS}', result);
							}
						}
					});
				}
			} ],
			onClose : function() {
				$(this).dialog('destroy');
			},
			onLoad : function() {
			}
		});
	}
    function timeoutLoginUser() {
		$('<div/>').dialog({
			href : '/home/Pub/login_dialog/flag/2',
			width : 350,
			height :250,
			modal : true,
			title : '{$Think.lang._LOGIN_TIMEOUT_ERROR_}',
            iconCls:'icon-user_go',
			id : 'IndexTopLoginTimeDia',
			buttons : [ {
				text : '{$Think.lang.LANG_LOGIN_BTN_LOGIN}',
				id : 'login_timeout_sub',
				iconCls : 'icon-user_go',
				handler : function() {
					$('#user_userInfo_form').form('submit', {
						url : '/home/Pub/checkLogin',
						success : function(result) {
							try {
								var r = $.parseJSON(result);
								if (r.status) {
									$('#IndexTopLoginTimeDia').dialog('destroy');
                                    location.replace('__APP__/Index/index');
								}
								$.messager.show({
									title : '{$Think.lang.LANG_TIPS}',
									msg : r.info
								});
							} catch (e) {
								$.messager.alert('{$Think.lang.LANG_TIPS}', result);
							}
						}
					});
				}
			} ],
			onClose : function() {
				$(this).dialog('destroy');
			},
			onLoad : function() {
			}
		});
	}
	function changeMenu(gId,gName){
		//console.log(gId,gName);
		
        $.ajax({
        type:"post",
        url:"/home/Pub/chageNavGroup",
        dataType:"json",
        data:"navId="+gId,
        success:function(jsonData){
            if(jsonData.status==1){
              $("#menuTreeTitle").panel('setTitle',gName);
			
              $('#layout_west_tree').tree('reload');
            }else{
              alert(jsonData.info);
            }
        }
        });
	}
	function north_addTab(opts,title){
		layout_center_addTabFun({
			title : title,
			closable : true,
			content : '<iframe src="'+opts+'" frameborder="0" style="border:0;width:100%;height:99%;"></iframe>'
		});
	}
    function changeDbConn(dbId){
        $.ajax({
        type:"post",
        url:"/home/Pub/changeDB",
        dataType:"json",
        data:"dbId="+dbId,
        success:function(jsonData){
            if(jsonData.status==1){
              location.replace('/home/Index/index');
            }else{
               $.messager.show({
				title : '{$Think.lang.LANG_TIPS}',
				msg : jsonData.info
			  });
            }
        }
    });
    }
    function systemLockFun() {
    	var logout_lock = document.getElementById('pub_top').getAttribute('logout_lock');
    	var unlock = document.getElementById('pub_top').getAttribute('unlock');
    	var tips = document.getElementById('pub_top').getAttribute('tips');
        $('<div/>').dialog({
			href : '/home/Pub/cancelLock',
			width : 350,
			height :250,
			modal : true,
			title : logout_lock,
            iconCls:'icon-lock',
            closable: false,
            bodyStyle: {overflow: 'hidden'},
			id : 'IndexTopLockDia',
			buttons : [ {
				text : unlock,
				id : 'cancelLock_sub',
				iconCls : 'icon-user_go',
				handler: function() {
                    var pwd = $.trim($('#cancelLockSystemPassword').val());
                    $.ajax({
                    type:"post",
                    url:"/home/Pub/doCancelLogin",
                    dataType:"json",
                    data:"pwd="+pwd,
                    success:function(jsonData){
                    	//colsole.log(jsonData);
                        if(jsonData.status==1){
                            $('#IndexTopLockDia').dialog('destroy');
                            $.messager.show({
									title : tips,
									msg : jsonData.message
				            });
                        }else{
                            $.messager.alert(tips, jsonData.message);
                        }
                    }
                    });
                }
			} ]
		});
    }