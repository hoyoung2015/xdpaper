<?php
/**
 * Created by PhpStorm.
 * User: hoyoung
 * Date: 16-3-10
 * Time: 下午7:42
 */
echo 'hh';
$arr = array(
    array(
        'name'=>'a'
    ),
    array(
        'name'=>'b'
    ),
    array(
        'name'=>'c'
    )
);
print_r($arr);

array_shift($arr);

print_r($arr);



print_r(json_decode("[{\"age\":12}]",true));

echo vsprintf("%s word",array('hello'));
print_r(array());
echo preg_match('/^([\{u4e00}-\{u9fa5}]{1,},){0,}([\{u4e00}-\{u9fa5}]{1,})$/',"测试");