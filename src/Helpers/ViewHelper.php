<?php
class ViewHelper
{
    public static function init($data)
    {
        foreach ($data as $key => $value) {
            if (!isset($GLOBALS[$key])) {
                $GLOBALS[$key] = $value;
            }
        }
    }
}
