/**
 * panel关闭时回收内存，主要用于layout使用iframe嵌入网页时的内存泄漏问题
 */
var dg_pageList = [5,10,15,20,60,100,200,1000];
$.fn.panel.defaults.onBeforeDestroy = function() {
	var frame = $('iframe', this);
	try {
		if (frame.length > 0) {
			for ( var i = 0; i < frame.length; i++) {
				frame[i].contentWindow.document.write('');
				frame[i].contentWindow.close();
			}
			frame.remove();
			if ($.browser.msie) {
				CollectGarbage();
			}
		}
	} catch (e) {
	}
};
/**
 * 使panel和datagrid在加载时提示
 *
 */
$.fn.panel.defaults.loadingMessage = '加载中....';
$.fn.datagrid.defaults.loadMsg = '加载中....';

var formatDate = function (dateunix,fatStr){
    if(!dateunix || dateunix==0) return;
    var formats = fatStr?fatStr:'yyyy-MM-dd hh:mm:ss';
    var d = new Date(dateunix*1000);
    var o = {
        "M+": d.getMonth() + 1, //month
        "d+": d.getDate(),    //day
        "h+": d.getHours(),   //hour
        "m+": d.getMinutes(), //minute
        "s+": d.getSeconds(), //second
        "q+": Math.floor((d.getMonth() + 3) / 3),  //quarter
        "S": d.getMilliseconds() //millisecond
    }
    if (/(y+)/.test(formats)) formats = formats.replace(RegExp.$1,(d.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o){
        if (new RegExp("(" + k + ")").test(formats)){
            formats = formats.replace(RegExp.$1,RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
        }
    }
    return formats;
}
/**
 *
 * 通用错误提示
 *
 * 用于datagrid/treegrid/tree/combogrid/combobox/form加载数据出错时的操作
*/
var easyuiErrorFunction = function(XMLHttpRequest) {
	$.messager.progress('close');
	$.messager.alert('错误', XMLHttpRequest.responseText);
};
$.fn.datagrid.defaults.onLoadError = easyuiErrorFunction;
$.fn.treegrid.defaults.onLoadError = easyuiErrorFunction;
$.fn.tree.defaults.onLoadError = easyuiErrorFunction;
$.fn.combogrid.defaults.onLoadError = easyuiErrorFunction;
$.fn.combobox.defaults.onLoadError = easyuiErrorFunction;
$.fn.form.defaults.onLoadError = easyuiErrorFunction;

(function($){
	$.fn.extend({
		insertAtCaret: function(myValue){
			var $t=$(this)[0];
			if (document.selection) {
				this.focus();
				sel = document.selection.createRange();
				sel.text = myValue;
				this.focus();
			}else{
				if ($t.selectionStart || $t.selectionStart == '0') {
					var startPos = $t.selectionStart;
					var endPos = $t.selectionEnd;
					var scrollTop = $t.scrollTop;
					$t.value = $t.value.substring(0, startPos) + myValue + $t.value.substring(endPos, $t.value.length);
					this.focus();
					$t.selectionStart = startPos + myValue.length;
					$t.selectionEnd = startPos + myValue.length;
					$t.scrollTop = scrollTop;
				}else {
					this.value += myValue;
					this.focus();
				}
			}
		},
		setCursorPosition : function(position){
			if(this.lengh == 0) return this;
			return $(this).setSelection(position, position);
		},
		setSelection : function(selectionStart, selectionEnd) {
			if(this.lengh == 0) return this;
			input = this[0];

			if (input.createTextRange) {
				var range = input.createTextRange();
				range.collapse(true);
				range.moveEnd('character', selectionEnd);
				range.moveStart('character', selectionStart);
				range.select();
			} else if (input.setSelectionRange) {
				input.focus();
				input.setSelectionRange(selectionStart, selectionEnd);
			}

			return this;
		} ,
		focusEnd : function(){
			this.setCursorPosition(this.val().length);
		}
	});
})(jQuery);
/**
 *
 * 为datagrid、treegrid增加表头菜单，用于显示或隐藏列，注意：冻结列不在此菜单中

var createGridHeaderContextMenu = function(e, field) {
	e.preventDefault();
	var grid = $(this);//grid本身
	var headerContextMenu = this.headerContextMenu;//grid上的列头菜单对象
	if (!headerContextMenu) {
		var tmenu = $('<div style="width:100px;"></div>').appendTo('body');
		var fields = grid.datagrid('getColumnFields');
		for ( var i = 0; i < fields.length; i++) {
			var fildOption = grid.datagrid('getColumnOption', fields[i]);
			if (!fildOption.hidden) {
				$('<div iconCls="icon-ok" field="' + fields[i] + '"/>').html(fildOption.title).appendTo(tmenu);
			} else {
				$('<div iconCls="icon-empty" field="' + fields[i] + '"/>').html(fildOption.title).appendTo(tmenu);
			}
		}
		headerContextMenu = this.headerContextMenu = tmenu.menu({
			onClick : function(item) {
				var field = $(item.target).attr('field');
				if (item.iconCls == 'icon-ok') {
					grid.datagrid('hideColumn', field);
					$(this).menu('setIcon', {
						target : item.target,
						iconCls : 'icon-empty'
					});
				} else {
					grid.datagrid('showColumn', field);
					$(this).menu('setIcon', {
						target : item.target,
						iconCls : 'icon-ok'
					});
				}
			}
		});
	}
	headerContextMenu.menu('show', {
		left : e.pageX,
		top : e.pageY
	});
};
$.fn.datagrid.defaults.onHeaderContextMenu = createGridHeaderContextMenu;
$.fn.treegrid.defaults.onHeaderContextMenu = createGridHeaderContextMenu;
*/
/**
 *
 * 扩展validatebox，添加验证两次密码功能
 */
$.extend($.fn.validatebox.defaults.rules, {
	eqPwd : {
		validator : function(value, param) {
			return value == $(param[0]).val();
		},
		message : '密码不一致！'
	}
});

/**
 *
 * 扩展tree，使其支持平滑数据格式
 */
$.fn.tree.defaults.loadFilter = function(data, parent) {
	var opt = $(this).data().tree.options;
	var idFiled, textFiled, parentField;
	if (opt.parentField) {
		idFiled = opt.idFiled || 'id';
		textFiled = opt.textFiled || 'title';
		parentField = opt.parentField;
		var i, l, treeData = [], tmpMap = [];
		for (i = 0, l = data.length; i < l; i++) {
			tmpMap[data[i][idFiled]] = data[i];
		}
		for (i = 0, l = data.length; i < l; i++) {
			if (tmpMap[data[i][parentField]] && data[i][idFiled] != data[i][parentField]) {
				if (!tmpMap[data[i][parentField]]['children'])
					tmpMap[data[i][parentField]]['children'] = [];
				data[i]['text'] = data[i][textFiled];
				tmpMap[data[i][parentField]]['children'].push(data[i]);
			} else {
				data[i]['text'] = data[i][textFiled];
				treeData.push(data[i]);
			}
		}
		return treeData;
	}
	return data;
};

/**
 *
 * 扩展treegrid，使其支持平滑数据格式
 */
$.fn.treegrid.defaults.loadFilter = function(data, parentId) {
	var opt = $(this).data().treegrid.options;
	var idFiled, textFiled, parentField;
	if (opt.parentField) {
		idFiled = opt.idFiled || 'id';
		textFiled = opt.textFiled || 'title';
		parentField = opt.parentField;
		var i, l, treeData = [], tmpMap = [];
		for (i = 0, l = data.length; i < l; i++) {
			tmpMap[data[i][idFiled]] = data[i];
		}
		for (i = 0, l = data.length; i < l; i++) {
			if (tmpMap[data[i][parentField]] && data[i][idFiled] != data[i][parentField]) {
				if (!tmpMap[data[i][parentField]]['children'])
					tmpMap[data[i][parentField]]['children'] = [];
				data[i]['text'] = data[i][textFiled];
				tmpMap[data[i][parentField]]['children'].push(data[i]);
			} else {
				data[i]['text'] = data[i][textFiled];
				treeData.push(data[i]);
			}
		}
		return treeData;
	}
	return data;
};

/**
 *
 * 扩展combotree，使其支持平滑数据格式
 */
$.fn.combotree.defaults.loadFilter = $.fn.tree.defaults.loadFilter;

/**
 * 防止panel/window/dialog组件超出浏览器边界
 * @param left
 * @param top
*/
var easyuiPanelOnMove = function(left, top) {
	var l = left;
	var t = top;
	if (l < 1) {
		l = 1;
	}
	if (t < 1) {
		t = 1;
	}
	var width = parseInt($(this).parent().css('width')) + 14;
	var height = parseInt($(this).parent().css('height')) + 14;
	var right = l + width;
	var buttom = t + height;
	var browserWidth = $(window).width();
	var browserHeight = $(window).height();
	if (right > browserWidth) {
		l = browserWidth - width;
	}
	if (buttom > browserHeight) {
		t = browserHeight - height;
	}
	$(this).parent().css({// 修正面板位置
		left : l,
		top : t
	});
};
$.fn.dialog.defaults.onMove = easyuiPanelOnMove;
$.fn.window.defaults.onMove = easyuiPanelOnMove;
$.fn.panel.defaults.onMove = easyuiPanelOnMove;

/**
 *
 * 更换EasyUI主题的方法
 *
 * @param themeName
 *            主题名称
 */
changeTheme = function(themeName) {
	var $easyuiTheme = $('#easyuiTheme');
	var url = $easyuiTheme.attr('href');
	var href = url.substring(0, url.indexOf('themes')) + 'themes/' + themeName + '/easyui.css';
	$easyuiTheme.attr('href', href);

	var $iframe = $('iframe');
	if ($iframe.length > 0) {
		for ( var i = 0; i < $iframe.length; i++) {
			var ifr = $iframe[i];
			$(ifr).contents().find('#easyuiTheme').attr('href', href);
		}
	}

	$.cookie('easyuiThemeName', themeName, {
		expires : 30,path: '/'
	});
};

/*
 *
 * 更换EasyUI语言的方法
 *
 * @param langName
 *            语言包名称
 */
changeLang = function(langName) {
	//alert(langName);
    $.messager.confirm('警告', '更换当前语言包将会导致页面重载，请确认是否执行切换?', function(r){
            $.cookie('easyuiLangType', '', {
        		expires : -1
        	});
        if (r){
        	var $easyuiLang = $('#easyuiLang');
        	var url = $easyuiLang.attr('src');
        	var src = url.substring(0, url.indexOf('locale')) + 'locale/easyui-lang-' + langName + '.js';
        	$easyuiLang.attr('src', src);
        	//alert(src);
        	$.cookie('easyuiLangType', langName, {
        		expires : 30,path: '/'
        	});
        	$.cookie('think_var', langName, {
        		expires : 30,path: '/'
        	});
            location.reload();
        }
    });
};

/*
 *
 * 将form表单元素的值序列化成对象
 *
 * @returns object
 */
serializeObject = function(form) {
	var o = {};
	$.each(form.serializeArray(), function(index) {
		if (o[this['name']]) {
			o[this['name']] = o[this['name']] + "," + this['value'];
		} else {
			o[this['name']] = this['value'];
		}
	});
	return o;
};

// 將含 "," 號的字符串按 "," 解析為數組
stringToList = function(value) {
	if (value != undefined && value != '') {
		var values = [];
		var t = value.split(',');
		for ( var i = 0; i < t.length; i++) {
			values.push('' + t[i]);//避免他将ID当成数字
		}
		return values;
	} else {
		return [];
	}
};

//状态格式化
var formatStatus = function(value, row, index) {
		if (row.status==1){
			return '<font color=green>正常</font>';
		}else {
			return '<font color=red>禁用</font>';
		}
}

//json格式轉換為json字符串或數組
var JsonToStr = function (o) {
	var g = arguments[1] ? arguments[1] : false;
	var arr = [];
	var fmt = function(s) {
		if (typeof s == 'object' && s != null) return JsonToStr(s);
		return /^(string|number)$/.test(typeof s) ? '"' + s + '"' : s;
	}
	for (var i in o) arr.push('"' + i + '":' + fmt(o[i]));
	if(g) return arr;
	else  return '{' + arr.join(',') + '}';
}
//Object轉換為字符串
var O2String = function (O) {
	//return JSON.stringify(jsonobj);
	var S = [];
	var J = "";
	if (Object.prototype.toString.apply(O) === '[object Array]') {
		for (var i = 0; i < O.length; i++)
			S.push(O2String(O[i]));
		J = '[' + S.join(',') + ']';
	}
	else if (Object.prototype.toString.apply(O) === '[object Date]') {
		J = "new Date(" + O.getTime() + ")";
	}
	else if (Object.prototype.toString.apply(O) === '[object RegExp]' || Object.prototype.toString.apply(O) === '[object Function]') {
		J = O.toString();
	}
	else if (Object.prototype.toString.apply(O) === '[object Object]') {
		for (var i in O) {
			O[i] = typeof (O[i]) == 'string' ? '"' + O[i] + '"' : (typeof (O[i]) === 'object' ? O2String(O[i]) : O[i]);
			S.push(i + ':' + O[i]);
		}
		J = '{' + S.join(',') + '}';
	}
	return J;
};
/**
 *
 * 改变jQuery的AJAX默认属性和方法
 */
$.ajaxSetup({
	/*error: function(rsp) {
        if (rsp.status != 901 && rsp.status != 902) {
            if (rsp.statusText === "timeout") {
                $.messager.alert("响应超时！(" + this.url.split("?")[0] + ")");
            } else {
                //console.info(rsp.status);
                $.messager.alert('Error Code:'+rsp.status);
            }
        }
    },*/
    statusCode: {
        901: function(data) {
            //$(this).closest('.window-body').dialog('destroy');
            //$.messager.alert('提示', '您未登录或者登录已超时，请重新登录！');
            timeoutLoginUser(data.statusText);
        },
        902: function(data) {
            //$(this).closest('.window-body').dialog('destroy');
            $.messager.alert('提示', '您没有权限访问此功能！');
        }
    },
    timeout: 90 * 100000
});
var timeoutLoginUser = function (jTitle){
	$('<div/>').dialog({
		title: jTitle,
		cache: false,
		href: '__PUBLIC__/login_dialog.html',
		modal: true
	});
}
//廠線組專用
function clearNode(node){
	// if (node.state){
		// alert("請選擇正確的段組站"+node.id);
		// return false;
	// }else{
		return true;
	// }
}
//廠線組專用
function clsNode(node){
	var _url = $(this).tree('options').url;
	var _sflag = _url.indexOf('sflag')>0?_url.substr(_url.indexOf('sflag')+6,1):false, //是否展開至區段 true為不展開至區段
		_gflag = _url.indexOf('gflag')>0?_url.substr(_url.indexOf('gflag')+6,1):false,//是否展開至組別 true為不展開至組別
		_tflag = _url.indexOf('tflag')>0?_url.substr(_url.indexOf('tflag')+6,1):false,//是否展開至站點 true為不展開至站點
		id = node.id;
	var id_ary = id.split('-');
	if(_sflag&&id_ary.length==2){
		return true;
	}else if(_gflag&&id_ary.length==3){
		return true;
	}else if(_tflag&&id_ary.length==4){
		return true;
	}else if(id_ary.length==5){
		return true;
	}else{
		if(node.state=='closed')
			$(this).tree('expand',node.target);
		else
			$(this).tree('collapse',node.target);
		return false;
	}
}
// 兩個TABLE間,相互移動指派
function tableAppoint(t1,t2) {
	var j_id1='#'+t1;
	var j_id2='#'+t2;
	var rowData = $(j_id1).datagrid('getChecked');
	var rowLen=rowData.length;
	//console.info(rowData);
	for ( var i = 0; i < rowLen; i++) {
		var rowIndex = $(j_id1).datagrid('getRowIndex',rowData[0]);
		if($(j_id2).datagrid('getRowIndex',rowData[0].id)==-1){
			$(j_id2).datagrid('insertRow',{row:rowData[0]});
		}
		$(j_id1).datagrid('deleteRow',rowIndex);
		rowData = $(j_id1).datagrid('getChecked');
	}
}

// 需声明数据的返回类型系统才会自动返回json格式数据，不然返回string格式数据，需用json.parse()转换
var PtSpoolFn = {
	print : function(printCode,pcaddr,port,tips){
				jQuery.support.cors = true;
				//alert(printCode);
				$.ajax({
					url:'http://'+pcaddr+':'+port+'/printSn.php',
					data:{ 'data':printCode,'fresh':Date.parse(new Date()) },
					type:'post',
					error:function(){
						 $.messager.show({
							title:tips,
							msg:'打印失敗'
						 });
					},
					success:function(){
						$.messager.show({
							title:tips,
							msg:'打印成功'
						});
					}
			   });
			},
    multiPrint : function(moNumber,model,startsn,endsn){
					var that=this;
					$.post('__URL__/x_xMultiprint',{"moNumber":moNumber,"model":model,"startsn":startsn,"endsn":endsn},
						   function(data){
								if(data){
									that.print(data);
								}else{
									$.messager.show({
										title:tips,
										msg:'打標條碼找尋不到'
									})
								}
						   });
				}
}
/**
* EasyUI DataGrid根据字段动态合并单元格
* 参数 tableID 要合并table的id
* 参数 colList 要合并的列,用逗号分隔(例如："name,department,office");
*/
function mergeCellsByField(tableID, colList) {
    var ColArray = colList.split(",");
    var tTable = $("#" + tableID);
    var TableRowCnts = tTable.datagrid("getRows").length;
    var tmpA;
    var tmpB;
    var PerTxt = "";
    var CurTxt = "";
    var alertStr = "";
    for (j = ColArray.length - 1; j >= 0; j--) {
        PerTxt = "";
        tmpA = 1;
        tmpB = 0;

        for (i = 0; i <= TableRowCnts; i++) {
            if (i == TableRowCnts) {
                CurTxt = "";
            }
            else {
                CurTxt = tTable.datagrid("getRows")[i][ColArray[j]];
            }
            if (PerTxt == CurTxt) {
                tmpA += 1;
            }
            else {
                tmpB += tmpA;

                tTable.datagrid("mergeCells", {
                    index: i - tmpA,
                    field: ColArray[j],　　//合并字段
                    rowspan: tmpA,
                    colspan: null
                });
                tTable.datagrid("mergeCells", { //根据ColArray[j]进行合并
                    index: i - tmpA,
                    field: "Ideparture",
                    rowspan: tmpA,
                    colspan: null
                });

                tmpA = 1;
            }
            PerTxt = CurTxt;
        }
    }
}
/**
 *
 * 播放聲音通用函數
 *
 */
PlayVoice = function(vUrl) {
	try{
		var audio = new Audio(vUrl);
		audio.play();
	}catch(e){
		var jUrl = vUrl.replace('.mp3', '.wav');
		//alert(jUrl);
		document.getElementById("PlayId").URL=jUrl;
		document.getElementById("PlayId").controls.play();
	}
};

// 判斷空值函數
returnNull = function(parm) {
	return (parm == null) ? "" : parm;
};

/**
 *
 * tree自動展開
 *
 */
loadTreeData = function (pid,trid){
	var paid = $(pid).val();
	var row = paid.split('-');
	for(var i = 0;i<row.length;i++){
		var node = $(trid).tree('find', row[i]);
		if(node){
			$(trid).tree('expand',node.target);
		}
	}
}

/**
 * AJAX 請求方法
 * @param url		 請求地址
 * @param data		 參數，json
 * @param successfn  請求成功執行函數
 * @param failfn	 失敗執行函數
 * create by LZY 2017/6/5
 */
postData = function(url, data, successfn, failfn) {
	$.ajax({
		url:  url,
		type: "post",
		data: data,
		dataType: "json",
		error: function() {
			if(typeof failfn == 'function'){
				failfn();
			}
			return false;
		},
		success: function(res) {
			if(typeof successfn == 'function'){
				successfn(res);
			}
		}
	});
}

/**
 * 表單提交通用方法
 * @param formId  	 	表單ID
 * @param formUrl	  	表單請求的URL
 * @param queryParams 	附加參數，json
 * @param successfn	    請求成功后除提示信息，附加代碼的函數
 * create by LZY 2017/6/2
 */
formSubmit = function(formId, formUrl, successfn, queryParams) {
	//console.log(queryParams);
	$(formId).form('submit', {
		url : formUrl,
		queryParams : queryParams,
		onSubmit: function(){ // 提交前進行表單驗證 textarea 設置 class='required'
			var flag = true;
			$(formId +" textarea.required").each(function(key, dom){
				if($(dom).val() == ""){
					messagerShow("提示", "必填項不可為空！");
					flag = false;
					return false;
				}
			});
			if(!$(formId).form('validate')){
				flag = false;
			}
			return flag;
		},
		success : function(result) {
			try {
				var r = $.parseJSON(result);
				//alert(r.status);
				if (r.status == 1) {
					messagerShow('提示', r.info);
					successfn && successfn(r); // 請求成功函數附加代碼
				}else{
					messagerShow('提示', r.info);
				}
			} catch (e) {
				//alert(1);
				$.messager.alert('提示1', result);
			}
		}
	});
}

/**
 * dialog 驗證是否選擇datagrid 行
 * @param  dgId   datagrid 的ID
 * @param  isMany 選擇行數是否多選，只選一行傳0，如果需要多選，此處傳 1
 * @return
 * create by LZY 2017/6/2
 */
isChecked = function(dgId, isMany){
	var rows = $(dgId).datagrid('getChecked');
	if (isMany !=1 && rows.length >1) {
		$(dgId).datagrid('clearChecked').datagrid('clearSelections');
		messagerShow('提示', '不能同时操作多条记录，请选择一条@！');
		return false;
	}else if (rows.length == 0) {
		messagerShow('提示', '请勾选要操作的记录！');
		return false;
	}else {
		return true;
	}
}

/**
 * dialog 顯示通用方法（服務于 DatagridMethod）
 * @param diaHref		dialog 頁面鏈接
 * @param diaTitle		dialog 標題
 * @param saveFn		保存按鈕的操作（保存，存增函數未傳入參數，則只顯示關閉按鈕）
 * @param continueFn	存增按鈕的操作（傳入 null 則不顯示存增）
 * @param diaWidth		dialog 寬度（未傳寬、高則默認適應父容器）
 * @param diaHeight		dialog 高度
 * create by LZY 2017/7/31
 */
//dialogShow = function(diaHref, diaTitle, saveFn, continueFn, diaWidth, diaHeight, queryParams){
dialogShow = function(diaHref, diaTitle, diaWidth, diaHeight, queryParams, btnCount, module,closeFun){
	var c_module =  module ? module : getModule();
	c_module = c_module.substring(0, 1).toUpperCase() + c_module.substring(1).toLowerCase();
	//console.log(c_module);
	var diaId = "#"+ c_module +"DiaLog";
	//var tab = $('#layout_center_tabs').tabs('getSelected'), _div = $(tab).find('div.easyui-layout');
	//if(_div){
		/*查找所選擇Tab的第一個div class="easyui-layout"，如有自動創建*/
	//	var OptDia = $("<div id='"+c_module+"DiaLog'></div>").appendTo(_div);
	//}else{/*如無採用系統固定Id*/
		var OptDia = $(diaId);
		//console.log(OptDia);
	//}
	// 默認存在 3 個按鈕
	var buttons = [{
				text:'保存',
				iconCls : 'icon-filesave',
				id : c_module + 'BtnSave'
			},{
				text:'存增',
				iconCls : 'icon-add',
				id : c_module + 'BtnCont'
			},{
				text:'關閉',
				iconCls : 'icon-no',
				handler:function(){ OptDia.dialog('close'); }
			}];
	// 判斷是否存在保存和存增函數
	if(btnCount == 0){
		buttons.splice(0, 2);
	}else if(btnCount == 1){
		buttons.splice(1, 1);
	}
	// dialog 設置
	//console.log(OptDia);
	OptDia.dialog({
		href : diaHref,
		inline : true,
		closable: false,
		method: 'post',
		queryParams: queryParams, // 參數傳遞
		constrain: diaWidth ? false:true,
		modal : true,
		cache : false,
		width : diaWidth ? diaWidth:0,
		height : diaHeight ? diaHeight:0,
		fit : diaWidth ? false:true, // 存在寬度即不適應父容器
		title : diaTitle,
		top : '0px',
		tools:[
				{
					iconCls:'icon-cross',
					text : '關閉',
					handler:function(){OptDia.dialog('close');}
				} ],
		buttons : buttons,
		onClose : function(){
			if(typeof closeFun == 'function'){
				closeFun();
		    }
			/*var operate;
			try{
				operate = queryParams.operate;
			}catch(e){}
			if(operate=='view'){
			}else{
				$("#"+c_module+"DataGrid").datagrid('reload');
			}*/
		},
		// 加載表單數據
		onLoad : function(){
			var operate = queryParams.operate;
			switch(operate){
				case 'edit':
					try{
						var rows = $("#"+c_module+"DataGrid").datagrid('getChecked');
						if($("#"+c_module+"EditForm")[0]){
							$("#"+c_module+"EditForm").form("load", rows[0]);
						} else if($("#"+c_module+"AddForm")[0]){
							$("#"+c_module+"AddForm").form("load", rows[0]);
						}
					}catch(e){}
					break;

				case 'view':
					var rows = $("#"+c_module+"DataGrid").datagrid('getChecked');
					if($("#"+c_module+"ViewForm")[0]){
						$("#"+c_module+"ViewForm").form("load", rows[0]);
						$("#"+c_module+"ViewForm input").attr("readonly", "readonly");
					} else if($("#"+c_module+"AddForm")[0]){
						$("#"+c_module+"AddForm").form("load", rows[0]);
						$("#"+c_module+"AddForm input").attr("readonly", "readonly");
					}
					break;
				case 'add':
					break;
				case 'upload':
					break;
				default :
					//var rows = $("#"+c_module+"DataGrid").datagrid('getChecked');
					//$(OptDia).find("form").form("load", rows[0]);
			}
		}
	});
	//console.log(OptDia);
	// 標題和按鈕居中
	$("#layout_center_tabs .dialog-button").css("text-align", "center");
	$("#layout_center_tabs .panel-title").css("text-align", "center");
	OptDia.dialog("hcenter");
}

/**
 * 提示信息方法
 * @param showTitle $.messager.show 的標題
 * @param showMsg   $.messager.show 的內容
 * create by LZY 2017/6/2
 */
messagerShow = function(showTitle, showMsg){
	$.messager.show({
		title : showTitle,
		msg : showMsg
	});
}

// 檢索數據大寫去空格
function toUpper(){
	var str = $(this).val();
	str = str.toUpperCase();
	$(this).val(str.replace(/(^\s*)|(\s*$)/g, "")); 
}

/**
 * 表單簡體轉繁體方法（綁定輸入框事件）
 * easyui 	表單調用 	data-options="events:{blur:inputBind}"
 * textarea 綁定 		blur 事件 $("textarea").blur(inputBind);
 * create by LZY 2017/7/3
 */
inputBind = function(){
	var str = $(this).val();
	str = langUtil.simpToTra(str);
	$(this).val(str);
}

// 獲取當前模塊名
getModule = function(){
	var tab = $('#layout_center_tabs').tabs('getSelected');
	var tabOptions = tab.panel('options');
	var module = tabOptions.href.split("/"); // 獲取當前模塊名
	c_module = module[2].substring(0, 1).toUpperCase() + module[2].substring(1).toLowerCase();
	return c_module;
}
//  在指定的毫秒数后调用函数或计算表达式；该函数定义如下参数：
    //      code:       必需。要调用的函数后要执行的 JavaScript 代码串。
    //      millisec:   可选。在执行代码前需等待的毫秒数。
    //  模拟 setTimeout/setImmediate 方法。
    //  备注：如果不传入参数 millisec 或该参数值为 0，则自动调用 setImmediate(该方法相对于 setTimeout 可以有效降低浏览器资源开销) 方法；
exec = function (code, millisec) {
	if (!code) { return; }
	return !millisec && window.setImmediate ? window.setImmediate(code) : window.setTimeout(code, millisec);
};

	//JS數組過濾重複數據
filterArray = function (receiveArray){
		var arrResult = new Array(); //定义一个返回结果数组.
		for (var i=0; i<receiveArray.length; ++i) {
			if(check(arrResult,receiveArray[i]) == -1) {
				//在这里做i元素与所有判断相同与否
				arrResult.push(receiveArray[i]);　
				//　添加该元素到新数组。如果if内判断为false（即已添加过），
				//则不添加。
			}
		}
		return arrResult;
}
check = function (receiveArray,checkItem){
		var index = -1; //　函数返回值用于布尔判断
		for(var i=0; i<receiveArray.length; ++i){
			if(receiveArray[i]==checkItem){
				index = i;
				break;
				}
			}
		return index;
}
function IEVersion() {
	var userAgent = navigator.userAgent; //取得浏览器的userAgent字符串 
	var isIE = userAgent.indexOf("compatible") > -1 && userAgent.indexOf("MSIE") > -1; //判断是否IE<11浏览器 
	var isEdge = userAgent.indexOf("Edge") > -1 && !isIE; //判断是否IE的Edge浏览器 
	var isIE11 = userAgent.indexOf('Trident') > -1 && userAgent.indexOf("rv:11.0") > -1;
	if(isIE) {
		var reIE = new RegExp("MSIE (\\d+\\.\\d+);");
		reIE.test(userAgent);
		var fIEVersion = parseFloat(RegExp["$1"]);
		if(fIEVersion == 7) {
			return 7;
		} else if(fIEVersion == 8) {
			return 8;
		} else if(fIEVersion == 9) {
			return 9;
		} else if(fIEVersion == 10) {
			return 10;
		} else {
			return 6;//IE版本<=7
		}  
	} else if(isEdge) {
		return 'edge';//edge
	} else if(isIE11) {
		return 11; //IE11 
	}else{
		return -1;//不是ie浏览器
	}
}
var LODOP;
function createPrinterList(id){
	if (document.getElementById(id).innerHTML!="") return;
	try{
		LODOP=getLodop();
		if (LODOP.VERSION) {
			//if (LODOP.webskt && LODOP.webskt.readyState==1){
				var iPrinterCount=LODOP.GET_PRINTER_COUNT();
				var data = [];
				for(var i=0;i<iPrinterCount;i++){
					var option=document.createElement('option');
					/*只抓取打印機名稱含有ZT410、ZM400的打印機*/
					if(LODOP.GET_PRINTER_NAME(i).indexOf('ZT410')>0 || LODOP.GET_PRINTER_NAME(i).indexOf('ZM400')>0){
						if(data.length==0){
							data.push({"id":i,"text":LODOP.GET_PRINTER_NAME(i),selected:true});
						}else{
							data.push({"id":i,"text":LODOP.GET_PRINTER_NAME(i)});
						}
					}
				};
				$('#'+id).combobox('loadData',data);
			//} else {
			//	alert("打印服務啟動失敗，請先手工啟動C-Lodop打印服務");
			//}
		}
	}catch(err){} 
};
/*
//在layout的panle全局配置中,增加一个onCollapse处理title
$.extend($.fn.layout.paneldefaults, {
    onCollapse : function () {
        //获取layout容器
        var layout = $(this).parents("body.layout");
                if(layout.length == 0) layout = $(this).parents("div.layout");
        //获取当前region的配置属性
        var opts = $(this).panel("options");
        //获取key
        var expandKey = "expand" + opts.region.substring(0, 1).toUpperCase() + opts.region.substring(1);
        //从layout的缓存对象中取得对应的收缩对象
        var expandPanel = layout.data("layout").panels[expandKey];
        //针对横向和竖向的不同处理方式
        if (opts.region == "west" || opts.region == "east") {
                     if(expandPanel){
                      if(opts.iconCls) icon = '<div class="'+ opts.iconCls +'" style="width:20px"> </div>';
              //竖向的文字打竖,其实就是切割文字加br
              var split = [];
              for (var i = 0; i < opts.title.length; i++) {
                split.push(opts.title.substring(i, i + 1) + '<br>');
              }
                          if(opts.iconCls){
                expandPanel.panel("body").addClass("panel-title").css("text-align", "center").html('<div style="padding-left: 2px; background-position: left center;width:20px"><div class="'+ opts.iconCls +'" style="width:20px"> </div>' + split.join("") +'</div>');
                          }else{
                            expandPanel.panel("body").addClass("panel-title").css("text-align", "center").html(split.join(""));
                          }
                        }
        } else {
                     if(expandPanel){
                      if(opts.iconCls){
                            expandPanel.panel("setTitle", '<div class="'+ opts.iconCls +'" style="padding-left: 20px; background-position: left center;">'+ opts.title +'</div>');
                          }else{
                            expandPanel.panel("setTitle", opts.title);
                          }
                        }
        }
    }
});

    var easyuiPanelOnMove = function(left, top) { // 防止超出浏览器边界
        if (left &lt; 0) {
            $(this).window(&#39;move&#39;, {
                left : 1
            });
        }
        if (top &lt; 0) {
            $(this).window(&#39;move&#39;, {
                top : 1
            });
        }
        var width = $(this).panel(&#39;options&#39;).width;
        var height = $(this).panel(&#39;options&#39;).height;
        var right = left + width;
        var buttom = top + height;
        var browserWidth = $(document).width();
        var browserHeight = $(document).height();
        if (right &gt; browserWidth) {
            $(this).window(&#39;move&#39;, {
                left : browserWidth - width
            });
        }
        if (buttom &gt; browserHeight) {
            $(this).window(&#39;move&#39;, {
                top : browserHeight - height
            });
        }
    };
    $.fn.panel.defaults.onMove = easyuiPanelOnMove;
    $.fn.window.defaults.onMove = easyuiPanelOnMove;
    $.fn.dialog.defaults.onMove = easyuiPanelOnMove;
*/