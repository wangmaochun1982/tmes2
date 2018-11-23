<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:60:"D:\xampp\htdocs\TMES\public/../app/home\view\TpSo\index.html";i:1540781908;}*/ ?>
<div class="easyui-layout" data-options="fit : true,border : false">
	<div data-options="region:'center',border:false"> 
     <table id="__MODULE__DataGrid"
		 data-options="url : '<?php echo url('x_xgetTableJson'); ?>',
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
<script id = "Tpso_index" src="/static/js/index/Tpso/index.js" refreshlang="<?php echo \think\Lang::get('LANG_REFRESH'); ?>" ></script>