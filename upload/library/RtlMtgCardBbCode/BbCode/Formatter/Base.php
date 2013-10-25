<?php

//reshaped as parser-only for BB Code Manager version 1.3.4
class RtlMtgCardBbCode_BbCode_Formatter_Base
{
    public static function parseUrlOptions($tag)
    {
        $attributes = explode('" url="', $tag);
        return $attributes;
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

    public static function parseCubeDeck(array $tag, array $rendererStates, &$parentClass)
    {
        $CARD_WIDTH = 147;
        $TUCKED_WIDTH = 37;
        $title = null;
        $url = null;

        if ($tag['option'] != NULL) {
            if (is_array(RtlMtgCardBbCode_BbCode_Formatter_Base::parseUrlOptions($tag['option']))
                && count(RtlMtgCardBbCode_BbCode_Formatter_Base::parseUrlOptions($tag['option'])) > 1) {

                $attributes = RtlMtgCardBbCode_BbCode_Formatter_Base::parseUrlOptions($tag['option']);
                $title = $attributes[0];
                $url = $attributes[1];
            }
            else {
                $title = $tag['option'];
            }
        }

        if ($title) {
            if ($url) {
                $response = "<h4><a href=\"http://" . $url . "\" target=\"_blank\">" . $title . "</a></h4>";
            }
            else {
                $response = "<h4>" . $title . "</h4>";
            }
        }

        $response .= '<table class="cubedecktable"><tr><td valign="top" style="border:none;">';

        $lines = preg_split("/[\n\r]/", $tag['children'][0]);

        $current_body = '';
        $current_title = '';
        $current_count = 0;
        $land = false;
        $columns = 1;
        $max_width = 0;
        $current_width = 0;

        foreach ($lines as &$line) {

            $line = trim($line);

            if (preg_match('/^([0-9]+)(.*)/', $line, $bits)) {

                // It's got a number. It's a card!
                $card = trim($bits[2]);
                $comment = null;

                // Capture any comments, indicated by # character
                if (strpos($card, '#') !== false) {
                    $nibbles = preg_split('/#/', $card, 2);
                    $card = trim($nibbles[0]);
                    $comment = $nibbles[1];
                }

                $card = str_replace("�", "'", $card);

                $basic = false;
                if (strcasecmp($card, "plains") == 0 || strcasecmp($card, "island") == 0
                    || strcasecmp($card, "swamp") == 0 || strcasecmp($card, "mountain") == 0
                    || strcasecmp($card, "forest") == 0) {
                    $basic = true;
                }

                if ($land && $basic) {

                    // This is the width our tucked train of cards will add to the row
                    $added_width = $CARD_WIDTH + ($bits[1] - 1) * $TUCKED_WIDTH;

                    // Start a new line for basics?
                    if ($added_width + $current_width > $max_width) {

                        $line = '</td></tr><tr><td colspan="' . $columns
                            . '" valign="top" style="border:none;"><div class="cubedeckimage cubedeckland">';

                        $current_width = $added_width;
                    }
                    else {
                        $line = '<div class="cubedeckimage cubedeckland">';
                        $current_width += $added_width;
                    }
                }
                else if ($land) {
                    $line = '<div class="cubedeckimage cubedeckland">';
                    $current_width += $CARD_WIDTH;

                    // Keep track of natural wrapping
                    if ($current_width >= $max_width) {
                        $current_width = 0;
                    }
                }
                else {
                    $line = '<div class="cubedeckimage">';
                }

                $line .= '<a href="http://deckbox.org/mtg/'. $card . '" target="_blank">'
                    . '<img src="http://gatherer.wizards.com/Handlers/Image.ashx?type=card&name='
                    . $card . '" onload="this.width*=0.72;this.onload=null;" />'
                    . '</a></div>';

                if ($land && $basic) {
                    for ($i = 1; $i < $bits[1]; $i++) {
                        $line .= '<div class="cubedecktucked cubedeckland">'
                            . '<a href="http://deckbox.org/mtg/'. $card . '" target="_blank">'
                            . '<img src="http://gatherer.wizards.com/Handlers/Image.ashx?type=card&name='
                            . $card . '" onload="this.width*=0.72;this.onload=null;" />'
                            . '</a></div>';
                    }
                }
                else {
                    for ($i = 1; $i < $bits[1]; $i++) {
                        $current_body .= $line;

                        if ($land) {
                            $current_width += $CARD_WIDTH;

                            // Keep track of natural wrapping
                            if ($current_width >= $max_width) {
                                $current_width = 0;
                            }
                        }
                    }
                }

                $current_body .= $line;
                $current_count += intval($bits[1]);
            }
            else {

                // No number. Category!
                // If this was not the first one, we put the previous one into the response body.
                if ($current_title != "") {
                    $response .= $current_body . '<br/>';
                }

                if (preg_match("/^Two/", $line)
                    || preg_match("/^Three/", $line) || preg_match("/^Four/", $line)
                    || preg_match("/^Five/", $line) || preg_match("/^Six/", $line)) {
                    $response .= '</td><td valign="top" style="border:none;">';
                    $columns += 1;
                }
                else if (preg_match("/^Land/", $line)) {
                    // Land rows, ahoy! Lock in the number of columns, and the associated maximum width.
                    $response .= '</td></tr><tr><td colspan="' . $columns . '" valign="top" style="border:none;">';
                    $land = true;
                    $max_width = $CARD_WIDTH * $columns;
                }

                $current_title = $line; $current_count = 0; $current_body = '';

            }
        }

        $response .= '<br />' . $current_body;

        $response .= '</td></tr></table>';

        return $response;
    }

    public static function parseTagDeck(array $tag, array $rendererStates, &$parentClass)
    {
        $title = null;

        if ($tag['option'] != NULL)
        {
            $title = $tag['option'];
        }

        if ($title) {
            $response = "<h4>" . $title . "</h4>";
        }

        $response .= '<table class="cubedeck"><tr><td valign="top" style="border:none;">';

        $lines = preg_split("/[\n\r]/", $tag['children'][0]);

        $current_body = '';
        $current_title = '';
        $current_count = 0;

        foreach ($lines as &$line) {

            $line = trim($line);

            if (preg_match('/^([0-9]+)(.*)/', $line, $bits)) {

                // It's got a number. It's a card!
                $card = trim($bits[2]);
                $comment = null;

                // Capture any comments, indicated by # character
                if (strpos($card, '#') !== false) {
                    $nibbles = preg_split('/#/', $card, 2);
                    $card = trim($nibbles[0]);
                    $comment = $nibbles[1];
                }

                $card = str_replace("�", "'", $card);
                $line = $bits[1] . '&nbsp;<a href="http://deckbox.org/mtg/'. $card .
                    '">' . $card . '</a>';
                if ($comment) {
                    $line .= '&nbsp;#' . $comment;
                }

                $current_body .= $line . '<br/>';
                $current_count += intval($bits[1]);
            }
            else {

                // No number. Category!
                // If this was not the first one, we put the previous one into the response body.
                if ($current_title != "") {

                    $response .= '<span style="font-weight:bold">' . $current_title . ' (' .
                        $current_count . ')</span><br />';
                    $response .= $current_body . '<br/>';

                }

                if (preg_match("/^Sideboard/", $line) || preg_match("/^Land/", $line)) {
                    $response .= '</td><td valign="top" style="border:none;">';
                }

                $current_title = $line; $current_count = 0; $current_body = '';

            }
        }

        $response .= '<span style="font-weight:bold">' . $current_title . ' (' . $current_count .
            ')</span><br />' . $current_body;

        $response .= '</td></tr></table>';

        return $response;
    }

    public static function parseTagCard(array $tag, array $rendererStates, &$parentClass)
    {

        if ($tag['option'] != NULL)
        {
            $card = $tag['option'];
            $card = strtolower(trim($card));
            preg_match('/([\s0-9x-]*)(.*)/', $card, $bits);

            return $bits[1].'<a href="http://deckbox.org/mtg/'.$bits[2].'">'.$tag['children'][0].'</a>';
        }
        else
        {
            $txt = $tag['children'][0];

            $cards = preg_split("/[\r\n]/", $txt);

            foreach($cards as &$card) {
                $card = trim($card);
                $lcCard = strtolower($card);
                preg_match('/([\s0-9x-]*)(.*)/', $lcCard, $bits);
                preg_match('/([\s0-9x-]*)(.*)/', $card, $origBits);
                $card = $bits[1].'<a href="http://deckbox.org/mtg/'.$bits[2].'">'.$origBits[2].'</a>';
            }

            return implode("<br/>", $cards);
        }
    }
}