$(function(){
	var Wid = $(window).width()-200;
	$(".platRight").css("width",Wid);
	$(window).resize(function(){
		$(".platRight").css("width",$(window).width()-200);
	})
	//选中状态菜单
	if( $(".active > .childLi").length != 0 ){
		$(".active").addClass('arrowDown');
		$(".active > .childLi").show();
	}
	//可切换菜单
	$(".platNav > li").click(function(){
		$(this).addClass("active").siblings("li").removeClass("active arrowDown").children().css("display","block");
		$(this).siblings().children("ul").hide();
		if( $(".active > .childLi").length != 0 ){
			$(".active").addClass('arrowDown');
			$(".active > .childLi").show();
		}

	})
	//优惠券管理页面的状态选择
	function xiala(aa,bb,cc){
		$(aa + " i").click(function(){
			$(bb).show();
			$(bb + " b").click(function(){
				$(cc).val( $(this).html() );
				$(bb).hide();
			})
		})
		$(cc).focus(function(){
			$(bb).show();
			$(bb +" b").click(function(){
				$(cc).val( $(this).html() );
				$(bb).hide();
			})
		})	
		$(bb).mouseover(function(){
			$(bb).show();
		}).mouseout(function(){
			$(bb).hide();
		})
		$(cc).mouseout(function(){
			$(bb).hide();
		})
	}
	xiala(".couponState",".couSta",".coupState");
	xiala(".couponState2",".couSta2",".coupState2");
	//全部客户页面详细按钮
		$(".info_more").click(function(){
			$(".info_bg").show();
			$(".detailed").show();
			$(".info_close").click(function(){
				$(".info_bg").hide();
				$(".detailed").hide();
			})
		})
	//管理中心页面提现功能
	$(".cash").click(function(){
		var cashtimer = null;
		if($(".cashhide").html()<=0)
			var cashhide = 0;
		else
			cashhide = parseFloat( $(".cashhide").html() );
		$(".info_bg").show();
		$(".withdraw_cash").show();
		$(".wdcashcue span").hide();
		$(".wdcashIpt").val( cashhide );
		$(".wdcashIpt").bind("keyup",function(){
			if( parseFloat( $(this).val() ) > cashhide){
				$(".wdcashcue span").html("您最多提取的金额为"+ cashhide + "元，请重新输入").show();
				$(".wdcashIpt").focus();
			}else{
				$(".wdcashcue span").hide();
			}
		})
		$(".info_close").click(function(){
			$(".info_bg").hide();
			$(".withdraw_cash").hide();
			$(".wdcStart").show();
			$(".wdcEnd").hide();
		})
	})
	//财务管理提现记录状态：待审核/审核通过/审核失败
		$(".Auditinag").click(function(){
			var state = $(".Auditing").html();
			if( state == "待审核"){
				$(".info_bg").show();
				$(".auditingFrame").show();
			}
			//关闭按钮
			$(".info_close").click(function(){
				$(".info_bg").hide();
				$(".auditingFrame").hide();
			})
			//通过adopt
			$(".adopt").click(function(){
				
				$(".info_bg").hide();
				$(".auditingFrame").hide();
				$(".Auditing").html("通过审核");
			})
			//拒绝refuse
			$(".refuse").click(function(){
				$(".info_bg").hide();
				$(".auditingFrame").hide();
				$(".Auditing").html("审核失败");
			})
		})
})
//提现成功返回
function cashover(){
	$(".wdcStart").hide();
	$(".wdcEnd").show();
	var i=5;
	$(".wdcEnd span").html( i );
		cashtimer = setInterval(function(){
		i--;
		$(".wdcEnd span").html( i );
		if (i==0) { 
			clearInterval(cashtimer);
			$(".info_bg").hide();
			$(".withdraw_cash").hide();
			$(".wdcStart").show();
			$(".wdcEnd").hide();
		};
	},1000)
}
function toggel(attr){
	if(attr == "reSearchM"){
		$(".reSearchM").removeClass("none");
		$(".RCsearch").addClass("none");
		$(".RCsearchSimple").html("简易");
	}else{
		$(".RCsearch").removeClass("none");
		$(".reSearchM").addClass("none");
		$(".RCsearchSimple").html("高级");
	}
	$(".change").click(function(){
		$(".reSearchM").toggleClass("none");
		$(".RCsearch").toggleClass("none");
		if( $(".RCsearchSimple").html() == "简易"){
			$(".RCsearchSimple").html("高级");
		}else{
			$(".RCsearchSimple").html("简易");
		}
	})
}
//toggel("RCsearch");