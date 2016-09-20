<?php
//源庐的项目

function getCategory($vo){
    foreach($vo as $key=>$value){
        if($key == 'category'){
            return $value['category'];
        }
    }
}