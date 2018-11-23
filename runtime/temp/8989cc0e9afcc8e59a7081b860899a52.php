<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:61:"D:\xampp\htdocs\TMES\public/../app/home\view\tcnode\edit.html";i:1541383978;}*/ ?>
<script type="text/javascript">
	$(function() {
		$('#<?php echo \think\Request::instance()->controller(); ?>NodeIconCom').combobox({
			url : '<?php echo url('XxgetIconJson'); ?>',
			formatter : function(row) {
				if(row.value=='blank'){
					return;
				}else{
					var imageFile = '/static/easyui/themes/icons/' + row.value+'.png';
					return '<img class="item-img" src="'+imageFile+'"/><span class="item-text">'+row.text+'</span>';
				}
			}
		});
	});
</script>
<div align="center">
<form id="<?php echo \think\Request::instance()->controller(); ?>EditForm" method="post">
<input type="hidden" name="id" />
		<table class="tableForm">
            <tr>
				<th><?php echo \think\Lang::get('LANG_NODE_PNAME'); ?></th>
				<td colspan="3">
                <input id="<?php echo \think\Request::instance()->controller(); ?>AddComTree" class="easyui-combotree" name="pid" style="width:200px;"
							data-options="url:'<?php echo url('Xxgetnodetree'); ?>',
							              queryParams :{ node_type:1 },
										  onLoadSuccess:function(node){
											var node = $(this).tree('find', 1);
											$(this).tree('expand',node.target);
										  },
										  onSelect : function (node){
											var levelId = node.attributes.node_level*1+1;
											$('<?php echo \think\Request::instance()->controller(); ?>nlev').val(levelId);
										  }" />
				</td>
			</tr>
			<tr>
				<th><?php echo \think\Lang::get('LANG_NODE_NAME'); ?></th>
				<td><input name="node_name" class="easyui-validatebox" data-options="required:true" style="width:130px;" />
				</td>
				<th><?php echo \think\Lang::get('LANG_NODE_CODE'); ?></th>
				<td><input name="node_code" class="easyui-validatebox" data-options="required:true" />
				</td>
			</tr>
			<tr>
				<th><?php echo \think\Lang::get('LANG_NODE_SORT'); ?></th>
				<td><input name="node_sort" class="easyui-numberspinner" data-options="min:0,max:999,editable:true,required:true,missingMessage:'<?php echo \think\Lang::get('LANG_NODE_PLS_MSG'); ?>'" value="10" style="width: 155px;" />
				</td>
				<th><?php echo \think\Lang::get('LANG_NODE_ICON'); ?></th>
				<td><input id="<?php echo \think\Request::instance()->controller(); ?>NodeIconCom"  name="node_icon" style="width: 200px;" />
				</td>
			</tr>
			<tr>
				<th><?php echo \think\Lang::get('LANG_NODE_ACTION'); ?></th>
				<td><input id="node_type" class="easyui-combobox"  name="node_type"
				              data-options="valueField: 'id',
                                            textField: 'text',
											editable:false,
                                            url:'<?php echo url('XxgetOption'); ?>',
							                queryParams :{ cusCode:'node_type' },
										    onSelect : function(row){
												if(row.id==2){
													$('#ismenu').combobox('setValue',0);
													$('#ismenu').combobox('readonly',true);
												}else{
													$('#ismenu').combobox('readonly',false);
												}
										   },
                                            panelHeight:'auto'" />
				</td>
				<th><?php echo \think\Lang::get('LANG_STATUS'); ?></th>
				<td><input id="status" class="easyui-combobox"  name="status"
							  data-options="valueField: 'id',
                                            textField: 'text',
											editable:false,
                                            url:'<?php echo url('XxgetOption'); ?>',
							                queryParams :{ cusCode:'status' },
                                            panelHeight:'auto'" />
				</td>
			</tr>
			<tr >
				<th><?php echo \think\Lang::get('LANG_NODE_ISMENU'); ?></th>
				<td><input id="ismenu" class="easyui-combobox"  name="ismenu"
							  data-options="valueField: 'id',
                                            textField: 'text',
											editable:false,
                                            url:'<?php echo url('Xxgetoption'); ?>',
							                queryParams :{ cusCode:'ismenu' },
                                            panelHeight:'auto'" />
				</td>
				<th><?php echo \think\Lang::get('LANG_NODE_ISSHOW'); ?></th>
				<td><input class="easyui-combobox"  name="isshow"
							  data-options="valueField: 'id',
                                            textField: 'text',
											editable:false,
                                            url:'<?php echo url('Xxgetoption'); ?>',
							                queryParams :{ cusCode:'ismenu' },
                                            panelHeight:'auto'" /> </td>
			</tr>
			<tr>
				<th><?php echo \think\Lang::get('LANG_REMARK'); ?></th>
				<td colspan="3"><textarea name="remark" style="width:390px;height:80px;"></textarea>
				</td>
			</tr>
			<input type="hidden" id="<?php echo \think\Request::instance()->controller(); ?>nlev"  name="node_level">
		</table>
	</form>
 </div>