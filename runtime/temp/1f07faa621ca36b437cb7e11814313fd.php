<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:63:"D:\xampp\htdocs\TMES\public/../app/home\view\Tccgcus\index.html";i:1540885856;}*/ ?>
<div id="__MODULE__PagePanel" class="easyui-layout" data-options="fit : true,border : false">
	<div data-options="title:'<?php echo \think\Lang::get('LANG_CGCUS_SYSNODE'); ?>',
						region:'west'"
						style="width:220px;padding:0px">
		<div id="__MODULE__Tree" class="easyui-layout" data-options="fit:true,border:false">
			<div data-options="region:'center',border:false">
				<ul id="__MODULE__LeftTree"></ul>
			</div>
		</div>
	</div>
	<div id="__MODULE__NodeChild" data-options="region:'center',border:false">
		<table  id="__MODULE__DataGrid"
		 data-options="url : '<?php echo url('x_xgetTableJson'); ?>',
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
					nowrap : true,
					striped:true,
					singleSelect:true,
					autoRowHeight:false,
					onBeforeLoad:function(param){
						$(this).datagrid('clearChecked').datagrid('clearSelections');
					},
					toolbar : [ <?php echo $toolBar; ?> ]
					">
		<thead>
			<tr>
				<th data-options="field:'id',width:50,checkbox:true"><?php echo \think\Lang::get('LANG_CGCUS_ID'); ?></th>
				<th data-options="field:'idx',width:50"><?php echo \think\Lang::get('LANG_CGCUS_ID'); ?></th>
				<th data-options="field:'cus_code',width:150"><?php echo \think\Lang::get('LANG_CGCUS_CODE'); ?></th>
				<th data-options="field:'cus_name',width:80"><?php echo \think\Lang::get('LANG_CGCUS_LANGCODE'); ?></th>
				<th data-options="field:'lang_value',width:100"><?php echo \think\Lang::get('LANG_CGCUS_NAME'); ?></th>
				<th data-options="field:'cg_type',width:150,formatter:formatCgType"><?php echo \think\Lang::get('LANG_CGCUS_TYPE'); ?></th>
				<th data-options="field:'remark',width:120"><?php echo \think\Lang::get('LANG_REMARK'); ?></th>
				<th data-options="field:'snode_id',width:80"><?php echo \think\Lang::get('LANG_CGCUS_NODE'); ?></th>
				<th data-options="field:'status',width:80,formatter:formatStatus"><?php echo \think\Lang::get('LANG_STATUS'); ?></th>
			</tr>
			</thead>
		</table>
	</div>
	<div id="__MODULE__DiaLog"></div>
</div>
<script type="text/javascript">
var formatCgType = function(value, row, index) {
	if (value==1){
		/*初始化類*/
		return '<font color=green><?php echo \think\Lang::get('LANG_CGCUS_INITTYPE'); ?></font>';
	}else if (value==2){
		/*系統類*/
		return '<font color=red><?php echo \think\Lang::get('LANG_CGCUS_SYSTYPE'); ?></font>';
	}else if(value==-1){
		/*用戶類*/
		return '<font color=red><?php echo \think\Lang::get('LANG_CGCUS_USERTYPE'); ?></font>';
	}
}
	$(function(){
		var dg = $('#__MODULE__DataGrid').datagrid();
			dg.datagrid('removeFilterRule');
			dg.datagrid('enableFilter',[{field:'idx',
								 type:'text'
								},{field:'cus_code',
								 type:'text'
								},{field:'cus_name',
								 type:'text'
								},{field:'lang_value',
								 type:'text'
								},{field:'cg_type',
								 type:'text'
								},{field:'remark',
								 type:'text'
								},{field:'snode_id',
								 type:'text'
								},{field:'status',
								 type:'text'
								}
								]);
		//列表名,tree方式顯示
		$('#__MODULE__LeftTree').tree({
			//url:'/home/tccgcus/x_xgetNodeTree',
			url: '<?php echo url('Tcnode/XxgetNodeTree'); ?>', 
			checkbox:false,
			animate:true,
			lines:true,
			onBeforeExpand:function(node,param){
			//	alert(node.id);
				$('#__MODULE__LeftTree').tree('options').url = "<?php echo url('Tcnode/XxgetNodeTree'); ?>";
				//$('#__MODULE__LeftTree').tree('options').url = "/home/tccgcus/x_xgetNodeTree/id/" + node.id;
			},
			onClick:function(node){
				var node = $('#__MODULE__LeftTree').tree('getSelected');
				$('#__MODULE__DataGrid').datagrid({
						url:'<?php echo url('XxgetTableJson'); ?>',
						queryParams:{
							id: node.id
						}
				});
			}
		});
	});
	//-----------------------------------------------列表名 增刪改-----------------------------------------------------
	//新增
	function __MODULE__AddFun() {
		new DatagridMethod().show('add', '400px', '350px', '<?php echo \think\Lang::get('LANG_ADD'); ?>').opt();
	}
	// 編輯
	function __MODULE__EditFun(){
		if(isChecked('#__MODULE__DataGrid', 0)){
			var rows= $('#__MODULE__DataGrid').datagrid('getChecked');
			new DatagridMethod().show('edit', '400px', '350px','<?php echo \think\Lang::get('LANG_EDIT'); ?>-'+rows[0].lang_name).opt({},"#__MODULE__EditForm");
		}
	}
	function __MODULE__SetListFun(){
		if(isChecked('#__MODULE__DataGrid', 0)){
			var rows = $('#__MODULE__DataGrid').datagrid('getChecked');
			var title = rows[0].cus_code+'列表名'+ rows[0].cus_name;
			// 在 dialog 中顯示 TcCgCusSub 模塊的 index
			new DatagridMethod('TcCgCusSub').show('index', 0, 0, title, 1, {pid: rows[0].id});
		}
	}
	//----------------------------------- 查詢 -----------------------------------------
	//暫時開發完成
	function __MODULE__SearchFun(value){
		var node = $('#__MODULE__LeftTree').tree('find', value);
		$('#__MODULE__LeftTree').tree('select', node.target);
		$('#__MODULE__LeftTree').tree({
			//url:'/home/tccgcus/x_xgetNodeTree?search=' + encodeURIComponent(value),
		});
	}
	function __MODULE__CleanFun(){
		$('#__MODULE__LeftTree').tree({
			url:'/home/tccgcus/x_xgetNodeTree'
		});
	}
</script>