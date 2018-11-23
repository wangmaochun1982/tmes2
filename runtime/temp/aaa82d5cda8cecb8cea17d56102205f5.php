<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:65:"D:\xampp\htdocs\TMES\public/../app/home\view\tcnode\prvlgset.html";i:1541396668;}*/ ?>
<div id="<?php echo \think\Request::instance()->controller(); ?>SetAppoint" class="easyui-tabs"  style="width:auto;height:494px;" data-options="onSelect:<?php echo \think\Request::instance()->controller(); ?>TabSelectFun" >
	<div title="<?php echo \think\Lang::get('LANG_NODE_HVROLE'); ?>" class="easyui-layout" data-options="fit:true,border:false">
		<div data-options="region:'center',border:false,title:'<?php echo \think\Lang::get('LANG_NODE_HVROLE'); ?>'" style="height:460px">
			<table class="easyui-datagrid" id="<?php echo \think\Request::instance()->controller(); ?>ExtendGrig"
				data-options="
					url:'<?php echo url('Xxroleactionlist'); ?>',
					queryParams :{ node_id:'<?php echo \think\Request::instance()->param('node_id'); ?>' },
					fit : true,
					border : false,
					rownumbers:true,
					pagination : true,
					idField : 'id',
					pageSize :15,
					pageList : [15,20,30,40,50],
					sortName : 'role_name',
					sortOrder : 'asc',
					checkOnSelect : true,
					selectOnCheck : true,
					toolbar:'#<?php echo \think\Request::instance()->controller(); ?>ExtendTool',
					nowrap : true,
					singleSelect:false">
				<thead>
					<tr>
						<th data-options="field:'role_name',width:100"><?php echo \think\Lang::get('LANG_NODE_ROLENAME'); ?></th>
						<volist id="columnList" name="columnList">
						<th data-options="field:'<?php echo $columnList['remark']; ?>',width:100"><?php echo $columnList['remark']; ?></th>
					    </volist>
					</tr>
				</thead>
			</table>
		</div>

	</div>
	<?php if(\think\Request::instance()->param('nodeType') == '2'): ?>
	<div title="<?php echo \think\Lang::get('LANG_NODE_NDAP'); ?>" data-options="border:false" style="width:auto;height:490px" >
		<div class="easyui-layout" data-options="fit:true,border:false" >
			<div data-options="region:'west',title:'<?php echo \think\Lang::get('LANG_NODE_NHVACTION'); ?>'" style="width:410px;height:400px; padding:1px">
				<table id="<?php echo \think\Request::instance()->controller(); ?>ActionLeftGrid" style="height:400px;"
									data-options="
									url:'<?php echo url('/home/Tcaction/Xxgettablejson'); ?>',
					                queryParams :{ node_lnk:1,node_id:'<?php echo \think\Request::instance()->param('node_id'); ?>' },
									fit : true,
									//border : false,//定义边框
									//pagination : true,//定义分页组件
									//pageSize : 100,
									//pageList : [100,200,500],
									remoteFilter: false,//不使用後臺查找
									rownumbers:true,
									sortName : 'action_code',
									sortOrder : 'asc',
									checkOnSelect : true,//选择checkbox则选择行
									selectOnCheck : true,//选择行则选择checkbox
									striped:true ">
					<thead>
						<tr>
							<th data-options="field:'id',width:20,checkbox:true" sortable="true">ID</th>
							<th data-options="field:'action_code',width:100"><?php echo \think\Lang::get('LANG_NODE_ACTIONCODE'); ?></th>
							<th data-options="field:'lang_value',width:100"><?php echo \think\Lang::get('LANG_NODE_ACTIONNAME'); ?></th>
							<th data-options="field:'remark',width:130"><?php echo \think\Lang::get('LANG_REMARK'); ?></th>
						</tr>
					</thead>
				</table>
			</div>
			<div data-options="region:'east',border:false,title:'<?php echo \think\Lang::get('LANG_NODE_HVACTION'); ?>'" style="width:410px;height:420px; padding:1px" >
				<table class="easyui-datagrid" id="<?php echo \think\Request::instance()->controller(); ?>ActionRightGrid" style="height:410px;"
								data-options="
									idField : 'id',
									url:'<?php echo url('/home/Tcaction/Xxgettablejson'); ?>',
					                queryParams :{ node_lnk:2,node_id:'<?php echo \think\Request::instance()->param('node_id'); ?>' },
									fit : true,
									rownumbers:true,
									sortName : 'action_code',
									checkOnSelect : true,//选择checkbox则选择行
									selectOnCheck : true">
						<thead>
							<tr>
								<th data-options="field:'id',width:20,checkbox:true" sortable="true">ID</th>
								<th data-options="field:'action_code',width:100"><?php echo \think\Lang::get('LANG_NODE_ACTIONCODE'); ?></th>
								<th data-options="field:'lang_value',width:100"><?php echo \think\Lang::get('LANG_NODE_ACTIONNAME'); ?></th>
								<th data-options="field:'remark',width:130"><?php echo \think\Lang::get('LANG_REMARK'); ?></th>
							</tr>
						</thead>
					</table>
			</div>
			<div data-options="region:'center'" style="width:50px;height:390px" >
				<div style="padding-top:100px" align="center" >
					<a href="javascript:void(0);" class="easyui-linkbutton" onclick="<?php echo \think\Request::instance()->controller(); ?>AddAction('<?php echo \think\Request::instance()->controller(); ?>ActionLeftGrid','<?php echo \think\Request::instance()->controller(); ?>ActionRightGrid');" data-options="iconCls:'icon-forward',iconAlign:'bottom'">add</a>
				 </div>
				<div style="padding-top:100px" align="center" >
					<a href="javascript:void(0);" class="easyui-linkbutton" onclick="<?php echo \think\Request::instance()->controller(); ?>AddAction('<?php echo \think\Request::instance()->controller(); ?>ActionRightGrid','<?php echo \think\Request::instance()->controller(); ?>ActionLeftGrid');" data-options="iconCls:'icon-backward',iconAlign:'bottom'">&nbsp;del&nbsp;</a>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<div title="<?php echo \think\Lang::get('LANG_NODE_ROLEAP'); ?>" data-options="border:false" style="width:auto;height:490px" >
		<div class="easyui-layout" data-options="fit:true,border:false" >
			<div data-options="region:'west',border:false,title:'<?php echo \think\Lang::get('LANG_NODE_ACTIONLIST'); ?>'" style="width:200px;height:420px; padding:1px" >
				<table class="easyui-datagrid" id="<?php echo \think\Request::instance()->controller(); ?>ActionGrid" style="height:410px;"
							data-options="
								url:'<?php echo url('/home/Tcaction/Xxgettablejson'); ?>',
					            queryParams :{ node_lnk:2,node_id:'<?php echo \think\Request::instance()->param('node_id'); ?>' },
								fit : true,
								border : false,
								rownumbers:true,
								pagination : false,
								idField : 'id',
								pageSize :15,
								pageList : [15,20,30,40,50],
								sortName : 'action_code',
								sortOrder : 'asc',
								checkOnSelect : true,
								selectOnCheck : true,
								nowrap : true,
								singleSelect:false,
								onSelect:<?php echo \think\Request::instance()->controller(); ?>ReGetRole,
								onUnselect:<?php echo \think\Request::instance()->controller(); ?>ReGetRole">
					<thead>
						<tr>
							<th data-options="field:'id',width:20,checkbox:true" sortable="true">ID</th>
							<th data-options="field:'remark',width:130"><?php echo \think\Lang::get('LANG_REMARK'); ?></th>
						</tr>
					</thead>
				</table>
			</div>
			<div data-options="region:'center',border:false" style="width:410px;height:420px; padding:1px" >
				<div class="easyui-layout" data-options="fit:true,border:false" >
					<div data-options="title:'<?php echo \think\Lang::get('LANG_NODE_HAB'); ?>',region:'west'" style="width:240px;padding:0px">
						 <ul id="<?php echo \think\Request::instance()->controller(); ?>DepartmentTree"></ul>
					</div>
					<div data-options="region:'center'" style="width:400px;">
						<div class="easyui-layout" data-options="fit:true,border:false" >
							<div data-options="region:'north'" style="height:210px;">
								<table class="easyui-datagrid" id="<?php echo \think\Request::instance()->controller(); ?>RoldGrid" data-options="
									url:'<?php echo url('Xxgetpartrole'); ?>',
									title:'<?php echo \think\Lang::get('LANG_NODE_ROLELIST'); ?>',
									fit : true,
									border : false,
									rownumbers:true,
									pagination : false,
									idField : 'id',
									pageSize :10,
									pageList : [10,15,20,30,40,50],
									sortName : 'id',
									sortOrder : 'asc',
									checkOnSelect : true,
									selectOnCheck : true,
									nowrap : true,
									singleSelect:false
								">
									<thead>
										<th data-options="field:'id',checkbox:true,width:50">編號</th>
										<th data-options="field:'name',width:80"><?php echo \think\Lang::get('LANG_NODE_ROLENAME'); ?></th>
										<th data-options="field:'org_code',width:100,hidden:true"><?php echo \think\Lang::get('LANG_NODE_DEPT'); ?>ID</th>
										<th data-options="field:'dept_name',width:200"><?php echo \think\Lang::get('LANG_NODE_DEPT'); ?></th>
										<th data-options="field:'remark',width:100"><?php echo \think\Lang::get('LANG_REMARK'); ?></th>
									</thead>
								</table>
							</div>
							<div data-options="region:'center'" style="width:400px;" align="center">
								<a href="javascript:void(0);" class="easyui-linkbutton" onclick="<?php echo \think\Request::instance()->controller(); ?>AddAction('<?php echo \think\Request::instance()->controller(); ?>RoldGrid','<?php echo \think\Request::instance()->controller(); ?>RoldAppoint');" data-options="iconCls:'icon-downward',iconAlign:'bottom'"></a>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<a href="javascript:void(0);" class="easyui-linkbutton" onclick="<?php echo \think\Request::instance()->controller(); ?>AddAction('<?php echo \think\Request::instance()->controller(); ?>RoldAppoint','<?php echo \think\Request::instance()->controller(); ?>RoldGrid');" data-options="iconCls:'icon-upward',iconAlign:'bottom'"></a>
							</div>
							<div data-options="region:'south'" style="height:210px;">
								<table class="easyui-datagrid" id="<?php echo \think\Request::instance()->controller(); ?>RoldAppoint" data-options="fit:true">
									<thead>
										<th data-options="field:'id',checkbox:true,width:50">編號</th>
										<th data-options="field:'name',width:80"><?php echo \think\Lang::get('LANG_NODE_ROLENAME'); ?></th>
										<th data-options="field:'org_code',width:100,hidden:true"><?php echo \think\Lang::get('LANG_NODE_DEPT'); ?>ID</th>
										<th data-options="field:'dept_name',width:200"><?php echo \think\Lang::get('LANG_NODE_DEPT'); ?></th>
										<th data-options="field:'remark',width:100"><?php echo \think\Lang::get('LANG_REMARK'); ?></th>
									</thead>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	//組織+角色的樹
	 $(function(){
		$('#<?php echo \think\Request::instance()->controller(); ?>Selections').tree({
                 checkbox: true,
                 animate:true,
                 lines:true,
				 onlyLeafCheck:true,
                 url: '<?php echo url('XxGetAllRole'); ?>',
            });
			var dg = $('#<?php echo \think\Request::instance()->controller(); ?>ActionLeftGrid').datagrid();
			dg.datagrid('removeFilterRule');
			dg.datagrid('enableFilter',[{field:'action_code',
						 type:'text'
						},
						{field:'lang_value',
						 type:'text'
						},
						{field:'remark',
						 type:'text'
						}
			]);
		$('#<?php echo \think\Request::instance()->controller(); ?>DepartmentTree').tree({
			checkbox: false,
			animate:true,
			lines:true,
			url: '<?php echo url('XxgetDeptTree'); ?>',
			onClick:function(node){
				var node_id ='<?php echo $node_id; ?>';
				var actionArr = [];
				var actionRows = $('#<?php echo \think\Request::instance()->controller(); ?>ActionGrid').datagrid('getChecked');
				var org = $('#<?php echo \think\Request::instance()->controller(); ?>DepartmentTree').tree('getSelected');
				for ( var i = 0; i < actionRows.length; i++) {
					actionArr.push(actionRows[i].id);
				}
				$('#<?php echo \think\Request::instance()->controller(); ?>RoldGrid').datagrid({
				    url: '<?php echo url('XxgetPartRole'); ?>',
					queryParams:{
						id: node_id,
						orgCode: org.id,
						actionList:actionArr
					},
					striped:true
				});
		    }
		});
	});
	function <?php echo \think\Request::instance()->controller(); ?>ReGetRole(){
		var node_id ='<?php echo $node_id; ?>';
		var actionArr = [];
		var orgId=1;
		var actionRows = $('#<?php echo \think\Request::instance()->controller(); ?>ActionGrid').datagrid('getChecked');
		var org = $('#<?php echo \think\Request::instance()->controller(); ?>DepartmentTree').tree('getSelected');
		for ( var i = 0; i < actionRows.length; i++) {
			actionArr.push(actionRows[i].id);
		}
		if (org!=null) orgId=org.id;
		$('#<?php echo \think\Request::instance()->controller(); ?>RoldGrid').datagrid({
			url: '<?php echo url('XxgetPartRole'); ?>',
			queryParams:{
				id: node_id,
				orgCode: orgId,
				actionList:actionArr
			},
			striped:true
		});
		$('#<?php echo \think\Request::instance()->controller(); ?>RoldAppoint').datagrid({
			url: '<?php echo url('XxgetRoleAppoint'); ?>',
			queryParams:{
				id: node_id,
				actionList:actionArr
			},
			striped:true
		});
	};
	//點擊將左邊的角色移到右邊
	function <?php echo \think\Request::instance()->controller(); ?>AddSelect() {
		var rowData = $('#<?php echo \think\Request::instance()->controller(); ?>Selections').tree('getChecked');
		//alert(rowData.length);
		for ( var i = 0; i < rowData.length; i++) {
			//alert(rowData[i].id);
			//alert(rowData[i].role_name);
			//delete rowData[i].rn;
			//alert($('#<?php echo \think\Request::instance()->controller(); ?>SeachGrid').datagrid('getRowIndex',rowData[i].id)==-1);

			if($('#<?php echo \think\Request::instance()->controller(); ?>SeachGrid').datagrid('getRowIndex',rowData[i].id)==-1){
				$('#<?php echo \think\Request::instance()->controller(); ?>SeachGrid').datagrid('insertRow',{
					index:i,
					row:{
						id : rowData[i].id,
						role_name :rowData[i].text,
						remark :rowData[i].attributes.remark,
					}
				});
			}//$('#<?php echo \think\Request::instance()->controller(); ?>Selections').tree('remove', rowData[i].target);
		}
		$('#<?php echo \think\Request::instance()->controller(); ?>Selections').datagrid('clearChecked');
	}

	//將右邊的角色刪除
	function <?php echo \think\Request::instance()->controller(); ?>DelSelect(){
		var rows = $('#<?php echo \think\Request::instance()->controller(); ?>SeachGrid').datagrid('getChecked');
		var rowLen=rows.length;
		if (rowLen > 0) {
			for ( var i = 0; i < rowLen; i++) {
				var rowIndex = $('#<?php echo \think\Request::instance()->controller(); ?>SeachGrid').datagrid('getRowIndex',rows[0]);
				if(rowIndex>-1){
					$('#<?php echo \think\Request::instance()->controller(); ?>SeachGrid').datagrid('deleteRow',rowIndex);
					rows = $('#<?php echo \think\Request::instance()->controller(); ?>SeachGrid').datagrid('getChecked');
				}
			}
			$('#<?php echo \think\Request::instance()->controller(); ?>SeachGrid').datagrid('clearChecked');
		}else{
			$.messager.show({
					title : '<?php echo \think\Lang::get('LANG_TIPS'); ?>',
					msg : '<?php echo \think\Lang::get('LANG_DEL_CONFIRM'); ?>'
				});
		}
	}

	function <?php echo \think\Request::instance()->controller(); ?>Action(value,row,index){
		//alert(row.frole_id);
		//alert(row.brole_id);
		var d = "<a href=\"javascript:void(0);\" onclick=\"<?php echo \think\Request::instance()->controller(); ?>Step('"+row.id+"','"+row.node_id+"')\">"+'<?php echo \think\Lang::get('LANG_CANCEL'); ?>'+"</a>";
		return d;
	}
	function <?php echo \think\Request::instance()->controller(); ?>Step(role_id,node_id){
		//alert(role_id);
		//alert(node_id);
		$.ajax({
			url: '<?php echo url('Xxcancelappoint'); ?>',
			type:'post',
			data:{role_id:role_id,node_id:node_id},
			dataType:'json',
			success:function(r){
				if(r.status==1){
					$('#<?php echo \think\Request::instance()->controller(); ?>ExtendGrig').datagrid('reload');
					$('#<?php echo \think\Request::instance()->controller(); ?>ReExtendGrig').datagrid('reload');
					$.messager.show({
						title:'<?php echo \think\Lang::get('LANG_TIPS'); ?>',
						msg:r.info
					});
				}else{
					$.messager.show({
						title:'<?php echo \think\Lang::get('LANG_TIPS'); ?>',
						msg:r.info
					});
				}
			}
		});

	}
	function <?php echo \think\Request::instance()->controller(); ?>AddAction(t1,t2) {
		var j_id1='#'+t1;
		var j_id2='#'+t2;
		var rowData = $(j_id1).datagrid('getChecked');
		var rowLen=rowData.length;
        //console.info(rowData);
		//var rowDiff = [];
		for ( var i = 0; i < rowLen; i++) {
			var rowIndex = $(j_id1).datagrid('getRowIndex',rowData[0]);
            //alert(rowData[0].rn);
			if($(j_id2).datagrid('getRowIndex',rowData[0].id)==-1){
				$(j_id2).datagrid('insertRow',{row:rowData[0]});
				//rowDiff.push(rowData[0].id);
			}
			$(j_id1).datagrid('deleteRow',rowIndex);
			rowData = $(j_id1).datagrid('getChecked');
		}
	}
	//將右邊的角色刪除
	function <?php echo \think\Request::instance()->controller(); ?>DelAction(){
		var rowData = $('#<?php echo \think\Request::instance()->controller(); ?>ActionRightGrid').datagrid('getChecked');
		var rowLen=rowData.length;
		for ( var i = 0; i < rowLen; i++) {
			var rowIndex = $('#<?php echo \think\Request::instance()->controller(); ?>ActionRightGrid').datagrid('getRowIndex',rowData[0].id);
			$('#<?php echo \think\Request::instance()->controller(); ?>ActionRightGrid').datagrid('deleteRow',rowIndex);
			rowData = $('#<?php echo \think\Request::instance()->controller(); ?>ActionRightGrid').datagrid('getChecked');
		}
		$('#<?php echo \think\Request::instance()->controller(); ?>ActionRightGrid').datagrid('clearChecked');
	}
	function <?php echo \think\Request::instance()->controller(); ?>AddActionSearchFun(j_search,j_grid) {
		var j_txt = $('#'+j_search).val();
		//alert(j_txt);
		$('#'+j_grid).datagrid('reload', {sear_code: j_txt});
	}

	function <?php echo \think\Request::instance()->controller(); ?>TabSelectFun(title,index){
		if(title=='<?php echo \think\Lang::get('LANG_NODE_HVROLE'); ?>'){
			$('#<?php echo \think\Request::instance()->controller(); ?>ApSave').hide();
		}else{
			//$('#<?php echo \think\Request::instance()->controller(); ?>ApSave>span>span:nth-child(1)').html("<?php echo \think\Lang::get('LANG_SAVE'); ?>");
			$('#<?php echo \think\Request::instance()->controller(); ?>ApSave').show();
		}
	}
</script>

