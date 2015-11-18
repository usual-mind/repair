/**
 *ajax请求html
 */
function ajaxRequestHtml(href,callback){
	$.ajax({
		type: "GET",
		url: href,
		dataType: "html",
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
*ajax跳转
*href:要跳转的链接
*/
function ajaxJump(href){
	$("html").fadeOut("fast",function(){
		$.ajax({
			type: "GET",
			url: href,
			dataType: "html",
			success: function(data){
				$(this).remove();
				document.write(data);
			},
			error: function(jqXHR){
				//ajax请求出错
				alert( "错误号: " + jqXHR.status );
			}
		});
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
	Jheader.children(".hidden-text").attr('href',backUrl);
	Jheader.children("h1").html(title);
}