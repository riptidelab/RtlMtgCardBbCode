<?php
class RtlMtgCardBbCode_EventListener_BbCode
{
    public static function listen($class, array &$extend)
    {
        if ($class == 'XenForo_BbCode_Formatter_Base')
        {
            $extend[] = 'RtlMtgCardBbCode_BbCode_Formatter_Base';
        }
    }
}
