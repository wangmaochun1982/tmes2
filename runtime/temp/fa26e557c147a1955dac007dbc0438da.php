<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:62:"D:\xampp\htdocs\TMES\public/../app/home\view\Tcnode\index.html";i:1541386321;}*/ ?>
<div id="<?php echo \think\Request::instance()->controller(); ?>PagePanel" class="easyui-layout" data-options="fit : true,border : false">
	<div data-options="title:'<?php echo \think\Lang::get('LANG_NODE_TITLE_MENUTREE'); ?>',border:false,region:'west',split:true,hideCollapsedContent:false" style="width:240px;padding:0px">
		 <ul id="<?php echo \think\Request::instance()->controller(); ?>LeftTree"></ul>
	</div>
	<div id="<?php echo \think\Request::instance()->controller(); ?>NodeChild" data-options="region:'center',border:false">
		<table id="<?php echo \think\Request::instance()->controller(); ?>DataGrid"></table>
	</div>
	<div id="<?php echo \think\Request::instance()->controller(); ?>DiaLog"></div>
</div>
<script type="text/javascript">
	$(function() {
		$('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid({
			url : '<?php echo url('XxgetTableJson'); ?>',
			fit : true,
			//fitColumns : true,
			border : false,
            rownumbers:true,
			pagination : true,
			idField : 'id',
			pageSize :20,
			pageList : dg_pageList,
			sortName : 'id',
			sortOrder : 'asc',
			checkOnSelect : true,
			selectOnCheck : true,
			nowrap : true,
            striped:true,
			singleSelect:true,
            autoRowHeight:false,
			frozenColumns : [ [ {
				field : 'id',
				title : '<?php echo \think\Lang::get('LANG_NODE_ID'); ?>',
				width : 0,
				checkbox : true
			}, {
				field : 'node_name',
				title : '<?php echo \think\Lang::get('LANG_NODE_NAME'); ?>',
				width : 0,
				sortable : true,
				formatter : function(value, row, index) {
					return '<span title="'+value+'">'+value+'</span>';
				}
			}, {
				field : 'lang_code',
				title : '<?php echo \think\Lang::get('LANG_NODE_LANGCODE'); ?>',
				width : 0,
				sortable : true,
				formatter : function(value, row, index) {
					return '<span title="'+value+'">'+value+'</span>';
				}
			}, {
				field : 'node_code',
				title : '<?php echo \think\Lang::get('LANG_NODE_CODE'); ?>',
				width : 0,
				sortable : true,
				formatter : function(value, row, index) {
					return '<span title="'+value+'">'+value+'</span>';
				}
			} ] ],
			columns : [ [{
				field : 'pname',
				title : '<?php echo \think\Lang::get('LANG_NODE_PNAME'); ?>',
				width : 0,
                sortable : true
			},{
				field : 'node_level',
				title : '<?php echo \think\Lang::get('LANG_NODE_LEVEL'); ?>',
				sortable : true,
				width : 0,
                align: 'center'
			},{
				field : 'action_lang',
				title : '<?php echo \think\Lang::get('LANG_NODE_ACTION'); ?>',
				sortable : true,
				width : 0,
                align: 'center',
                formatter: function(value,row,index){
					if (row.node_type==2){
						return '<font color=red>'+value+'</font>';
					}else{
						return '<font color=blue>'+value+'</font>';
					}
				}
			},{
				field : 'ismenu_lang',
				title : '<?php echo \think\Lang::get('LANG_NODE_ISMENU'); ?>',
				width : 0,
                align: 'center',
				formatter: function(value,row,index){
					if (row.ismenu==0){
						return '<font color=red>'+value+'</font>';
					}else{
						return value;
					}
				}
			},{
				field : 'isshow_lang',
				title : '<?php echo \think\Lang::get('LANG_NODE_ISSHOW'); ?>',
				width : 0,
                align: 'center',
				formatter: function(value,row,index){
					if (row.ismenu==0){
						return '<font color=red>'+value+'</font>';
					}else{
						return value;
					}
				}
			},  {
				field : 'node_sort',
				title : '<?php echo \think\Lang::get('LANG_NODE_SORT'); ?>',
                align: 'center'
			},  {
				field : 'node_icon',
				title : '<?php echo \think\Lang::get('LANG_NODE_ICON'); ?>',
				sortable : true,
				width : 0,
                align: 'center'
			},  {
				field : 'status',
				title : '<?php echo \think\Lang::get('LANG_STATUS'); ?>',
				sortable : true,
				width : 0,
                align: 'center',
				formatter: formatStatus
			},  {
				field : 'remark',
				title : '<?php echo \think\Lang::get('LANG_REMARK'); ?>',
				sortable : true,
				width : 0
			},{
				field:'pid',
				title:'父ID',
				hidden:true
			},{
				field:'node_type',
				title:'<?php echo \think\Lang::get('LANG_NODE_ACTION'); ?>',
				hidden:true
			},{
				field:'ismenu',
				title:'<?php echo \think\Lang::get('LANG_NODE_ISMENU'); ?>',
				hidden:true
			} ] ],
			toolbar : [ {
				text : '<?php echo \think\Lang::get('LANG_ADD'); ?>',
				iconCls : 'icon-add',
				handler : function() {
					<?php echo \think\Request::instance()->controller(); ?>AddFun();
				}
			}, '-',{
				text : '<?php echo \think\Lang::get('LANG_DEL'); ?>',
				iconCls : 'icon-delete',
				handler : function() {
					<?php echo \think\Request::instance()->controller(); ?>DeleteFun();
				}
			}, '-',{
				text : '<?php echo \think\Lang::get('LANG_EDIT'); ?>',
				iconCls : 'icon-edit',
				handler : function() {
					<?php echo \think\Request::instance()->controller(); ?>EditFun();
				}
			}, '-',{
				text :'<?php echo \think\Lang::get('LANG_APPOINT'); ?>',
				iconCls :'icon-group_gear',
				handler : function(){
					<?php echo \think\Request::instance()->controller(); ?>PrvlgSetFun();
				}
			} , '-',{
				text : '<?php echo \think\Lang::get('LANG_NODE_BTN_AUTOLEVEL'); ?>',
				iconCls : 'icon-control_equalizer',
				handler : function() {
					<?php echo \think\Request::instance()->controller(); ?>AutoLevelFun();
				}
			}]
		});

        $('#<?php echo \think\Request::instance()->controller(); ?>LeftTree').tree({
                 checkbox: false,
                 animate:true,
                 lines:true,
                 url : '<?php echo url('Xxgetnodetree'); ?>',
                 queryParams : { node_type:1 },
				 onLoadSuccess:function(node){
					var node = $('#<?php echo \think\Request::instance()->controller(); ?>LeftTree').tree('find', 1);
				 	//console.log(node)
					$('#<?php echo \think\Request::instance()->controller(); ?>LeftTree').tree('expand',node.target);
				 },
                onClick:function(node){
				var node = $('#<?php echo \think\Request::instance()->controller(); ?>LeftTree').tree('getSelected');
				$('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid('unselectAll');
                $('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid('loadData', { total: 0, rows: [] });
				$('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid({
					url : '<?php echo url('XxgetTableJson'); ?>',
					queryParams:{
						id: node.id,
                        pid: node.id
					}
				});
               }
            });

	});

    function <?php echo \think\Request::instance()->controller(); ?>CheckNodeFun(){
        $.messager.progress({
            title:'<?php echo \think\Lang::get('LANG_NODE_TITLE_AUTOUP'); ?>',
            msg:'<?php echo \think\Lang::get('LANG_NODE_MSG1'); ?>'
        });
        $.ajax({
            type:"post",
            url : '<?php echo url('XxCheckNodeName'); ?>',
            dataType:"html",
            data:"a=0",
            success:function(htmlVal){
                $.messager.progress('close');
                $.messager.alert('<?php echo \think\Lang::get('LANG_TIPS'); ?>', htmlVal);
                $('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid('reload');
				$('#<?php echo \think\Request::instance()->controller(); ?>LeftTree').tree('reload');
            }
        });
    }

    function <?php echo \think\Request::instance()->controller(); ?>AutoLevelFun(){
        $.messager.progress({
            title:'<?php echo \think\Lang::get('LANG_NODE_BTN_AUTOLEVEL'); ?>',
            msg:'<?php echo \think\Lang::get('LANG_NODE_MSG1'); ?>'
        });
        $.ajax({
            type:"post",
            url : '<?php echo url('XxAutoLevelId'); ?>',
            dataType:"html",
            data:"a=0",
            success:function(htmlVal){
                $.messager.progress('close');
                $.messager.alert('<?php echo \think\Lang::get('LANG_TIPS'); ?>', htmlVal);
                $('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid('reload');
				$('#<?php echo \think\Request::instance()->controller(); ?>LeftTree').tree('reload');
            }
        });
    }
 
	//新增
	function <?php echo \think\Request::instance()->controller(); ?>AddFun() {
		new DatagridMethod().show('add', '580', '350', '<?php echo \think\Lang::get('LANG_ADD'); ?>');
	}
	// 編輯
	function <?php echo \think\Request::instance()->controller(); ?>EditFun(){
		if(isChecked('#<?php echo \think\Request::instance()->controller(); ?>DataGrid', 0)){
			var rows= $('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid('getChecked');
			new DatagridMethod().show('edit', '600px', '350px','<?php echo \think\Lang::get('LANG_EDIT'); ?>-'+rows[0].node_name).opt({},"#<?php echo \think\Request::instance()->controller(); ?>EditForm");
		}
	}

	function <?php echo \think\Request::instance()->controller(); ?>DeleteFun(id) {
		if (id != undefined) {
			$('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid('select', id);
		}
		var node = $('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid('getSelected');
		if (node) {
			$.messager.confirm('<?php echo \think\Lang::get('LANG_TIPS'); ?>', '<?php echo \think\Lang::get('LANG_NODE_ASKMSG'); ?>:' + node.title + '？', function(b) {
				if (b) {
					$.ajax({
						url : '<?php echo url('delete'); ?>',
						data : {
							id : node.id
						},
						cache : false,
						dataType : 'JSON',
						success : function(r) {
							if (r.status) {
								//$('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid('remove', r.data.id);
								$('#<?php echo \think\Request::instance()->controller(); ?>LeftTree').tree('reload');
								$('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid('reload');//这里的reload是重新加载所有数据，而remove是根据id移除一条数据
							}
							$.messager.show({
								msg : r.info,
								title : '<?php echo \think\Lang::get('LANG_TIPS'); ?>'
							});
						}
					});
				}
			});
		}
	}
	// Add By 
	function <?php echo \think\Request::instance()->controller(); ?>PrvlgSetFun()
	{
		var rows = $('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid('getChecked');
	
		if(rows.length == 0){
			$.messager.show({
				title:'<?php echo \think\Lang::get('LANG_TIPS'); ?>',
				msg:'<?php echo \think\Lang::get('_NO_SELECT_ERROR_'); ?>'
			});
		}else if(rows.length > 1){
			 $('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid('clearChecked').datagrid('clearSelections');
				$.messager.show({
					title: '<?php echo \think\Lang::get('LANG_TIPS'); ?>',
					msg: '<?php echo \think\Lang::get('_SELECT_ERROR_'); ?>'
				});

		}else{
			
			$('<div/>').dialog({
				href : '<?php echo url('prvlgSet'); ?>',
				queryParams : { node_id:rows[0].id,nodeType:rows[0].node_type },
				width : 900,
				height : 580,
				modal : true,
				//maximizable:true,
				//maximized:true,
				title : '<?php echo \think\Lang::get('LANG_APPOINT'); ?>:'+rows[0].id+':'+rows[0].node_code+':'+rows[0].node_name,
				id : '<?php echo \think\Request::instance()->controller(); ?>prvlgDia',
				buttons : [ {
					text : '<?php echo \think\Lang::get('LANG_SAVE'); ?>',
					id : '<?php echo \think\Request::instance()->controller(); ?>ApSave',
					iconCls : 'icon-filesave',
					handler : function() {
						var roleList = [];
						//節點操作指派不是每次都會出現, 故用標題來定點TAB
						var vTitle = $('#<?php echo \think\Request::instance()->controller(); ?>SetAppoint').tabs('getSelected').panel('options').title;
						//var index = $('#<?php echo \think\Request::instance()->controller(); ?>SetAppoint').tabs('getTabIndex', $('#<?php echo \think\Request::instance()->controller(); ?>SetAppoint').tabs('getSelected'));
						//每個TAB設定參數
						var j_action,j_roleStr,j_actionStr;
						switch (vTitle){
							case '<?php echo \think\Lang::get('LANG_NODE_NDAP'); ?>':
								j_action="XxNodeActionInsert";
								var rowData  = $('#<?php echo \think\Request::instance()->controller(); ?>ActionRightGrid').datagrid('getRows','id');//全部
								var actionArr = [];
								for ( var i = 0; i < rowData.length; i++) {
									actionArr.push(rowData[i].id);
								}
								j_actionStr= actionArr.join(',');
								break;
							case '<?php echo \think\Lang::get('LANG_NODE_ROLEAP'); ?>':
								j_action="XxNodeRoleInserts";
								var actionsData  = $('#<?php echo \think\Request::instance()->controller(); ?>ActionGrid').datagrid('getChecked');
								var actionArr = [];
								for ( var i = 0; i < actionsData.length; i++) {
									actionArr.push(actionsData[i].id);
								}
								j_actionStr= actionArr.join(',');
								var roleData  = $('#<?php echo \think\Request::instance()->controller(); ?>RoldAppoint').datagrid('getRows','id');//全部
								var roleArr = [];
								for ( var i = 0; i < roleData.length; i++) {
									roleArr.push(roleData[i].id);
								}
								j_roleStr= roleArr.join(',');
								break;
						}
						$.ajax({
								type : 'post',
								url : '<?php echo url('+j_action+'); ?>',
								data:{nodeId:rows[0].id,pid:rows[0].pid,level:rows[0].node_level,actionData:j_actionStr,roleData:j_roleStr},
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
									$('#<?php echo \think\Request::instance()->controller(); ?>ExtendGrig').datagrid('reload');
									$('#<?php echo \think\Request::instance()->controller(); ?>Selections').tree('reload');
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
	function <?php echo \think\Request::instance()->controller(); ?>AppointRole()
	{
		var rows = $('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid('getChecked');
		if(rows.length == 0){
			$.messager.show({
				title:'<?php echo \think\Lang::get('LANG_TIPS'); ?>',
				msg:'<?php echo \think\Lang::get('_NO_SELECT_ERROR_'); ?>'
			});
		}else if(rows.length > 1){
			 $('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid('clearChecked').datagrid('clearSelections');
				$.messager.show({
					title: '<?php echo \think\Lang::get('LANG_TIPS'); ?>',
					msg: '<?php echo \think\Lang::get('_SELECT_ERROR_'); ?>'
				});

		}else{
			$('<div/>').dialog({
				href : '<?php echo url('setNodeRole'); ?>',
				queryParams : { nodeId:rows[0].id },
				width : 700,
				height : 600,
				modal : true,
				title : '<?php echo \think\Lang::get('LANG_NODE_ROLESET'); ?>- '+rows[0].title,
				id : '<?php echo \think\Request::instance()->controller(); ?>ApointDia',
				buttons : [{
					text : '<?php echo \think\Lang::get('LANG_SAVE'); ?>',
					iconCls : 'icon-filesave',
					handler : function() {
						var roleList = [];
						//指派角色的數據
							$('#<?php echo \think\Request::instance()->controller(); ?>SeachGrid').datagrid('checkAll');
							roleList  = $('#<?php echo \think\Request::instance()->controller(); ?>SeachGrid').datagrid('getChecked');

							$.ajax({
								type : 'post',
								url : '<?php echo url('XxNodeRoleInsert'); ?>',
								data:{nodeId:rows[0].id,pid:rows[0].pid,level:rows[0].node_level,role:roleList},
								dataType :'json',
								success :function(res){
											//alert(O2String(res));
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
								}
							});
					}
				},{
					text:'<?php echo \think\Lang::get('LANG_CLOSE'); ?>',
					iconCls:'icon-cancel',
					handler:function(){
						$('#<?php echo \think\Request::instance()->controller(); ?>ApointDia').dialog('destroy');
					}
				}],
				onClose : function() {
					$(this).dialog('destroy');
				}
			});
		}
	}
</script>
