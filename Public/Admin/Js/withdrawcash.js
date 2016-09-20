//关闭按钮
	$(".info_close").click(function(){
		$(".info_bg").hide();
		$(".auditingFrame").hide();
	})
	//提现记录页面修改状态：审核中/审核通过/审核失败
	var updateRowIndex = -1;
	var tr;
	function updateRow(optionFlag) {
		var table = document.getElementById("tableid");
		tr = table.rows[updateRowIndex];
	$(".info_bg").hide();
	$(".auditingFrame").hide();
	}
	//通修改按钮对table里的数据进行修改
	function shenhe(row) {
		updateRowIndex = row.rowIndex;
		optionFlag = "update";
		//对table里的数据进行回显
		$("#backCash").val( row.cells[3].innerHTML );
        $("#id").val(row.cells[12].innerHTML);
		$(".info_bg").show();
		$(".auditingFrame").show();
        $(".auditingFrame").css({
            top: function(index, value) {
                return $(window).scrollTop() + ($(window).height()/2);
            }
        });

    }