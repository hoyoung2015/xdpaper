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
//echo preg_match('/^([\{u4e00}-\{u9fa5}]{1,},){0,}([\{u4e00}-\{u9fa5}]{1,})$/',"测试");

$arr = array(1,2,3,4);
echo join(',',$arr);
print_r(array());

//$regx = '/^([\u4e00-\u9fa5\w]{1,},){0,}([\u4e00-\u9fa5\w]{1,})$/';
$regx = "/^([\x{4e00}-\x{9fa5}\da-zA-Z]{1,},){0,}([\x{4e00}-\x{9fa5}\da-zA-Z]{1,})$/u";
echo preg_match($regx,'学硕,2014');
echo ' ';
echo preg_match($regx,'英文,逗号');
echo ' ';
echo preg_match($regx,'english,12a');
echo ' ';
echo preg_match($regx,'中文，逗号');
//echo preg_match('/[\x{4e00}-\x{9fa5}]+/u','中文');