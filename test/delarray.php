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