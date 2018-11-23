<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:62:"D:\xampp\htdocs\TMES\public/../app/home\view\Tcuser\index.html";i:1541469411;}*/ ?>
<script type="text/javascript">
	$(function(){
            var dg = $('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid();
            dg.datagrid('removeFilterRule');
			dg.datagrid('enableFilter', [{
				field:'login_count',
				type:'numberbox',
				options:{precision:0},
				op:['equal','notequal','less','greater']
			},{
				field:'u_account',
				type:'text'
			},{
				field:'user_title',
				type:'text'
			},{
				field:'user_sex',
				type:'combobox',
				options:{
					panelHeight:'auto',
					url : '<?php echo url('XxgetOption'); ?>',
					queryParams : { cusCode:'sex',filter:1 },
					valueField:'id',
					textField:'text',
					onChange:function(value){
						if (value == ''){
							dg.datagrid('removeFilterRule', 'user_sex');
						} else {
							dg.datagrid('addFilterRule', {
								field: 'user_sex',
								op: 'equal',
								value: value
							});
						}
						dg.datagrid('doFilter');
					}
				}
			},{
				field:'user_email',
				type:'text'
			},{
				field:'user_phone',
				type:'text'
			},{
				field:'user__mobile',
				type:'text'
			},{
				field:'dept_name',
				type:'text'
			},{
				field:'last_login_time',
				type:'datebox',
                op:['less','lessorequal','greater','greaterorequal']
			},{
				field:'status_name',
				type:'combobox',
				options:{
					panelHeight:'auto',
					url : '<?php echo url('XxgetOption'); ?>',
					queryParams : { cusCode:'status',filter:1 },
					valueField:'id',
					textField:'text',
					onChange:function(value){
						if (value == ''){
							dg.datagrid('removeFilterRule', 'status');
						} else {
							dg.datagrid('addFilterRule', {
								field: 'status',
								op: 'equal',
								value: value
							});
						}
						dg.datagrid('doFilter');
					}
				}
			}]);
   });
   function formatSex(value, row, index) {
		if(value==1){
			return '<span title="'+value+'">男</span>';
		}else if(value==0){
			return '<span title="'+value+'">女</span>';
		}
  }
</script>
<div id="<?php echo \think\Request::instance()->controller(); ?>PagePanel" class="easyui-layout" data-options="fit : true,border : false">
	<form id="<?php echo \think\Request::instance()->controller(); ?>SearchForm" onsubmit="$('#<?php echo \think\Request::instance()->controller(); ?>Search').click();return false;">
	<div data-options="region:'center',border:false">
		<table id="<?php echo \think\Request::instance()->controller(); ?>DataGrid"
		 data-options="url : '<?php echo url('XxGetTableJson'); ?>',
			fit : true,
			border : false,
            rownumbers:true,
			pagination : true,
			idField : 'id',
			pageSize :20,
			pageList : dg_pageList,
			sortName : 'u_account',
			sortOrder : 'desc',
			checkOnSelect : true,
			selectOnCheck : true,
			nowrap : false,
            striped:true,
			singleSelect:true,
            filterBtnIconCls:'icon-filter',
			onBeforeLoad:function(param){
				$(this).datagrid('clearChecked').datagrid('clearSelections');
			},
			toolbar : [ <?php echo $toolBar; ?> ]
			">
		 <thead data-options="frozen:true">
		    <tr>
				<th data-options="field:'id',width:30,checkbox:true" sortable="true"><?php echo \think\Lang::get('LANG_USER_ID'); ?></th>
                <th data-options="field:'u_account',width:80" sortable="true"><?php echo \think\Lang::get('LANG_USER_U_ACCOUNT'); ?></th>
                <th data-options="field:'user_title',width:120" sortable="true"><?php echo \think\Lang::get('LANG_USER_USER_TITLE'); ?></th>
				<th data-options="field:'emp_no',width:70" sortable="true"><?php echo \think\Lang::get('LANG_USER_USER_NO'); ?></th>
			</tr>
		</thead>
		<thead>
            <tr>
                <th data-options="field:'user_sex',width:50,align: 'center',formatter:formatSex" sortable="true"><?php echo \think\Lang::get('LANG_USER_USER_SEX'); ?></th>
                <th data-options="field:'dept_name',width:300" sortable="true"><?php echo \think\Lang::get('LANG_USER_USER_DEPT'); ?></th>
				<th data-options="field:'manger_code',width:100" sortable="true"><?php echo \think\Lang::get('LANG_USER_USER_MAG'); ?></th>
                <th data-options="field:'post_code',width:100" sortable="true"><?php echo \think\Lang::get('LANG_USER_USER_POST'); ?></th>
				<th data-options="field:'user_email',width:200" sortable="true"><?php echo \think\Lang::get('LANG_USER_USER_EMAIL'); ?></th>
                <th data-options="field:'user_phone',width:60" sortable="true"><?php echo \think\Lang::get('LANG_USER_USER_PHONE'); ?></th>
                <th data-options="field:'user_mobile',width:100" sortable="true"><?php echo \think\Lang::get('LANG_USER_USER_MOBILE'); ?></th>
				<th data-options="field:'online_status',width:60,align: 'center'" sortable="true"><?php echo \think\Lang::get('LANG_USER_USER_ONLINE'); ?></th>
                <th data-options="field:'login_count',width:60,align: 'center'" sortable="true"><?php echo \think\Lang::get('LANG_USER_USER_LOGINTIMES'); ?></th>
                <th data-options="field:'last_login_time',width:150" sortable="true"><?php echo \think\Lang::get('LANG_USER_USER_LASTLOGIN'); ?></th>
                <th data-options="field:'bu_name',width:50,align: 'center'" sortable="true"><?php echo \think\Lang::get('LANG_USER_USER_BUNAME'); ?></th>
                <th data-options="field:'lang_sets',width:50,align: 'center'" sortable="true"><?php echo \think\Lang::get('LANG_USER_USER_LANGSETS'); ?></th>
                <th data-options="field:'last_login_ip',width:80" sortable="true"><?php echo \think\Lang::get('LANG_USER_USER_LTLNAREA'); ?></th>
                <th data-options="field:'update_time',width:140" sortable="true"><?php echo \think\Lang::get('LANG_UPDATE_TIME'); ?></th>
                <th data-options="field:'status_name',width:60,align: 'center',formatter:formatStatus" sortable="true"><?php echo \think\Lang::get('LANG_STATUS'); ?></th>
                <th data-options="field:'remark',width:150"><?php echo \think\Lang::get('LANG_REMARK'); ?></th>
            </tr>
          </thead>
		</table>
	</div>
 </form>
	<div id="<?php echo \think\Request::instance()->controller(); ?>DiaLog"></div>
</div>
<script type="text/javascript">
	function <?php echo \think\Request::instance()->controller(); ?>SearchFun() {
		$('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid('load', serializeObject($('#<?php echo \think\Request::instance()->controller(); ?>SearchForm')));
	}
	function <?php echo \think\Request::instance()->controller(); ?>CleanFun() {
		$('#<?php echo \think\Request::instance()->controller(); ?>SearchForm input').val('');
		$('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid('load', {});
	}


	// 編輯
	function <?php echo \think\Request::instance()->controller(); ?>EditFun(){
		if(isChecked('#<?php echo \think\Request::instance()->controller(); ?>DataGrid', 0)){
			var rows= $('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid('getChecked');
			new DatagridMethod().show('edit', '550px', '450px','<?php echo \think\Lang::get('LANG_EDIT'); ?>-'+rows[0].user_title).opt({},"#<?php echo \think\Request::instance()->controller(); ?>EditForm");
		}
	}
	//新增
	function <?php echo \think\Request::instance()->controller(); ?>AddFun() {
		new DatagridMethod().show('add', '', '', '<?php echo \think\Lang::get('LANG_ADD'); ?>');
	}
	function <?php echo \think\Request::instance()->controller(); ?>DeleteFun() {
		new DatagridMethod().del();
	}
	function <?php echo \think\Request::instance()->controller(); ?>EditPwdFun(){
		if(isChecked('#<?php echo \think\Request::instance()->controller(); ?>DataGrid', 0)){
			var rows= $('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid('getChecked');
			new DatagridMethod().show('editPwd', '350px', '350px','<?php echo \think\Lang::get('LANG_INDEXTOP_CONTROLPANEL_PASS'); ?>-'+rows[0].user_title,'1').opt({},"#<?php echo \think\Request::instance()->controller(); ?>EditPwdForm");
		}
	}

    

	function <?php echo \think\Request::instance()->controller(); ?>PrvlgSetFun(){
		var rows = $('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid('getChecked');
		if(rows.length == 0){
			$.messager.show({
				title:'<?php echo \think\Lang::get('LANG_TIPS'); ?>',
				msg:'<?php echo \think\Lang::get('LANG_CHECK_CONFIRM'); ?>'
			});
		}else if(rows.length > 1){
			 $('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid('clearChecked').datagrid('clearSelections');
				$.messager.show({
					title: '<?php echo \think\Lang::get('LANG_TIPS'); ?>',
					msg: '<?php echo \think\Lang::get('LANG_EDIT_CONFIRM'); ?>'
				});

		}else{
			$('<div/>').dialog({
				href : '<?php echo url('prvlgSet'); ?>',
				queryParams : { id:rows[0].id,empId:rows[0].u_account },
				width : 900,
				height : 580,
				modal : true,
				//maximizable:true,
				//maximized:true,
				title : '<?php echo \think\Lang::get('LANG_APPOINT'); ?>:'+rows[0].u_account+':'+rows[0].user_title,
				id : '<?php echo \think\Request::instance()->controller(); ?>PrvlgDia',
				buttons : [ {
					text : '<?php echo \think\Lang::get('LANG_SAVE'); ?>',
					id : '<?php echo \think\Request::instance()->controller(); ?>ApSave',
					iconCls : 'icon-filesave',
					handler : function() {
						//var vTitle = $('#<?php echo \think\Request::instance()->controller(); ?>PrvlgSet').tabs('getSelected').panel('options').title;
						var index =  $('#<?php echo \think\Request::instance()->controller(); ?>PrvlgSet').tabs('getTabIndex', $('#<?php echo \think\Request::instance()->controller(); ?>PrvlgSet').tabs('getSelected'));
						var j_action,j_chkIds,j_flag,j_grid,j_list,j_firstDB;//alert(index);
						switch(index){
							case 0://设置接入点
								j_action = "XxSetBu";
								var j_selectDB=[];
								$('input:checkbox:checked[name="selectDB[]"]').each(function(){
								   j_selectDB.push($(this).val());
								   //alert($(this).val());
								});
								j_chkIds = j_selectDB;
								//默認
								var j_firstDB = $('input:radio:checked[name="firstDB"]').val();
								j_grid = 0;
								j_list = 0;
								break;
							case 1://删除角色
								j_action="XxCancelRole";
								var roleData = $('#<?php echo \think\Request::instance()->controller(); ?>RoleList').datagrid('getChecked');//全部
								var roleArr = [];
								for ( var i = 0; i < roleData.length; i++) {
									roleArr.push(roleData[i].id);
								}
								j_chkIds = roleArr;
								//j_flag = 1;
								j_grid = '<?php echo \think\Request::instance()->controller(); ?>RoleGrid';
								j_list = '<?php echo \think\Request::instance()->controller(); ?>RoleList';
								break;
							case 2://设置角色

								j_action = "XxSetRole";
								var rowData  = $('#<?php echo \think\Request::instance()->controller(); ?>RoleAppoint').datagrid('getRows');//全部
								var roleArr = [];
								for ( var i = 0; i < rowData.length; i++) {
									roleArr.push(rowData[i].id);
								}
								j_chkIds= roleArr;
								//j_flag = 2;
								j_grid = '<?php echo \think\Request::instance()->controller(); ?>RoleGrid';
								j_list = '<?php echo \think\Request::instance()->controller(); ?>RoleList';
								$('#<?php echo \think\Request::instance()->controller(); ?>RoleAppoint').datagrid('loadData', { total: 0, rows: [] });
								break;
							case 3://删除群组
								j_action = "XxSetGroup";
								var groupData = $('#<?php echo \think\Request::instance()->controller(); ?>GroupList').datagrid('getChecked');//全部
								var groupArr = [];
								for ( var i = 0; i < groupData.length; i++) {
									groupArr.push(groupData[i].id);
								}
								j_chkIds = groupArr;
								j_flag = 1;
								j_grid = '<?php echo \think\Request::instance()->controller(); ?>GroupGrid';
								j_list = '<?php echo \think\Request::instance()->controller(); ?>GroupList';
								break;
							case 4://设置群组
								j_action = "XxSetGroup";
								var groupData = $('#<?php echo \think\Request::instance()->controller(); ?>GroupAppoint').datagrid('getRows');//全部
								var groupArr = [];
								for ( var i = 0; i < groupData.length; i++) {
									groupArr.push(groupData[i].id);
								}
								j_chkIds = groupArr;
								j_flag = 2;
								j_grid = '<?php echo \think\Request::instance()->controller(); ?>GroupGrid';
								j_list = '<?php echo \think\Request::instance()->controller(); ?>GroupList';
								$('#<?php echo \think\Request::instance()->controller(); ?>GroupAppoint').datagrid('loadData', { total: 0, rows: [] });
								break;
						}
						$.ajax({
								type : 'post',
								url : '<?php echo url('+j_action+'); ?>',
								data:{uId:rows[0].id,chkIds:j_chkIds,flag:j_flag,firstDB:j_firstDB},
								dataType :'json',
								success :function(res){
									if(res.status == 1){
										$.messager.show({
											title : '<?php echo \think\Lang::get('LANG_TIPS'); ?>',
											msg : res.info
										});
									}else{
										$.messager.show({
										title : '<?php echo \think\Lang::get('LANG_TIPS'); ?>',
										msg :res.info,
										});
									}
									$('#'+j_grid).datagrid('reload');
									$('#'+j_list).datagrid('reload');
									/*if(j_grid!=''){
										$('#'+j_grid).datagrid('reload');
									}
									if(j_list!=''){
										$('#'+j_list).datagrid('reload');
									}*/
								}
						});
					}
				},{
					text:'<?php echo \think\Lang::get('LANG_CLOSE'); ?>',
					iconCls:'icon-cancel',
					handler:function(){
						$('#<?php echo \think\Request::instance()->controller(); ?>PrvlgDia').dialog('destroy');
					}
				} ],
				onClose : function() {
					$(this).dialog('destroy');
				}
			});
		}
	}
</script>