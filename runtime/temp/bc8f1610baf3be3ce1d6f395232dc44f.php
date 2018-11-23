<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:62:"D:\xampp\htdocs\TMES\public/../app/home\view\Tcdept\index.html";i:1541402388;}*/ ?>
<div id="<?php echo \think\Request::instance()->controller(); ?>PagePanel" class="easyui-layout" data-options="fit : true,border : false">
	<div data-options="title:'<?php echo \think\Lang::get('LANG_DEPT_TITLE_TREE'); ?>',region:'west'" style="width:240px;padding:0px">
		 <ul id="<?php echo \think\Request::instance()->controller(); ?>LeftTree"></ul>
	</div>
	<div id="<?php echo \think\Request::instance()->controller(); ?>NodeChild" data-options="region:'center',border:false">
		<table id="<?php echo \think\Request::instance()->controller(); ?>DataGrid">
		</table>
	</div>
	<div id="<?php echo \think\Request::instance()->controller(); ?>DiaLog"></div>
</div>

<script>
 $(function(){
            $('#<?php echo \think\Request::instance()->controller(); ?>LeftTree').tree({
                 checkbox: false,
                 animate:true,
                 lines:true,
                 url : '<?php echo url('XxgetDeptTree'); ?>',
                onClick:function(node){
				var node = $('#<?php echo \think\Request::instance()->controller(); ?>LeftTree').tree('getSelected');
					$('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid({
						url : '<?php echo url('Xxlist2'); ?>',
						queryParams:{
							id: node.id
						},
						striped:true
					});
				if (!node.state){
					$.messager.show({
									title : '<?php echo \think\Lang::get('LANG_TIPS'); ?>',
									msg :"<?php echo \think\Lang::get('LANG_DEPT_MSG1'); ?>",
									showType:'show',
									height:100,
									width:250,
									timeout :1000
									});
				};
               }
            });
   });

   $(function() {
		$('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid({
			url : '<?php echo url('Xxlist2'); ?>',
			fit : true,
			border : false,
			rownumbers:true,
			pagination : true,
			idField : 'id',
			pageSize :10,
			pageList : [10,15,20,30,40,50],
			sortName : 'dept_name',
			sortOrder : 'asc',
			checkOnSelect : true,
			selectOnCheck : true,
			nowrap : true,
			singleSelect:true,
			frozenColumns : [ [  {
				field : 'id',
				title : '<?php echo \think\Lang::get('LANG_DEPT_ID'); ?>',
				width : 50,
				checkbox : true
			}, {
				field : 'dept_code',
				title : '<?php echo \think\Lang::get('LANG_DEPT_CODE'); ?>',
				width : 80,
				sortable : true,
				formatter : function(value, row, index) {
					return '<span title="'+value+'">'+value+'</span>';
				}
			}, {
				field : 'dept_name',
				title : '<?php echo \think\Lang::get('LANG_DEPT_NAME'); ?>',
				//width : 80,
				sortable : true,
				formatter : function(value, row, index) {
					return '<span title="'+value+'">'+value+'</span>';
				}
			}, {
				field : 'cost_code',
				title : '<?php echo \think\Lang::get('LANG_DEPT_COST_CODE'); ?>',
				//width : 80,
				sortable : true,
				formatter : function(value, row, index) {
					return '<span title="'+value+'">'+value+'</span>';
				}
			}, {
				field : 'pid',
				title : '父節點ID',
				width : 80,
				sortable : true,
				hidden : true,
				formatter : function(value, row, index) {
					return '<span title="'+value+'">'+value+'</span>';
				}
			} ] ],
			columns : [ [ {
				field : 'status',
				title : '<?php echo \think\Lang::get('LANG_STATUS'); ?>',
				width : 60,
				//hidden : true,
				formatter :formatStatus
			} ] ],
			toolbar : [ {
				text : '<?php echo \think\Lang::get('LANG_ADD'); ?>',
				iconCls : 'icon-add',
				handler : function() {
					<?php echo \think\Request::instance()->controller(); ?>AddFun();
				}
			}, '-', {
				text : '<?php echo \think\Lang::get('LANG_EDIT'); ?>',
				iconCls : 'icon-edit',
				handler : function() {
					<?php echo \think\Request::instance()->controller(); ?>EditFun();
				}
			}/*, '-', {
				text : '刪除',
				iconCls : 'icon-remove',
				handler : function() {
					<?php echo \think\Request::instance()->controller(); ?>DelDeptFun();
				}
			}, '-', {
				text : '<?php echo \think\Lang::get('LANG_DEPT_BTN_APUSER'); ?>',
				iconCls : 'icon-group_gear',
				handler : function() {
					<?php echo \think\Request::instance()->controller(); ?>SetUserFun();
				}
			}, '-', {
				text : '<?php echo \think\Lang::get('LANG_DEPT_BTN_APROLE'); ?>',
				iconCls : 'icon-role',
				handler : function() {
					<?php echo \think\Request::instance()->controller(); ?>SetRoleFun();
				}
			}*/]
		});

	});

	//新增
	function <?php echo \think\Request::instance()->controller(); ?>AddFun() {
		new DatagridMethod().show('add', '350px', '250px', '<?php echo \think\Lang::get('LANG_ADD'); ?>').opt();
	}
	// 編輯
	function <?php echo \think\Request::instance()->controller(); ?>EditFun(){
		if(isChecked('#<?php echo \think\Request::instance()->controller(); ?>DataGrid', 0)){
			var rows= $('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid('getChecked');
			new DatagridMethod().show('edit', '350px', '250px','<?php echo \think\Lang::get('LANG_EDIT'); ?>-'+rows[0].dept_name).opt({},"#<?php echo \think\Request::instance()->controller(); ?>EditForm");
		}
	}
	
</script>


