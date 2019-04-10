<?php
/**
 * Created by Gerrit Thomson.
 * User: gerrit
 * Date: 10/04/2019
 * Time: 22:09
 */
$data = array_map(function($x){
                    if (($x%3 != 0) && ($x%5 != 0)){
                        return $x;
                    }
                    $rData = '';
                    if($x%3 == 0){
                        $rData.='foo';
                    }
                    if($x%5 == 0){
                        $rData .= 'bar';
                    }
                    return $rData;
                },
                range(1,100)
                );
echo join($data,',');
