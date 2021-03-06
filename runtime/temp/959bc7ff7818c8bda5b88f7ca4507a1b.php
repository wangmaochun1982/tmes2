<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:60:"D:\xampp\htdocs\TMES\public/../app/home\view\Tpso\index.html";i:1540801724;}*/ ?>
<div class="easyui-layout" data-options="fit : true,border : false">
	<div data-options="region:'center',border:false"> 
     <table id="__MODULE__DataGrid"
		 data-options="url : '<?php echo url('XxGetTableJson'); ?>',
			fit : true,
			border : false,//定义边框
            rownumbers : true,//显示行号
			pagination : true,//定义分页组件
			pageSize : 20,
			pageList : [10,20,50],
			sortName : 'so_number', 
			sortOrder : 'desc',
			remoteFilter : true,
			checkOnSelect : true,//选择checkbox则选择行
			selectOnCheck : true,//选择行则选择checkbox
			nowrap : false,
            striped : true,//单元格显示条纹
			singleSelect : false,//默认不单选
			toolbar : [ <?php echo $toolBar; ?> ]"> 
        <thead>
		    <tr>                
				<th data-options="field:'id',checkbox:true,width:50">ID</th>
                <th data-options="field:'so_number',sortable:true,width:80" ><?php echo \think\Lang::get('LANG_SO_NO'); ?></th>
                <th data-options="field:'so_desc',sortable:true,width:80" ><?php echo \think\Lang::get('LANG_SO_TYPE'); ?></th>
				<th data-options="field:'po_no',sortable:true,width:150"><?php echo \think\Lang::get('LANG_SO_PO_NO'); ?></th>
                <th data-options="field:'target_qty',width:100"><?php echo \think\Lang::get('LANG_SO_TARGET_QTY'); ?></th>
                <th data-options="field:'finish_qty',width:60"><?php echo \think\Lang::get('LANG_SO_FINISH_QTY'); ?></th>
                <th data-options="field:'dept_1',width:80"><?php echo \think\Lang::get('LANG_SO_DEPT1_DESC'); ?></th>
                <th data-options="field:'dept1_desc',width:80"><?php echo \think\Lang::get('LANG_SO_DEPT1_DESC'); ?></th>
                <th data-options="field:'dept2_desc',width:50"><?php echo \think\Lang::get('LANG_SO_DEPT2_DESC'); ?></th>
                <th data-options="field:'cus_name',width:150"><?php echo \think\Lang::get('LANG_SO_CUS_NAME'); ?></th>
				<th data-options="field:'freight_id',width:80,hidden:true"></th>
                <th data-options="field:'freight_desc',width:80"><?php echo \think\Lang::get('LANG_SO_FREIGHT_DESC'); ?></th>
                <th data-options="field:'pin_code',sortable:true,hidden:true"></th>
                <th data-options="field:'pin_desc',sortable:true,width:150"><?php echo \think\Lang::get('LANG_SO_PIN_DESC'); ?></th>
                <th data-options="field:'plan_finish',width:100"><?php echo \think\Lang::get('LANG_SO_PLAN_FINISH'); ?></th>
                <th data-options="field:'actu_finish',sortable:true,width:140"><?php echo \think\Lang::get('LANG_SO_ACTU_FINISH'); ?></th>
                <th data-options="field:'so_status',width:100"><?php echo \think\Lang::get('LANG_STATUS'); ?></th>
				<th data-options="field:'crea_date',width:100"><?php echo \think\Lang::get('LANG_SO_CREA_DATE'); ?></th>
				<th data-options="field:'remark',width:100"><?php echo \think\Lang::get('LANG_REMARK'); ?></th>
            </tr>
        </thead>
		</table> 
	</div>   
</div>   
<script type="text/javascript"> 
	$(function(){
		var dg = $('#__MODULE__DataGrid').datagrid();
		dg.datagrid('removeFilterRule');
		dg.datagrid('enableFilter',[{field:'so_number',
									 type:'text'
									},{field:'so_desc',
									 type:'text'
									},{field:'dept_1',
									 type:'text',
									},{field:'cus_name',
									 type:'text',
									},{field:'so_status',
									 type:'text',
									}]);
	});
	var js_Width = $(document).width()*2/3;
	var js_Height = 360; 
    function __MODULE__AddFun() { 
		$('#__MODULE__DataGrid').datagrid('clearChecked').datagrid('clearSelections');
		$('<div/>').dialog({
			href : "<?php echo url('add'); ?>",
			width : 1000,//$(document).width(),
			height : 600,//$(document).height(),
			modal : true,
			title : '<?php echo \think\Lang::get('LANG_ADD'); ?>',
			id	: '__MODULE__AddDiv',
			buttons : [ {
				text : '<?php echo \think\Lang::get('LANG_SAVE'); ?>',
				iconCls : 'icon-accept',
				handler : function() { 
					$('#__MODULE__AddForm').form('submit', {
						url : "<?php echo url('XxInsert'); ?>",
						success : function(result) {
							try {
								var r = $.parseJSON(result);
								if (r.status==1) {
									$('#__MODULE__DataGrid').datagrid('reload');
									$("#__MODULE__AddDiv").dialog('destroy');
									$.messager.show({
									title : '<?php echo \think\Lang::get('LANG_TIPS'); ?>',
									msg :r.info,
									});
								}else{
									$.messager.show({
									title : '<?php echo \think\Lang::get('LANG_TIPS'); ?>',
									msg :r.info,
									});
								}
							} catch (e) {
								$.messager.alert('<?php echo \think\Lang::get('LANG_TIPS'); ?>', result);
							}
						}
					});
				}
			} ],
			onClose : function() {
				$(this).dialog('destroy');
			}
		});
	};
	function __MODULE__EditFun() {
		var rows= $('#__MODULE__DataGrid').datagrid('getChecked');
		if (rows.length > 1) {
		    $('#__MODULE__DataGrid').datagrid('clearChecked').datagrid('clearSelections');
			$.messager.show({
				title: '<?php echo \think\Lang::get('LANG_TIPS'); ?>',
				msg: '<?php echo \think\Lang::get('_SELECT_ERROR_'); ?>'
			});
		}else if (rows.length == 0) {
				$.messager.show({
					title: '<?php echo \think\Lang::get('LANG_TIPS'); ?>',
					msg: '<?php echo \think\Lang::get('_NO_SELECT_ERROR_'); ?>'
				});
		}else {
			$('<div/>').dialog({
				href : "<?php echo url('edit'); ?>?so_number="+rows[0].so_number+'&so_type='+rows[0].so_type,
				width : 1000,//js_Width,
				height :600,//js_Height,
				modal : true,
				title : '<?php echo \think\Lang::get('LANG_EDIT'); ?>',
				id	: '__MODULE__EdtDiv',
				buttons : [ {
					text : '<?php echo \think\Lang::get('LANG_SAVE'); ?>',
					iconCls : 'icon-accept',
					handler : function() { 
						$('#__MODULE__EdtForm').form('submit', {
							url : "<?php echo url('XxUpdate'); ?>",
							success : function(result) {
								try {
									var r = $.parseJSON(result);
									if (r.status==1) {
										$('#__MODULE__DataGrid').datagrid('reload');
										$("#__MODULE__EdtDiv").dialog('destroy');
										$.messager.show({
										title : '<?php echo \think\Lang::get('LANG_TIPS'); ?>',
										msg :r.info,
										});
									}else{
										$.messager.show({
										title : '<?php echo \think\Lang::get('LANG_TIPS'); ?>',
										msg :r.info,
										});
									}
								} catch (e) {
									$.messager.alert('<?php echo \think\Lang::get('LANG_TIPS'); ?>', result);
								}
							}
						});
					}
				} ],
				onClose : function() {
					$(this).dialog('destroy');
				},
				onLoad : function() {
					var rows= $('#__MODULE__DataGrid').datagrid('getChecked');
					var editData =rows[0];
					$('#__MODULE__EdtForm').form('load',editData); 
				}
			});
		}
	}
</script>