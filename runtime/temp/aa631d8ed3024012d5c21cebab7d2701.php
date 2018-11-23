<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:64:"D:\xampp\htdocs\TMES\public/../app/home\view\Tcaction\index.html";i:1541491452;}*/ ?>
<div class="easyui-layout" data-options="fit : true,border : false">
	<div data-options="region:'center',border:false">
     <table id="<?php echo \think\Request::instance()->controller(); ?>DataGrid"
		 data-options="
		    url : '<?php echo url('XxgetTableJson'); ?>',
			fit : true,
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
			nowrap : false,
            striped:true,
			singleSelect:true,
			onBeforeLoad:function(param){
				$(this).datagrid('clearChecked').datagrid('clearSelections');
			},
			toolbar : [ <?php echo $toolBar; ?> ]
			">
		<thead>
            <tr>
			<th data-options="field:'id',width:30,checkbox:true" sortable="true"><?php echo \think\Lang::get('LANG_ACTION_ID'); ?></th>
			<th data-options="field:'action_code',width:100"><?php echo \think\Lang::get('LANG_ACTION_CODE'); ?></th>
			<th data-options="field:'action_name',width:100"><?php echo \think\Lang::get('LANG_ACTION_NAME'); ?></th>
			<th data-options="field:'lang_value',width:100"><?php echo \think\Lang::get('LANG_ACTION_NAMEVALUE'); ?></th>
			<th data-options="field:'icon',width:100,formatter:<?php echo \think\Request::instance()->controller(); ?>Icon"><?php echo \think\Lang::get('LANG_ACTION_ICON'); ?></th>
			<th data-options="field:'status',width:90,formatter:formatStatus"><?php echo \think\Lang::get('LANG_STATUS'); ?></th>
			<th data-options="field:'list_value',width:90"><?php echo \think\Lang::get('LANG_ACTION_TYPE'); ?></th>
			<th data-options="field:'remark',width:100"><?php echo \think\Lang::get('LANG_REMARK'); ?></th>
			</tr>
		</thead>
		</table>
	</div>
	<div id="<?php echo \think\Request::instance()->controller(); ?>DiaLog"></div>
</div>
<script type="text/javascript">
	$(function(){
		var dg = $('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid();
		dg.datagrid('removeFilterRule');
		dg.datagrid('enableFilter',[{field:'action_code',
							 type:'text'
							},
							{field:'pic_code',
							 type:'text'
							},
							{field:'remark',
							 type:'text'
							}
		]);
	});
	function <?php echo \think\Request::instance()->controller(); ?>Icon(value,row,index){
		if (value!=''){
			var imageFile = '/static/easyui/themes/icons/' + value+'.png';
	        return '<img class="item-img" src="'+imageFile+'"/><span class="item-text">'+value+'</span>';
		}
	}

	//新增
	function <?php echo \think\Request::instance()->controller(); ?>AddFun() {
		new DatagridMethod().show('add', '400px', '350px', '<?php echo \think\Lang::get('LANG_ADD'); ?>').opt();
	}
	// 編輯
	function <?php echo \think\Request::instance()->controller(); ?>EditFun(){
		if(isChecked('#<?php echo \think\Request::instance()->controller(); ?>DataGrid', 0)){
			var rows= $('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid('getChecked');
			new DatagridMethod().show('edit', '400px', '350px','<?php echo \think\Lang::get('LANG_EDIT'); ?>-'+rows[0].role_name).opt({},"#<?php echo \think\Request::instance()->controller(); ?>EditForm");
		}
	}
	//導出
	function <?php echo \think\Request::instance()->controller(); ?>ExportDataFun() {
		var timestamp = Date.parse(new Date());//時間戳作為參數，解決緩存問題
		location.href="<?php echo url('XxExportData'); ?>?"+timestamp;	
	}
</script>