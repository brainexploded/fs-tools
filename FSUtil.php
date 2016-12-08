<?php
namespace Brainexploded\FSTools;

class FSUtil
{
    public static function getFileExtension($filename)
    {
        if (strrpos($filename, '.') === false) {
            return false;
        }
        return substr($filename, strrpos($filename, '.') + 1);
    }
}
