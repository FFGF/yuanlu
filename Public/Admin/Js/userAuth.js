var _url="/";
var nametips =  $("#userName-tips");
var tip_str='Tip：';

function codeObjTime(obj,ywait){
	if(ywait==0){
		$(obj).val("重新获取验证码").removeAttr("disabled").removeClass("after");
		ywait=120;
		return;
	}else{
		$(obj).attr("disabled","true");
		$(obj).val(ywait+"秒后获取验证码").addClass("after");
		ywait--; 
	}
	setTimeout(function(){codeObjTime(obj,ywait)},1000)	
}

function Validmobile(mobile){
    if(mobile.length!=11)
    {
        return false;
    }
    var myreg = /^(13[0-9]|15[0-9]|18[0-9])\d{8}$/;
    if(!myreg.test(mobile))
    {
        return false;
	}
    return true;
}



function m_check(type) {
	var mobile = $('#mobile').val();
	nametips.hide();	
	if (mobile == "") {
		alert(tip_str+'请输入手机号');
		$("#mobile").focus();
		return false
	}
	if (!Validmobile(mobile)) {
		alert(tip_str+'手机格式不正确');
		$("#mobile").focus();
		return false;
	}	
	
	if(type=='reg'){
		if ($('#captcha').val() == "") {
			alert(tip_str+'请输入验证码');
			$("#captcha").focus();
			return false;
		}
	}
	
	
}

function mRegistered() {
	
	if(m_check('reg')!=false){
		var mobile = $('#mobile').val();
		var email = $('#email').val();
		var company = $('#company').val();
		var name = $('#name').val();		
		var captcha = $('#captcha').val();
		var password1 = $("#password1").val();
		var password2 = $("#password2").val();		
		$("#m-button").val("正在提交...");
		var url = _url + "customer-add.html";
		var data = {"email": email,"mobile": mobile, "company": company,"name": name,  "captcha": captcha, "password1": password1, "password2": password2};		
		$.post(url, data, function(response) {
			if (response.code == 1) {
				$("#sendcaptcha").val("获取验证码").removeAttr("disabled").removeClass("after");
				alert(response.message);
				location.reload(true);
			} else {
				alert(response.message);
			}
		}, "json")
	}
}

function check_repeat(obj) {
	if($("#" + obj).val()==''){
		return;
	}
	var data = {"info": $("#" + obj).val(),"type":obj}
	var url = _url + "customer-isrepeat.html";
	$.getJSON(url, data, function(response) {
		if (response.code == 1) {
			$("#tip_" + obj).attr('class','right');
			//$("#m-button").removeAttr("disabled").attr('class','');
			return true;
		}else if (response.code == -1) {
			$("#tip_" + obj).attr('class','wrong');
			//$("#m-button").attr('class','after').attr("disabled","true");
			return false;
		}else {
			alert('服务错误，请重试');
		}
	});

}


function mCaptcha() {	
	if(m_check('')!=false){
		codeObjTime('#sendcaptcha',60);
		var data = {"mobile": $('#mobile').val()}
		var url = _url + "customer-sendmsg.html";	
		$.getJSON(url, data, function(response) {
			if (response.code == 1) {
				$("#captcha").focus();
				alert('验证码已发送到手机！'+response.results);
			}else{
				alert(response.message);
			}
		});
	}	
}
