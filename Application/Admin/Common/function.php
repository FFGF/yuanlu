<?php
//源庐的项目

function getCategory($vo){
    foreach($vo as $key=>$value){
        if($key == 'category'){
            return $value['category'];
        }
    }
}

function rmbToDollar($value,$rate){
    $value = str_replace(',','',$value);
    return number_format($value/$rate,2);
}

function formatWeek($value){
    switch($value){
        case 1:
            return "星期一";
        case 2:
            return "星期二";
        case 3:
            return "星期三";
        case 4:
            return "星期四";
        case 5:
            return "星期五";
        case 6:
            return "星期六";
        case 0:
            return "星期日";
    }
}
//删除数组中指定元素
function array_remove(&$arr, $offset)
{
    array_splice($arr, $offset, 1);
}
//为了实现日报日历查看，需要格式化日期
function formatdailyPaperDate($year,$month,$branch_id){
    $day = cal_days_in_month(CAL_GREGORIAN,$month,$year);
    $time_stamp = mktime(0,0,0,$month,1,$year);
//    $week = date('w',mktime(0,0,0,$month,1,$year));
    $first_day = date('Y-m-d',$time_stamp);;
    $last_day = date('Y-m-d',mktime(0,0,0,$month,$day,$year));
//    $first_day = date('Y-m-d',$time_stamp-24*3600*--$week);
//    $last_week = date('w',mktime(0,0,0,$month,$day,$year));
//    $last_day = date('Y-m-d',mktime(0,0,0,$month,$day,$year)+24*3600*(6-$last_week));
    $day_daily_paper = [];
    $PerdayDataItem = D("PerdayDataItem");
    $Branch = D("Branch");
    $branch_name = $Branch->getBranchNameById($branch_id);
    for(;strtotime($first_day)<=strtotime($last_day);){
        $temp = [];
        $temp['date'] = $first_day;
        $temp['branch_name'] = $branch_name;
        $temp['week'] =  date("w",strtotime($first_day));
        $temp['business'] = $PerdayDataItem->judgeDataInput($branch_id,$first_day,false);
        $temp['finance'] = $PerdayDataItem->judgeDataInput($branch_id,$first_day,true);;
        $day_daily_paper[] = $temp;
        $first_day = date('Y-m-d',strtotime("$first_day +1 day"));
    }
    return $day_daily_paper;
}
//为了实现日报日历查看，需要格式化日期,领导查看所有部门专用
function formatdailyPaperDateLead($year,$month,$branch_name){
    $day = cal_days_in_month(CAL_GREGORIAN,$month,$year);
    $time_stamp = mktime(0,0,0,$month,1,$year);
    $first_day = date('Y-m-d',$time_stamp);;
    $last_day = date('Y-m-d',mktime(0,0,0,$month,$day,$year));
    $day_daily_paper = [];
    $PerdayDataItem = D("PerdayDataItem");
    for(;strtotime($first_day)<=strtotime($last_day);){
        $temp = [];
        $temp['date'] = $first_day;
        $temp['week'] =  date("w",strtotime($first_day));
        $temp['branch_name'] = $branch_name;
        $temp['business'] = $PerdayDataItem->judgeDataInputLead([2,3],$first_day);
        $temp['finance'] = $PerdayDataItem->judgeDataInputLead([1],$first_day);;
        $day_daily_paper[] = $temp;
        $first_day = date('Y-m-d',strtotime("$first_day +1 day"));
    }
    return $day_daily_paper;
}
//格式化填写账户数据信息
function formatData($data,$date){
    $formatdata = [];
    $item = [];
    $i = 0;
    $project = M('project');
    $branch = M('branch');
    foreach($data as $key=>$value){
        $item[pickKey($key)] = $value;
        $i++;
        if($i%6 == 0){
            $item['project_id'] = preg_replace('/\D/s', '', $key);
            $item['project_name'] = $project->where('id='.$item['project_id'])->getField('name',true)[0];
            $item['category'] = $project->where('id='.$item['project_id'])->getField('category',true)[0];
            $item['user_id'] = session('admin')['id'];
            $item['branch_id'] = $project->where('id='.$item['project_id'])->getField('branch_id',true)[0];
            $item['branch_name'] = $branch->where('id='.$item['branch_id'])->getField('name',true)[0];
            $item['effect_date'] = $date;
            $formatdata[$i/6] = $item;
            $item = [];
        }
    }
    return $formatdata;
}
//将字符串中的key提取出来
function pickKey($str){
    if(substr($str,0,strpos($str,preg_replace('/\D/s', '', $str)))){
        return substr($str,0,strpos($str,preg_replace('/\D/s', '', $str)));
    }else{
        return $str;
    }
}
//根据key取出符合要求的数据
function getValueByKey($key,$value,$data){
    $needData = [];
    foreach($data as $k=>$v){
        if($v[$key] == $value){
            $needData[] = $v;
        }
    }
    return $needData;
}
//生成两个日期之前的所有日期，返回一个日期数组
function getStartDateEndDateArray($start_date,$end_date){
    $date_array = [];
    for(;strtotime($start_date)<=strtotime($end_date);){
        $date_array[] = $start_date;
        $start_date = date("Y-m-d",strtotime("$start_date +1 day"));
    }
    return $date_array;
}

function getBankData($result,$value,$key_name){
    $data = [];
    foreach($result as $key=>$item) {
        if ($item[$key_name] == $value) {
            $data[] = $item;
        }
    }
    return $data;
}

//完善数据，如果董事长当天查看数据，但是没有录入，会无法查看，不知道数据是否输入
function formatShowData($formatData){
    $formatData_temp = $formatData;
    foreach($formatData as $key=>$value){
        $k_maps['branch.name'] = $key;
        $result = M('project')->join('branch on branch.id = project.branch_id')
            ->where($k_maps)
            ->field('project.name')
            ->select();
        $form_result = [];
        foreach($result as $k=>$v){
            $form_result[] = $v['name'];
        }
        $temp = [];
        foreach($value as $k=>$v){
            $temp[] = $v['name'];
        }
        $diff_array = array_diff($form_result,$temp);
        foreach($diff_array as $v){
            $project_result = M('project')->where(array('name'=>$v))->find();
            $t['project_id'] = $project_result['id'];
            $t['id'] = $project_result['branch_id'];
            $t['name'] = $v;
            $t['name1'] = $key;
            $t['asset_money'] = '0.00';
            $t['asset_product'] = '0.00';
            $t['finance_debt'] = '0.00';
            $t['receivable'] = '0.00';
            $t['payable'] = '0.00';
            $t['remark'] = '';
            $t['bank_category'] = $project_result['bank_category'];
            $t['onshore_exchange_rate'] = '0.0000';
            $t['asset_money_navtive'] = '0.0000';
            $t['asset_product_navtive'] = '0.0000';
            $t['finance_debt_navtive'] = '0.0000';
            $t['receivable_navtive'] = '0.0000';
            $t['payable_navtive'] = '0.0000';
            $formatData_temp[$key][] = $t;
        }
    }
    return $formatData_temp;
}

function sumFuck($formatData){
    $formatData['所有部门总和']['fuck'] = [];
    foreach($formatData as $key=>$value){
        $i = count($value);
        if($key!='所有部门总和'){
            $formatData[$key][$i] = [];
            $formatData[$key][$i]['name'] = '部门总和';
        }
        foreach($value as $k=>$v){
            if($key!='所有部门总和'){
                $formatData[$key][$i]['asset_money'] =  $formatData[$key][$i]['asset_money'] +  str_replace(',','',$v['asset_money']);
                $formatData[$key][$i]['asset_product'] =  $formatData[$key][$i]['asset_product'] +  str_replace(',','',$v['asset_product']);
                $formatData[$key][$i]['finance_debt'] =  $formatData[$key][$i]['finance_debt'] +  str_replace(',','',$v['finance_debt']);
                $formatData[$key][$i]['receivable'] =  $formatData[$key][$i]['receivable'] +  str_replace(',','',$v['receivable']);
                $formatData[$key][$i]['payable'] =  $formatData[$key][$i]['payable'] +  str_replace(',','',$v['payable']);
            }
            $formatData['所有部门总和']['fuck']['asset_money'] =  $formatData['所有部门总和']['fuck']['asset_money'] + str_replace(',','',$v['asset_money']);
            $formatData['所有部门总和']['fuck']['asset_product'] =  $formatData['所有部门总和']['fuck']['asset_product'] + str_replace(',','',$v['asset_product']);
            $formatData['所有部门总和']['fuck']['finance_debt'] =  $formatData['所有部门总和']['fuck']['finance_debt'] + str_replace(',','',$v['finance_debt']);
            $formatData['所有部门总和']['fuck']['receivable'] =  $formatData['所有部门总和']['fuck']['receivable'] + str_replace(',','',$v['receivable']);
            $formatData['所有部门总和']['fuck']['payable'] =  $formatData['所有部门总和']['fuck']['payable'] + str_replace(',','',$v['payable']);
        }
    }
    return $formatData;
}
//计算两个交易日的差值，所有部门
function subtractDataAll($result,$last_result){
    $sub_data = [];
    foreach($result as $key=>$value){
        $sub_data[$key] = [];
    }
    reset($last_result);
    $first = key($last_result);
    foreach($result as $key=>$value){
        foreach($value as $k=>$v){
            $v['sum_asset_money'] = str_replace(',','',$v['asset_money']) -getLastDateData($key,$v['name'],$last_result,'asset_money');
            $v['sum_asset_money_navtive'] = str_replace(',','',$v['asset_money_navtive']) -getLastDateData($key,$v['name'],$last_result,'asset_money_navtive');

            $v['sum_asset_product'] = str_replace(',','',$v['asset_product']) -getLastDateData($key,$v['name'],$last_result,'asset_product');
            $v['sum_asset_product_navtive'] = str_replace(',','',$v['asset_product_navtive']) -getLastDateData($key,$v['name'],$last_result,'asset_product_navtive');

            $v['sum_finance_debt'] = str_replace(',','',$v['finance_debt']) -getLastDateData($key,$v['name'],$last_result,'finance_debt');
            $v['sum_finance_debt_navtive'] = str_replace(',','',$v['finance_debt_navtive']) -getLastDateData($key,$v['name'],$last_result,'finance_debt_navtive');
            array_push($sub_data[$key], $v);
        }
    }
    return $sub_data;
}
function getLastDateData($key,$name,$last_result,$str){
    $item_array = $last_result[$key];
    foreach($item_array as $key=>$value){
        if($value['name'] == $name){
            return str_replace(',','',$value[$str]);
        }
    }
}

//计算两个交易日的差值
function subtractData($result,$last_result){
    $sub_data = [];
    foreach($result as $key=>$value){
        $sub_data[$key] = [];
    }
    reset($last_result);
    $first = key($last_result);
    if(count($last_result[$first])<=1){
        //如果上一个交易日没有数据
        foreach($result as $key=>$value){
            foreach($value as $k=>$v){
                $v['sum_asset_money'] = round(str_replace(',','',$v['asset_money']),2);
                $v['sum_asset_money_navtive'] = round(str_replace(',','',$v['asset_money_navtive']),2);

                $v['sum_asset_product'] = round(str_replace(',','',$v['asset_product']),2);
                $v['sum_asset_product_navtive'] = round(str_replace(',','',$v['asset_product_navtive']),2);

                $v['sum_finance_debt'] = round(str_replace(',','',$v['finance_debt']),2);
                $v['sum_finance_debt_navtive'] = round(str_replace(',','',$v['finance_debt_navtive']),2);
                array_push($sub_data[$key], $v);
            }
        }
    }else{
        foreach($result as $key=>$value){
            foreach($value as $k=>$v){
                foreach($last_result as $key_last=>$value_last){
                    foreach($value_last as $k_l=>$v_l){
                        if($v['name'] == $v_l['name']){
                            $v['sum_asset_money'] = round(str_replace(',','',$v['asset_money'])  - str_replace(',','',$v_l['asset_money']),2);
                            $v['sum_asset_money_navtive'] = round(str_replace(',','',$v['asset_money_navtive'])  - str_replace(',','',$v_l['asset_money_navtive']),2);

                            $v['sum_asset_product'] = round(str_replace(',','',$v['asset_product'])  - str_replace(',','',$v_l['asset_product']),2);
                            $v['sum_asset_product_navtive'] = round(str_replace(',','',$v['asset_product_navtive'])  - str_replace(',','',$v_l['asset_product_navtive']),2);

                            $v['sum_finance_debt'] = round(str_replace(',','',$v['finance_debt'])  - str_replace(',','',$v_l['finance_debt']),2);
                            $v['sum_finance_debt_navtive'] = round(str_replace(',','',$v['finance_debt_navtive'])  - str_replace(',','',$v_l['finance_debt_navtive']),2);
                            array_push($sub_data[$key], $v);
                        }
                    }
                }
            }
        }
    }
    return $sub_data;
}

function exportExcel(){
    Vendor('PHPExcel.PHPExcel');
    $formatData = S(session('admin')['id'].'formatData');
    $workingCapital = S(session('admin')['id'].'workingCapital');
    $objPHPExcel=new \PHPExcel();//实例化PHPExcel类， 等同于在桌面上新建一个excel

    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

    $objSheet=$objPHPExcel->getActiveSheet();//获得当前活动sheet
    $objSheet->setCellValue("A1","日期：".$workingCapital["日期"]);
    $objSheet->setCellValue("A5","账号")->setCellValue("B5","运营资金")->setCellValue("C5","资产现金")
        ->setCellValue("D5","资产品")->setCellValue("E5","融资负债/授信")
        ->setCellValue("F5","应收")->setCellValue("G5","应付")->setCellValue("H5","资产现金+-")
        ->setCellValue("I5","资产品+-")->setCellValue("J5","持仓授信+-")->setCellValue("K5","备注")->setCellValue("L5","制单人");
    $objSheet->setCellValue("A6",'所有部门总和')
        ->setCellValue("B6",' '.number_format($workingCapital['所有部门总和'],0,'.',''))
        ->setCellValue("C6",' '.number_format($formatData['所有部门总和'][0]['asset_money'],2,'.',''))
        ->setCellValue("D6",' '.number_format($formatData['所有部门总和'][0]['asset_product'],2,'.',''))
        ->setCellValue("E6",' '.number_format($formatData['所有部门总和'][0]['finance_debt'],2,'.',''))
        ->setCellValue("F6",' '.number_format($formatData['所有部门总和'][0]['receivable'],2,'.',''))
        ->setCellValue("G6",' '.number_format($formatData['所有部门总和'][0]['payable'],2,'.',''))
        ->setCellValue("H6",' '.number_format($formatData['所有部门总和'][0]['sum_asset_money'],2,'.',''))
        ->setCellValue("I6",' '.number_format($formatData['所有部门总和'][0]['sum_asset_product'],2,'.',''))
        ->setCellValue("G6",' '.number_format($formatData['所有部门总和'][0]['sum_finance_debt'],2,'.',''))
        ->setCellValue("K6",' '.number_format($formatData['所有部门总和'][0]['sum_finance_debt'],2,'.',''))
        ->setCellValue("L6","");
    unset($formatData['所有部门总和']);
    $i = 7;
    foreach($formatData as $key=>$value){
        $objSheet->setCellValue("A".$i,$key);
        $objSheet->setCellValue("B".$i,' '.number_format($workingCapital[$key],0,'.',''));
        $objSheet->getStyle('A'.$i.':L'.$i)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $objSheet->getStyle('A'.$i.':L'.$i)->getFill()->getStartColor()->setRGB('00ffff');
        foreach($value as $k=>$v){
            $i += 1;
            if($v['bank_category'] == 1){
                $objSheet->setCellValue("A".$i,$v['name'])
                    ->setCellValue("C".$i,str_replace(',','','$'.number_format($v['asset_money_navtive'],2)))
                    ->setCellValue("D".$i, str_replace(',','','$'.number_format($v['asset_product_navtive'],2)))
                    ->setCellValue("E".$i,str_replace(',','','$'.number_format($v['finance_debt_navtive'],2)))
                    ->setCellValue("F".$i,str_replace(',','','$'.number_format($v['receivable_navtive'],2)))
                    ->setCellValue("G".$i,str_replace(',','','$'.number_format($v['payable_navtive'],2)))
                    ->setCellValue("H".$i,'$'.str_replace(',','',number_format($v['sum_asset_money_navtive'],2)))
                    ->setCellValue("I".$i,'$'.str_replace(',','',number_format($v['sum_asset_product_navtive'],2)))
                    ->setCellValue("J".$i,'$'.str_replace(',','',number_format($v['sum_finance_debt_navtive'],2)))
                    ->setCellValue("K".$i,$v['remark'])
                    ->setCellValue("L".$i,$v['user_name']);
            }else {
                if ($v['name'] == '部门总和') {
                    $objSheet->setCellValue("A".$i,$v['name'])
                        ->setCellValue("C".$i,' '.number_format($v['asset_money'],2,'.',''))
                        ->setCellValue("D".$i, ' '.number_format($v['asset_product'],2,'.',''))
                        ->setCellValue("E".$i,' '.number_format($v['finance_debt'],2,'.',''))
                        ->setCellValue("F".$i,' '.number_format($v['receivable'],2,'.',''))
                        ->setCellValue("G".$i,' '.number_format($v['payable'],2,'.',''))
                        ->setCellValue("H".$i,' '.number_format($v['sum_asset_money'],2,'.',''))
                        ->setCellValue("I".$i,' '.number_format($v['sum_asset_product'],2,'.',''))
                        ->setCellValue("J".$i,' '.number_format($v['sum_finance_debt'],2,'.',''))
                        ->setCellValue("K".$i,$v['remark'])
                        ->setCellValue("L".$i,$v['user_name']);
                } else {
                    $objSheet->setCellValue("A".$i,$v['name'])
                        ->setCellValue("C".$i,' '.str_replace(',','',$v['asset_money']))
                        ->setCellValue("D".$i,' '.str_replace(',','',$v['asset_product']))
                        ->setCellValue("E".$i,' '.str_replace(',','',$v['finance_debt']))
                        ->setCellValue("F".$i,' '.str_replace(',','',$v['receivable']))
                        ->setCellValue("G".$i,' '.str_replace(',','',$v['payable']))
                        ->setCellValue("H".$i,' '.number_format($v['sum_asset_money'],2,'.',''))
                        ->setCellValue("I".$i,' '.number_format($v['sum_asset_product'],2,'.',''))
                        ->setCellValue("J".$i,' '.number_format($v['sum_finance_debt'],2,'.',''))
                        ->setCellValue("K".$i,$v['remark'])
                        ->setCellValue("L".$i,$v['user_name']);
                }
            }
        }
        $i += 1;
    }
    return $objPHPExcel;
}
function browser_export($type,$filename){
    if($type=="Excel5"){
        header('Content-Type: application/vnd.ms-excel');//告诉浏览器将要输出excel03文件
    }else{
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器数据excel07文件
    }
    header('Content-Disposition: attachment;filename="'.$filename.'"');//告诉浏览器将输出文件的名称
    header('Cache-Control: max-age=0');//禁止缓存
}