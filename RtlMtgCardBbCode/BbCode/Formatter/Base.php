<?php

//followed the tutorial at: http://kingkovifor.com/creating-a-custom-bbcode-in-xenforo-a-comprehensive-guide
class RtlMtgCardBbCode_BbCode_Formatter_Base extends XFCP_RtlMtgCardBbCode_BbCode_Formatter_Base
{
    protected $_tags;
    public function getTags()
    {
        $this->_tags = parent::getTags();
        $this->_tags['ci'] = array(
            'parseCallback' => array($this, 'parseValidatePlainIfNoOption'),
            'callback' => array($this, 'parseCardImage')
        );
        return $this->_tags;
    }

    public static function parseCardImage(array $tag, array $rendererStates, &$parentClass)
    {
        $txt = $tag['children'][0];

        $small = null;
        if ($tag['option'] != NULL)
        {
            $small = $tag['option'];
        }

        $cards = preg_split("/[\r\n]/", $txt);

        foreach($cards as &$card) {
            $card = trim($card);
            $card = strtolower($card);
            if (strcasecmp($small, "small") == 0) {
                $card = '<img src="http://gatherer.wizards.com/Handlers/Image.ashx?type=card&name='
                    . $card .'" onload="this.width*=0.75;this.onload=null;" />';
            }
            else {
                $card = '<img src="http://gatherer.wizards.com/Handlers/Image.ashx?type=card&name='
                    . $card .'" />';
            }
        }

        return implode("", $cards);
    }
}