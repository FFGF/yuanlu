function format_changeDate(s_obj,e_obj){
	if($("#"+s_obj).val()!=''){
		var s_time=DateToUnix($("#"+s_obj).val());
		$("input[name='"+s_obj+"']").val(s_time);
        $("input[name='"+s_obj+"1']").val(s_time);
		//console.log(s_time);
	}
	if($("#"+e_obj).val()!=''){
		var e_time=DateToUnix($("#"+e_obj).val()+' 23:59:59');
		$("input[name='"+e_obj+"']").val(e_time);
		//console.log(e_time);
	}
}

function DateToUnix(string) {
	var f = string.split(' ', 2);
	var d = (f[0] ? f[0] : '').split('-', 3);
	var t = (f[1] ? f[1] : '').split(':', 3);
	return (new Date(
		parseInt(d[0], 10) || null,
		(parseInt(d[1], 10) || 1) - 1,
		parseInt(d[2], 10) || null,
		parseInt(t[0], 10) || null,
		parseInt(t[1], 10) || null,
		parseInt(t[2], 10) || null
	)).getTime() / 1000;
};