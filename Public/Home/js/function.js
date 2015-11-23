/**
 *ajax请求
 * @param href 请求的URl
 * @param callback 请求成功的回调函数
 * @param dataType	请求返回的数据类型
 */
function ajaxRequest(href,callback,dataType){
	if(typeof(dataType) == "undefined") dataType='html';
	$.ajax({
		type: "GET",
		url: href,
		dataType: dataType,
		success:function(data){
			callback(data);
		},
		error: function(jqXHR){
			//ajax请求出错
			alert( "错误号: " + jqXHR.status );
			return null;
		}
	});
}

/**
*显示对话框
*mess：消息内容
*fnOk:点击去顶按钮的回调函数
*fnCancel:点击取消按钮的回调函数
*/
function dialogBox(mess,fnOk,fnCancel){
	$("#messageBoxText").html(mess);
	$("#messageBox").show("fast");
	
	$("#messageBoxOkBtn").click(function(){
		$("#messageBox").hide("hidden");
		//确定
		fnOk();
	});
	$("#messageBoxCancelBtn").click(function(){
		$("#messageBox").hide("hidden");
		//取消
		fnCancel();
	});
}
/**
*显示提示框
*tip：提示内容
*time:显示时间
*/
function tipBox(tip,time){
	if(!time) time = 3000;
	$("#tipBox>.cue-text").html(tip);
	$("#tipBox").slideDown("fast");
	setTimeout('$("#tipBox").slideUp("fast")',time);
}
function setHeader(title,backUrl){
	if(backUrl==null) backUrl='javascript:;';
	var Jheader = $("#header");
	Jheader.children(".hidden-text").attr('data-href',backUrl);
	Jheader.children("h1").html(title);
}
/**
 *	设置返回按钮的回调函数
 */
function setBackFun(fn){
	if(!fn){
		$('#header>button').remove();
		$('#header').prepend('<a href="#" class="hidden-text" title="后退">后退</a>');
		return;
	}
	$('#header>a').remove();
	$('#header').prepend('<button href="#" class="hidden-text" title="后退">后退</button>');
	$('#header>button').click(fn);

}