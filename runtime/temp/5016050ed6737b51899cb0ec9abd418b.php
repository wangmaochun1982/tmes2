<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:60:"D:\xampp\htdocs\TMES\public/../app/home\view\Tcmt\index.html";i:1538018025;}*/ ?>

<div class="easyui-layout" data-options="fit : true,border : false">
	<div data-options="region:'center',border:false">
     <table id="__MODULE__DataGrid"
		 data-options="url : '/home/tcmt/x_xgetTableJson',
			fit : true,
			border : false,//定义边框
            rownumbers:true,//显示行号
			pagination : true,//定义分页组件
			idField : 'model_name',
			pageSize :20,
			pageList : dg_pageList,
			checkOnSelect : true,//选择checkbox则选择行
			selectOnCheck : true,//选择行则选择checkbox
			nowrap : false,
            striped:true,//单元格显示条纹
			singleSelect:true,//默认不单选
			toolbar : [ <?php echo $toolBar; ?> ]">
		 <thead data-options="frozen:true">
		    <tr>
				<th data-options="field:'id',checkbox:true,width:50">ID</th>
                <th data-options="field:'model_name',sortable:true,width:140" ><?php echo \think\Lang::get('LANG_MODEL_NAME'); ?></th>
				<th data-options="field:'m_flag',sortable:true,width:90"><?php echo \think\Lang::get('LANG_MODEL_MFLAG'); ?></th>
				<th data-options="field:'model_serial',sortable:true,width:140"><?php echo \think\Lang::get('LANG_MODEL_SERIAL'); ?></th>
				<th data-options="field:'model_no',sortable:true,width:140"><?php echo \think\Lang::get('LANG_MODEL_NO'); ?></th>
			</tr>
		</thead>
		<thead>
            <tr>
                <th data-options="field:'semi_product',width:140"><?php echo \think\Lang::get('LANG_MODEL_SEMI'); ?></th>
                <th data-options="field:'model_type_name1',width:60"><?php echo \think\Lang::get('LANG_MODEL_TYPE1'); ?></th>
                <th data-options="field:'r_type_name',width:90"><?php echo \think\Lang::get('LANG_MODEL_RTYPE'); ?></th>
                <th data-options="field:'cust_code',width:130"><?php echo \think\Lang::get('LANG_MODEL_CUST'); ?></th>
                <th data-options="field:'cust_model',width:130"><?php echo \think\Lang::get('LANG_CUST_MODEL'); ?></th>
                <th data-options="field:'cust_desc',width:130"><?php echo \think\Lang::get('LANG_MODEL_CUST_DESC'); ?></th>
                <th data-options="field:'ean_code',width:110"><?php echo \think\Lang::get('LANG_MODEL_EAN_CODE'); ?></th>
                <th data-options="field:'cust_1',width:60"><?php echo \think\Lang::get('LANG_MODEL_CUST1'); ?></th>
                <th data-options="field:'cust_2',width:60"><?php echo \think\Lang::get('LANG_MODEL_CUST2'); ?></th>
                <th data-options="field:'m_1',width:60"><?php echo \think\Lang::get('LANG_MODEL_M1'); ?></th>
                <th data-options="field:'m_2',width:60"><?php echo \think\Lang::get('LANG_MODEL_M2'); ?></th>
                <th data-options="field:'m_size',width:60">Size</th>
                <th data-options="field:'route_desc',width:130"><?php echo \think\Lang::get('LANG_MODEL_ROUTE'); ?></th>
                <th data-options="field:'model_type3',width:130"><?php echo \think\Lang::get('LANG_MODEL_TYPE3'); ?></th>
                <th data-options="field:'unit',width:40"><?php echo \think\Lang::get('LANG_MODEL_UNIT'); ?></th>
                <th data-options="field:'pack_type',width:60"><?php echo \think\Lang::get('LANG_MODEL_PACK_TYPE'); ?></th>
                <th data-options="field:'status_name',width:60"><?php echo \think\Lang::get('LANG_STATUS'); ?></th>
            </tr>
        </thead>
		</table>
	</div>
	<div id="__MODULE__DiaLog"></div>
</div>
<script type="text/javascript">
	$(function(){
		var __MODULE__FilterDG = $('#__MODULE__DataGrid').datagrid();
		__MODULE__FilterDG.datagrid('removeFilterRule');
		__MODULE__FilterDG.datagrid('enableFilter',[{field:'model_name',
									 type:'text'
									},
									{field:'model_serial',
									 type:'text'
									},
									{field:'m_flag',
									 type:'text'
									},
									{field:'model_no',
									 type:'text'
									},
									{field:'semi_product',
									 type:'text'
									},
									{field:'model_type1',
									 type:'text'
									},
									{field:'cust_code',
									 type:'text'
									},
									{field:'cust_desc',
									 type:'text'
									},
									{field:'pack_type',
									 type:'text'
									},
									{field:'ean_code',
									 type:'text'
									},
									{field:'route_desc',
									 type:'text'
									},
									{field:'cust_model',
									 type:'text'
									},
									{field:'model_type3',
									 type:'text'
									},
									{field:'unit',
									 type:'text'
									},{
										field:'model_type_name1',
										type:'combobox',
										options:{
											panelHeight:'auto',
											url: '/home/tcmt/x_xgetOption?cusCode=model_type1&filter=1',
											valueField:'id',
											textField:'text',
											onChange:function(value){
												if (value == ''){
													__MODULE__FilterDG.datagrid('removeFilterRule', 'model_type1');
												} else {
													__MODULE__FilterDG.datagrid('addFilterRule', {
														field: 'model_type1',
														op: 'equal',
														value: value
													});
												}
												__MODULE__FilterDG.datagrid('doFilter');
											}
										}
									},{
										field:'status_name',
										type:'combobox',
										options:{
											panelHeight:'auto',
											url: '/home/tcmt/x_xgetOption?cusCode=m_status&filter=1',
											valueField:'id',
											textField:'text',
											onChange:function(value){
												if (value == ''){
													__MODULE__FilterDG.datagrid('removeFilterRule', 'm_status');
												} else {
													__MODULE__FilterDG.datagrid('addFilterRule', {
														field: 'm_status',
														op: 'equal',
														value: value
													});
												}
												__MODULE__FilterDG.datagrid('doFilter');
											}
										}
									},{
										field:'r_type_name',
										type:'combobox',
										options:{
											panelHeight:'auto',
											url: '/home/tcmt/x_xgetOption?cusCode=r_type&filter=1',
											valueField:'id',
											textField:'text',
											onChange:function(value){
												if (value == ''){
													__MODULE__FilterDG.datagrid('removeFilterRule', 'r_type');
												} else {
													__MODULE__FilterDG.datagrid('addFilterRule', {
														field: 'r_type',
														op: 'equal',
														value: value
													});
												}
												__MODULE__FilterDG.datagrid('doFilter');
											}
										}
									}
				]);
	});
	//新增
	function __MODULE__AddFun() {
		new DatagridMethod().show('add', '', '', '<?php echo \think\Lang::get('LANG_ADD'); ?>');
	}
	//編輯
	function __MODULE__EditFun(){
		if(isChecked('#__MODULE__DataGrid', 0)){
			new DatagridMethod().show('edit').opt({},'#__MODULE__EditForm');
		}
	}
	//查看
	function __MODULE__ViewFun(){
		if(isChecked('#__MODULE__DataGrid', 0)){
			//var rows= $('#__MODULE__DataGrid').datagrid('getChecked');
			new DatagridMethod().show('view');
		}
	}
	/*
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
			href : '/home/tcmt/edit',
			maximized:false,
			maximizable:true,
			width : 680,
			height : 480,
			modal : true,
			title : '<?php echo \think\Lang::get('LANG_EDIT'); ?>',
			id : '__MODUEL__EdtDiv',
			buttons : [ {
				text : '<?php echo \think\Lang::get('LANG_SAVE'); ?>',
				iconCls : 'icon-edit',
				handler : function() {
					$('#__MODULE__EdtForm').form('submit', {
						url : '/home/tcmt/x_xUpdate',
						success : function(result) {
							try {
								var r = $.parseJSON(result);
								if (r.status==1) {
									$('#__MODULE__DataGrid').datagrid('reload');
									$('#__MODUEL__EdtDiv').dialog('destroy');
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
			},{
				text:'<?php echo \think\Lang::get('LANG_CANCEL'); ?>',
				iconCls:'icon-cancel',
				handler:function(){
					$('#__MODUEL__EdtDiv').dialog('destroy');
				}
			} ],
			onClose : function() {
				$(this).dialog('destroy');
			},
			onLoad : function() {
				var rows= $('#__MODULE__DataGrid').datagrid('getChecked');
				var editData =rows[0];//setValue
				$('#__MODULE__route_name').combogrid('setValue',editData.route_code);
				$('#__MODULE__route_name').combogrid('setText',editData.route_desc);
				$('#__MODULE__EdtForm').form('load',editData);
			}
		});
	}
	}
    function __MODULE__AddFun() {
		$('#__MODULE__DataGrid').datagrid('clearChecked').datagrid('clearSelections');
		$('<div/>').dialog({
			href : '/home/tcmt/add',
			maximized:false,
			maximizable:true,
			width : 680,
			height : 480,
			modal : true,
			title : '<?php echo \think\Lang::get('LANG_ADD'); ?>',
			id : '__MODULE__AddDiv',
			buttons : [ {
				text : '<?php echo \think\Lang::get('LANG_SAVE'); ?>',
				iconCls : 'icon-accept',
				handler : function() {
					var index = $('#__MODULE__Add').tabs('getTabIndex', $('#__MODULE__Add').tabs('getSelected'));
					if(index==0 ){
						var j_formid="__MODULE__Erp2MesForm";
						var j_action="x_xErp2MesAdd";
					}
					else if(index==1 ){
						var j_formid="__MODULE__AddForm";
						var j_action="x_xInsert";
					}
					$('#'+j_formid).form('submit', {
						url : '/home/tcmt/'+j_action,
						onSubmit: function(){
							$.messager.progress();
						},
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
							$.messager.progress('close');
						}
					});
				}
			},{
				text:'<?php echo \think\Lang::get('LANG_CANCEL'); ?>',
				iconCls:'icon-cancel',
				handler:function(){
					$('#__MODULE__AddDiv').dialog('destroy');
				}
			} ],
			onClose : function() {
				$(this).dialog('destroy');
			}
		});
	}*/
	 function __MODULE__UploadFileFun() {
		var rows= $('#__MODULE__DataGrid').datagrid('getChecked');
		var modelName;
		if (rows.length > 1) {
		    $('#__MODULE__DataGrid').datagrid('clearChecked').datagrid('clearSelections');
			$.messager.show({
				title: '<?php echo \think\Lang::get('LANG_TIPS'); ?>',
				msg: '<?php echo \think\Lang::get('_SELECT_ERROR_'); ?>'
			});
		} else if (rows.length == 1 || rows.length == 0) {
			if (rows.length == 1)
				modelName = rows[0].model_name;
		$('<div/>').dialog({
			href : '/home/tcmt/uploadFile?model_name='+modelName,
			width : 520,
			height : 360,
			modal : true,
			title : '<?php echo \think\Lang::get('LANG_UPLOAD'); ?>',
			buttons : [ {
				text : '<?php echo \think\Lang::get('LANG_SAVE'); ?>',
				iconCls : 'icon-accept',
				handler : function() {
					var vTitle = $('#__MODULE__tt').tabs('getSelected').panel('options').title;
					if (vTitle == "<?php echo \think\Lang::get('LANG_MODEL_UPTITLE'); ?>") {
						$('#__MODULE__updbmForm').form('submit', {
							url : '/home/tcmt/x_xUploadDo',
							success : function(result) {
								try {
									var r = $.parseJSON(result);
									if (r.status==1) {
										$("#__MODUEL__model_name").val('');
										document.getElementById("file1").outerHTML = document.getElementById("file1").outerHTML;
										$("#__MODUEL__model_name").focus();
										$('#__MODULE__DataGrid').datagrid('reload');
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
					} else {
						$('#__MODULE__updboForm').form('submit', {
							url : '/home/tcmt/x_xUploadDo',
							success : function(result) {
								try {
									var r = $.parseJSON(result);
									if (r.status==1) {
										$("#__MODULE__layup_no").val('');
										document.getElementById("file2").outerHTML = document.getElementById("file2").outerHTML;
										$("#__MODULE__layup_no").focus();
										$('#__MODULE__DataGrid').datagrid('reload');
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
				}
			} ],
			onClose : function() {
				$(this).dialog('destroy');
			}
		});
		}
	}
	/*
	function __MODULE__ViewFun() {
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
				href : '/home/tcmt/view',
				maximized:false,
				maximizable:true,
				width : 680,
				height : 480,
				modal : true,
				title : '<?php echo \think\Lang::get('LANG_VIEW'); ?>',
				id : '__MODUEL__ViewDiv',
				buttons : [ {
					text:'<?php echo \think\Lang::get('LANG_CLOSE'); ?>',
					iconCls:'icon-cancel',
					handler:function(){
						$('#__MODUEL__ViewDiv').dialog('destroy');
					}
				} ],
				onClose : function() {
					$(this).dialog('destroy');
				},
				onLoad : function() {
					var editData =rows[0];
					$('#__MODULE__ViewForm').form('load',editData);
				}
			});
		}
	}*/
	//導出Excel
	function __MODULE__ExportDataFun(){
		var timestamp = Date.parse(new Date());//時間戳作為參數，解決緩存問題
		location.href="/home/tcmt/x_xexportData?"+timestamp;
	}
</script>