<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:61:"D:\xampp\htdocs\TMES\public/../app/home\view\tpso\wkItem.html";i:1540860658;}*/ ?>
<script>
var girows = $('#<?php echo \think\Request::instance()->request('grid'); ?>').datagrid('getRows');  
</script>
<div>
	<form id="__MODULE__wkItemForm" method="post" style="margin: 0;padding:0;">
		<input id="__MODULE__wkgroupCode" name="group_code" type="hidden" />
		<input name="flag" type="hidden" />
		<input  name="index" type="hidden" />
		<input  name="ln_code" id="__MODULE__lnCode" type="hidden" />
		<table class="tableForm" style="white-space: nowrap;width:100%">
			<tr>
				<th><?php echo \think\Lang::get('LANG_MODEL_NAME'); ?>:</th>
				<td>
					<select name="model_name" id="__MODULE__modelName" class="easyui-combogrid" style="width:160px" 
						data-options="panelWidth: 330,
						idField: 'model_name',
						textField: 'model_name',
						url: '__APP__/TcMtModel/x_xgetModelJson/model/<?php echo $_REQUEST['model_name']; ?>', 
						pagination: true,//是否分页  
						rownumbers: true,//序号  
						collapsible: false,//是否可折叠的 
						fit: true,//自动大小  
						mode:'remote',
						pageSize: 10,//每页显示的记录条数，默认为10  
						pageList: [10],//可以设置每页记录条数的列表 									
						method: 'post', 
						required:true,
						onSelect:function(record){
									var row = $('#__MODULE__modelName').combogrid('grid').datagrid('getSelected');
									$('#__MODULE__modelSerial').val(row.model_serial);
									$('#__MODULE__modelSerialOld').val(row.model_serial);
									if(row.model_size){ 
										$('#__MODULE__sizes').val(row.model_size);
										$('#__MODULE__sizesOld').val(row.model_size);
									} 
									if(row.modelno){ 
										$('#__MODULE__modelNo').val(row.modelno);
										$('#__MODULE__modelNoOld').val(row.modelno);
									} 
									if(row.shape_rev){ 
										$('#__MODULE__shapeRev').val(row.shape_rev); 
									} 
								},
						columns: [[
						{field:'model_name',title:'<?php echo \think\Lang::get('LANG_MODEL_NAME'); ?>',width:120},
						{field:'model_serial',title:'<?php echo \think\Lang::get('LANG_MODEL_DESC'); ?>',width:160}
						]]">
				</td>
			</tr>
			<tr>
				<th><?php echo \think\Lang::get('LANG_CUST_MODEL'); ?>:</th>
				<td>
					<input id="__MODULE__custModel" name="cust_model" class="easyui-validatebox" />
				</td>
			</tr>
            <tr>
				<th><?php echo \think\Lang::get('LANG_SO_TARGET_QTY'); ?>:</th>
				<td colspan="3">
					<input name="target_qty" id="__MODULE__stargetQty"  class="easyui-validatebox" />
				</td>
			</tr>   
            <tr>
				<th><?php echo \think\Lang::get('LANG_SO_DEPT1_DESC'); ?>:</th>
				<td colspan="3">
					<input name="dept_1" id="__MODULE__sdept1" class="easyui-combobox" style="width:180px" required="true" data-options=" valueField: 'id', 
																											textField: 'text', 
																											url: '/home/tpso/x_xgetOption?cusCode=area', 
																											panelHeight:'auto'" />
				</td>
			</tr>
			<tr>
				<th><?php echo \think\Lang::get('LANG_SO_FREIGHT_DESC'); ?>:</th>
				<td colspan="3">
					<input name="freight_id" id="__MODULE__sfreightId" class="easyui-combobox" style="width:180px" required="true" data-options=" valueField: 'id', 
																											textField: 'text', 
																											url: '/home/tpso/x_xgetOption?cusCode=freight_id', 
																											panelHeight:'auto'" />
				</td>
			</tr>
			<tr>
				<th><?php echo \think\Lang::get('LANG_SO_PIN_DESC'); ?>:</th>
				<td colspan="3">
					<input name="pin_code" id="__MODULE__spinCode" class="easyui-combobox" style="width:180px" required="true" data-options=" valueField: 'id', 
																											textField: 'text', 
																											url: '/home/tpso/x_xgetOption?cusCode=pin_code', 
																											panelHeight:'auto'" />
				</td>
			</tr>
			<tr>
				<th><?php echo \think\Lang::get('LANG_SO_PROM_DELI_DATE'); ?>:</th>
				<td colspan="3">
					<input name="prom_deli_date" id="__MODULE__promDeliDate" class="easyui-my97" data-options="lang:'zh-cn'" />
				</td>
			</tr>
			<tr>
				<th><?php echo \think\Lang::get('LANG_SO_PROM_SHIP_DATE'); ?>:</th>
				<td colspan="3">
					<input name="prom_ship_date" id="__MODULE__promShipDate" class="easyui-my97" data-options="lang:'zh-cn'" />
				</td>
			</tr>
			<tr>
				<th><?php echo \think\Lang::get('LANG_REMARK'); ?>:</th>
				<td colspan="3">
					
					<textarea name="remark" id="__MODULE__sremark" rows="2" style="width: 70%;"></textarea>
				</td>
			</tr>
        </table> 
	</form> 
</div>