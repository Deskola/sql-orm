<?php

if (!function_exists('dd')) {
   function dd()
   {
        array_map(function ($content) {
            echo "<pre>";
            var_dump($content);
            echo "</pre>";
            echo "<hr>";
        }, func_get_args());

        die;
   }
}