<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:62:"D:\xampp\htdocs\TMES\public/../app/home\view\Tcrole\index.html";i:1541559544;}*/ ?>
<div id="<?php echo \think\Request::instance()->controller(); ?>PagePanel" class="easyui-layout" data-options="fit : true,border : false">
	<div data-options="title:'<?php echo \think\Lang::get('LANG_ROLE_TITLE_TREE'); ?>',region:'west'" style="width:240px;padding:0px">
		 <ul id="<?php echo \think\Request::instance()->controller(); ?>LeftTree"></ul>
	</div>
	<div id="<?php echo \think\Request::instance()->controller(); ?>NodeChild" data-options="region:'center',border:false">
		<div id="<?php echo \think\Request::instance()->controller(); ?>InnerPanel" class="easyui-layout" data-options="fit:true,border:false">
			<div data-options="region:'center',border:false">
				<table  id="<?php echo \think\Request::instance()->controller(); ?>DataGrid"
					data-options="url : '<?php echo url('XxgetTableJson'); ?>',
					fit : true,
					border : false,//定义边框
					rownumbers:true,//显示行号
					pagination : true,//定义分页组件
					pageSize :20,
					pageList : dg_pageList,
					idField : 'id',
					sortName : 'id',
					sortOrder : 'asc',
					nowrap : true,
					striped:true,//单元格显示条纹
					singleSelect:true,
					onBeforeLoad:function(param){
						$(this).datagrid('clearChecked').datagrid('clearSelections');
					},
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
					}, '-', {
						text : '<?php echo \think\Lang::get('LANG_APPOINT'); ?>',
						iconCls : 'icon-group_gear',
						handler : function() {
							<?php echo \think\Request::instance()->controller(); ?>PrvlgSetFun();
						}
					},'-']
				">
					<thead>
						<th data-options="field:'id',checkbox:true,width:50"><?php echo \think\Lang::get('LANG_ROLE_ID'); ?></th>
						<th data-options="field:'role_name',width:100"><?php echo \think\Lang::get('LANG_ROLE_ROLENAME'); ?></th>
						<th data-options="field:'dept_name',width:280"><?php echo \think\Lang::get('LANG_ROLE_DEPTNAME'); ?></th>
						<th data-options="field:'type_name2',width:100"><?php echo \think\Lang::get('LANG_ROLE_ROLETYPE'); ?></th>
						<th data-options="field:'type_name3',width:100"><?php echo \think\Lang::get('LANG_ROLE_ROLELAT'); ?></th>
						<th data-options="field:'status_name',width:100"><?php echo \think\Lang::get('LANG_STATUS'); ?></th>
						<th data-options="field:'remark',width:100"><?php echo \think\Lang::get('LANG_REMARK'); ?></th>
					</thead>
				</table>
			</div>
		</div>

	</div>
	<div id="<?php echo \think\Request::instance()->controller(); ?>DiaLog"></div>
</div>
<script>
	function rolefilter(){
		 var dg = $('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid();
            dg.datagrid('removeFilterRule');
			dg.datagrid('enableFilter', [{
								field:'role_name',
								type:'text'
						},{
								field:'dept_name',
								type:'text'
							},{
								field:'type_name2',
								type:'combobox',
								options:{
									panelHeight:'auto',
									url : '<?php echo url('XxgetOption'); ?>',
					                queryParams : { cusCode:'role_type2',filter:1 },
									valueField:'id',
									textField:'text',
									onChange:function(value){
										if (value == ''){
											dg.datagrid('removeFilterRule', 'role_type2');
										} else {
											dg.datagrid('addFilterRule', {
												field: 'role_type2',
												op: 'equal',
												value: value
											});
										}
										dg.datagrid('doFilter');
									}
								}
							},{
								field:'type_name3',
								type:'combobox',
								options:{
									panelHeight:'auto',
									url : '<?php echo url('XxgetOption'); ?>',
					                queryParams : { cusCode:'role_type3',filter:1 },
									valueField:'id',
									textField:'text',
									onChange:function(value){
										if (value == ''){
											dg.datagrid('removeFilterRule', 'role_type3');
										} else {
											dg.datagrid('addFilterRule', {
												field: 'role_type3',
												op: 'equal',
												value: value
											});
										}
										dg.datagrid('doFilter');
									}
								}
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
	}
	$(function(){
		rolefilter();
		$('#<?php echo \think\Request::instance()->controller(); ?>LeftTree').tree({
                 checkbox: false,
                 animate:true,
                 lines:true,
                 url : '<?php echo url('XxgetDeptTree'); ?>',
				 onLoadSuccess:function(node){
					var node = $(this).tree('find', 1);
					$(this).tree('expand',node.target);
				 },
                 onBeforeExpand:function(node,param){
					 $('#<?php echo \think\Request::instance()->controller(); ?>LeftTree').tree('options').url = "/home/tcrole/XxgetDeptTree/id/" + node.id ;
                 },
                onClick:function(node){
                	   // console.log(node.id);
						$('#<?php echo \think\Request::instance()->controller(); ?>DataGrid').datagrid({
							queryParams:{
								id: node.id
							}
						});
						rolefilter();
               }
            });
	});
	
</script>