$(function(){
        $('#layout_west_tree').tree({
            url: '/home/Pub/menu',
            parentField: 'pid',
            lines: false,
            onClick: function(node){
                if(node.attributes.ismenu!=1) {
					var url;
					if (node.attributes.node_code && node.attributes.node_type==2 ) {
						/*模塊*/
						url =  '/home/' + node.attributes.node_code;
					}else if (node.attributes.node_code && node.attributes.node_type==1 ) {
						/*應用*/
						url = '/home/' + node.attributes.node_code;
					}
                    layout_center_addTabFun({
                        title: node.title,
                        closable: true,
                        iconCls: node.iconCls,
                        href: url
                    });
                }else{
					if(node.state=='closed')
						$(this).tree('expand',node.target);
					else
						$(this).tree('collapse',node.target);
				}
            },
            onContextMenu:function(e,node){
                e.preventDefault();
				if(node.attributes.ismenu!=1) {
					$(this).tree('select',node.target);
				}
				$('#layout_leftTree_tabsMenu').menu('show', {
					left : e.pageX,
					top : e.pageY
				}).data('tabTitle', '右键菜单');
            },
            onLoadSuccess:function(e,row){
                addTreeTitle(row);//显示title
                //closedNode(); //打开这句则默认所有节点收缩
            }
        });
    });
    //增加树的title属性 by lhp 2013-09-23//遍历节点//使用Jquery选择器设置title//如果还有子级则继续遍历
    function addTreeTitle(row){
        $.each(row,function(idx,val){
            $("[node-id='"+val.id+"']").attr('title',val.name+'->'+val.title);
            if(val.children){
                addTreeTitle(val.children);
            }
        });
    }
    function layout_center_addTab(url, title){
        layout_center_addTabFun({
            title: title,
            closable: true,
            href: url,
        });
    }
    //收缩节点 by lhp 2013-09-18
    function closedNode(){
        var node = $('#layout_west_tree').tree('getSelected');
		 if (node) {
			$('#layout_west_tree').tree('collapseAll',  node.target);
		 }  else {
			$('#layout_west_tree').tree('collapseAll');
		 }
    }
    //展开节点 by lhp 2013-09-18
    function openNode(){
        var node = $('#layout_west_tree').tree('getSelected');
		 if (node) {
			$('#layout_west_tree').tree('expandAll',  node.target);
		 }  else {
			$('#layout_west_tree').tree('expandAll');
		 }
    }
    //属性菜单右键菜单 by lhp 2013-09-23
    $('#layout_leftTree_tabsMenu').menu({
			onClick : function(item) {
				var curTabTitle = $(this).data('tabTitle');
				var type = $(item.target).attr('type');
				if(type === 'reLoad') { $('#layout_west_tree').tree('reload');return;}
				if(type === 'collapse') { closedNode();return;	}
                if(type === 'expand') { openNode(); return; }
                if(type === 'opennode') { openNode(); return; }
                if(type === 'addFavorite'){
                    var node = $('#layout_west_tree').tree('getSelected');
                    if(node){
                        $.messager.progress({
                            title:'加入我的最爱',
                            msg:'功能正在建设中,5秒后关闭本窗口...'
                        });
                        setTimeout(function(){
                            $.messager.progress('close');
                        },5000);
                    }else{
                        $.messager.show({
                            title:'提示',
                            msg:'请先选择菜单,然后再点击加入我的最爱！',
                            showType:'show',
                            timeout:3000
                        });
                    }
                }
			}
		});