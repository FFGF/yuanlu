/**
 * Created by Administrator on 2016/9/26.
 */

function checkDate(){
    if($("input[name='s_time']").val() == ''){
        alert('日期不能为空')
        return false;
    }else{
        return true;
    }
}



