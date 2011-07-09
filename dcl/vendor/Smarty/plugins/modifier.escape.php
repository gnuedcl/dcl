<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty escape modifier plugin
 *
 * Type:     modifier<br>
 * Name:     escape<br>
 * Purpose:  Escape the string according to escapement type
 * @link http://smarty.php.net/manual/en/language.modifier.escape.php
 *          escape (Smarty online manual)
 * @param string
 * @param html|htmlall|url|quotes|hex|hexentity|javascript
 * @return string
 */
function smarty_modifier_escape($string, $esc_type = 'html')
{
    switch ($esc_type) {
        case 'html':
            return htmlspecialchars($string, ENT_QUOTES);

        case 'htmlall':
            return htmlentities($string, ENT_QUOTES);

		case 'link':
			$sText = nl2br(htmlspecialchars($string));
			$sRetVal = preg_replace('#(http|ftp|telnet|irc|https)://[^<>[:space:]]+[[:alnum:]/]#i', '<a target="_blank" href="\0">\0</a>', $sText);

			// Pseudo stuff
			$sRetVal = preg_replace('#dcl://workorders/([0-9]+)[-]([0-9]+)#i', '<a href="' . menuLink() . '?menuAction=boWorkorders.viewjcn&jcn=\1&seq=\2">\0</a>', $sRetVal);
			$sRetVal = preg_replace('#dcl://tickets/([0-9]+)#i', '<a href="' . menuLink() . '?menuAction=boTickets.view&ticketid=\1">\0</a>', $sRetVal);
			$sRetVal = preg_replace('#dcl://projects/([0-9]+)#i', '<a href="' . menuLink() . '?menuAction=boProjects.viewproject&wostatus=0&project=\1">\0</a>', $sRetVal);

			return $sRetVal;

        case 'url':
            return urlencode($string);
            
        case 'date':
            $o = new DbProvider;
            return $o->FormatDateForDisplay($string);

        case 'timestamp':
            $o = new DbProvider;
            return $o->FormatTimeStampForDisplay($string);

        case 'rawurl':
			return rawurlencode($string);

        case 'quotes':
            // escape unescaped single quotes
            return preg_replace("%(?<!\\\\)'%", "\\'", $string);
            
        case 'utf8xml':
        	return utf8_encode(htmlspecialchars($string, ENT_NOQUOTES));

        case 'hex':
            // escape every character into hex
            $return = '';
            for ($x=0; $x < strlen($string); $x++) {
                $return .= '%' . bin2hex($string[$x]);
            }
            return $return;

        case 'hexentity':
            $return = '';
            for ($x=0; $x < strlen($string); $x++) {
                $return .= '&#x' . bin2hex($string[$x]) . ';';
            }
            return $return;

        case 'javascript':
            // escape quotes and backslashes and newlines
            return strtr($string, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n'));

        default:
            return $string;
    }
}

/* vim: set expandtab: */

?>
