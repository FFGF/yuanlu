<?php
//创建密码
function createPassword($password) {
    if (empty($password)) {
        return false;
    } else {
        $salt = C('PASSWORDINTER');
        return md5($password . $salt);
    }
}
