<?php
    function str_format(){
        $args = func_get_args();
        if(empty($args)) return '';
        $str = $args[0];
        for($i = 0; $i < count($args); $i++){
            if($i == count($args) - 1) break;
            $str = str_replace('{'.$i.'}', $args[$i+1], $str);
        }
        return $str;
    }
?>