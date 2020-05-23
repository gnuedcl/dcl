<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2004 Free Software Foundation
 *
 * Double Choco Latte is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * Double Choco Latte is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * Select License Info from the Help menu to view the terms and conditions of this license.
 *
 * License Note:
 *
 * format_html contains some code from WIKIPLAM.  The original WIKIPLAM source is covered
 * by the Free Art License (http://artlibre.org/licence.php/lalgb.html)
 */

LoadStringResource('wiki');
class htmlWiki
{
	var $t;
	var $aLockedPages;

	function __construct()
	{
		$this->t = new SmartyHelper();

		$this->aLockedPages = array('RecentChanges');
	}

	function SetDescription($type)
	{
		if ($type == DCL_ENTITY_GLOBAL)
			return;
		
		$id = Filter::RequireInt($_REQUEST['id']);

		$oMeta = new DisplayHelper();

		switch ($type)
		{
			case DCL_ENTITY_PROJECT:
				$this->t->assign('VAL_DESCRIPTION', sprintf(DCL_WIKI_PROJECTWIKI, $id, $oMeta->GetProject($id)));
				$this->t->assign('LNK_DESCRIPTION', menuLink('', 'menuAction=Project.Detail&id=' . $id));
				break;
			case DCL_ENTITY_PRODUCT:
				$this->t->assign('VAL_DESCRIPTION', sprintf(DCL_WIKI_PRODUCTWIKI, $oMeta->GetProduct($id)));
				$this->t->assign('LNK_DESCRIPTION', menuLink('', 'menuAction=Product.Detail&id=' . $id));
				break;
			case DCL_ENTITY_ORG:
				$aOrg = $oMeta->GetOrganization($id);
				$this->t->assign('VAL_DESCRIPTION', sprintf(DCL_WIKI_ACCOUNTWIKI, $aOrg['name']));
				$this->t->assign('LNK_DESCRIPTION', menuLink('', 'menuAction=Organization.Detail&org_id=' . $id));
				break;
			case DCL_ENTITY_WORKORDER:
				$id2 = Filter::RequireInt($_REQUEST['id2']);

				$o = new WorkOrderModel();
				$o->LoadByIdSeq($id, $id2);
				$this->t->assign('VAL_DESCRIPTION', sprintf(DCL_WIKI_WOWIKI, $id, $id2, $o->summary));
				$this->t->assign('LNK_DESCRIPTION', menuLink('', 'menuAction=WorkOrder.Detail&jcn=' . $id . '&seq=' . $id2));
				break;
			case DCL_ENTITY_TICKET:
				$o = new TicketsModel();
				$o->Load($id);
				$this->t->assign('VAL_DESCRIPTION', sprintf(DCL_WIKI_TICKETWIKI, $id, $o->summary));
				$this->t->assign('LNK_DESCRIPTION', menuLink('', 'menuAction=boTickets.view&ticketid=' . $id));
				break;
			default:
				return;
		}
	}

	function show()
	{
		global $dcl_info, $g_oSec;

		commonHeader();

		$type = Filter::RequireInt($_REQUEST['type']);

		if (($id = @Filter::ToInt($_REQUEST['id'])) === null && $type != DCL_ENTITY_GLOBAL)
			throw new InvalidDataException();

		if ($type == DCL_ENTITY_GLOBAL)
			$id = 0;

		if (isset($_REQUEST['name']))
			$name = @Filter::RequireWikiName($_REQUEST['name']);

		if (!isset($name) || $name == '')
			$name = 'FrontPage';

		$editmode = @$_REQUEST['editmode'];
		$text = @$_REQUEST['text'];
		if (($id2 = @Filter::ToInt($_REQUEST['id2'])) === null)
		{
			if ($type == DCL_ENTITY_WORKORDER)
				throw new InvalidDataException();

			$id2 = 0;
		}

		if ($dcl_info['DCL_WIKI_ENABLED'] != 'Y' || !$g_oSec->HasPerm($type, DCL_PERM_VIEWWIKI))
			throw new PermissionDeniedException();

		if (in_array($name, $this->aLockedPages))
			unset($editmode);

		$obj = new WikiModel();
		if ((!$obj->PageExists($type, $id, $id2, $name) || $obj->LoadPage($type, $id, $id2, $name) == -1) && $name != 'RecentChanges')
			$obj = $this->quickwiki($type, $id, $id2, $name);

		$extraParams = "type=$type";
		if ($type != DCL_ENTITY_GLOBAL)
			$extraParams .= "&id=$id";

		if ($type == DCL_ENTITY_WORKORDER)
			$extraParams .= "&id2=$id2";

		$this->t->assign('VAL_TITLE', (isset($editmode) && $editmode == 'edit') ? sprintf(DCL_WIKI_EDITINGFORMAT, $name) : $name);
		$this->t->assign('VAL_TEXT', $text);
		$this->t->assign('LNK_ACTION', menuLink());
		$this->t->assign('VAL_PAGENAME', $name);
		$this->t->assign('VAL_ENTITYTYPEID', $type);
		$this->t->assign('VAL_ENTITYID', $id);
		$this->t->assign('VAL_ENTITYID2', $id2);
		$this->t->assign('TXT_SAVE', STR_CMMN_SAVE);
		$this->t->assign('TXT_RESET', STR_CMMN_RESET);
		$this->t->assign('TXT_CANCEL', STR_CMMN_CANCEL);
		$this->t->assign('TXT_VIEW', STR_CMMN_VIEW);
		$this->t->assign('LNK_CANCEL', menuLink('', "menuAction=htmlWiki.show&name=" . urlencode($name) . "&$extraParams"));
		$this->t->assign('LNK_EDITTHISPAGE', menuLink('', "menuAction=htmlWiki.show&editmode=edit&name=" . urlencode($name) . "&$extraParams"));
		$this->t->assign('TXT_EDITTHISPAGE', DCL_WIKI_EDITTHISPAGE);
		$this->t->assign('LNK_RETURNTOFRONTPAGE', menuLink('', "menuAction=htmlWiki.show&name=FrontPage&$extraParams"));
		$this->t->assign('TXT_RETURNTOFRONTPAGE', DCL_WIKI_RETURNTOFRONTPAGE);
		$this->t->assign('LNK_RECENTCHANGES', menuLink('', "menuAction=htmlWiki.show&name=RecentChanges&$extraParams"));
		$this->t->assign('TXT_VIEWRECENTCHANGES', DCL_WIKI_VIEWRECENTCHANGES);
		$this->t->assign('TXT_THISPAGECANNOTBEEDITED', DCL_WIKI_THISPAGECANNOTBEEDITED);

		$this->SetDescription($type);

		if ($obj->page_name != '' || $name == 'RecentChanges')
		{
			$i = 0;

			if ($obj->page_text == '')
		        $tf = sprintf(DCL_WIKI_NEWPAGEFORMAT, $name);
			else
				$tf =  htmlspecialchars($obj->page_text, ENT_QUOTES, 'UTF-8');

			if (!isset($editmode) || $editmode != 'edit')
			{
	        	if ($name == 'RecentChanges')
				{
					$tf = $this->RecentChanges();
					$tf = $this->format_html($tf);
				}
				else
				{
					$tf = $this->format_html($tf);
				}

				$this->t->assign('VAL_TEXT', $tf);
				$this->t->assign('VAL_MODE', 'view');
	        }
			else
			{
				$this->t->assign('VAL_TEXT', $tf);
				$this->t->assign('VAL_MODE', 'edit');
	        }
		}

		if (in_array($name, $this->aLockedPages))
			$this->t->assign('VAL_MODE', 'locked');

		$this->t->Render('Wiki.tpl');
	}

	function _list($on, $list_type, $numtype="", $close="")
	{
		if ($list_type=="dd")
		{
			if ($on)
				$list_type="dl><dd";
			else
				$list_type="dd></dl";
		}
		else if (!$on && $close !=1)
			$list_type=$list_type."></li";

		if ($on)
		{
			if ($numtype)
			{
				$start=mb_substr($numtype,1);
				if ($start)
					return "<$list_type type='$numtype[0]' start='$start'>";

				return "<$list_type type='$numtype[0]'>";
			}
			return "<$list_type>\n";
		}
		else
		{
			return "</$list_type>\n";
		}
	}

	function _table_span($str)
	{
		$len = mb_strlen($str) / 2;
		if ($len > 1)
			return " align=\"center\" colspan=\"$len\"";

		return "";
	}

	function _table($on, $attr = "")
	{
		if ($on)
			return "<table class=\"wiki\" cellpadding=\"3\" cellspacing=\"2\" $attr>\n";
		else
			return "</table>\n";
	}

	function format_html($text)
	{
		if (($type = Filter::ToInt($_REQUEST['type'])) === null)
		{
			throw new InvalidDataException();
		}

		if ($type != DCL_ENTITY_GLOBAL && ($id = Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if ($type == DCL_ENTITY_WORKORDER)
			$id2 = @Filter::ToInt($_REQUEST['id2']);
		else
			$id2 = 0;

		$retVal = '';
		$bWikiCode = false;
		$aText = explode("\n", $text);
		$in_pre = 0;
		$in_p = 0;
		$in_li = 0;
		$li_open = 0;
		$in_table = 0;
		$indent_list[0] = 0;
		$indent_type[0] = "";

		foreach ($aText as $line)
		{
			// {{{ and }}} start and end code blocks (preformatted text) ala MoinMoin
			$line = chop($line);
			if ($line == '{{{' && !$bWikiCode)
			{
				$retVal .= '<pre class="wikicode">';
				$bWikiCode = true;
				continue;
			}
			else if ($line == '}}}' && $bWikiCode)
			{
				$retVal .= '</pre>';
				$bWikiCode = false;
				continue;
			}
			else if ($bWikiCode)
			{
				// No processing in wiki code block
				$retVal .= $line . "\n";
				continue;
			}

			if ($line == '')
			{
				$retVal .= '<p>';
				continue;
			}

			// __text__ for title bar
			$line = preg_replace("/^(_){2}(.*)(_){2}/", "<div style=\"font-weight: bold; width: 100%; background-color: #aaaaaa;\"> \\2 </div>", $line);

			// ''' bold '''
			$line=preg_replace("/'''([^']*)'''/","<b>\\1</b>",$line);
			$line=preg_replace("/(?<!')'''(.*)'''(?!')/","<b>\\1</b>",$line);

			// '' italic ''
			$line=preg_replace("/''([^']*)''/","<i>\\1</i>",$line);
			$line=preg_replace("/(?<!')''(.*)''(?!')/","<i>\\1</i>",$line);

			# ^ superscript ^, _subscripts_
			$line=preg_replace("/\^([^\^]+)\^/","<sup>\\1</sup>",$line);
			$line=preg_replace("/(?: |^)_([^ _]+)_/","<sub>\\1</sub>",$line);

			// ` text ` for monospace
			$line = preg_replace("/\`(('?[^\`])*)\`/", "<tt>\\1</tt>", $line);

			// {{{ text }}} also for monospace
			$line = preg_replace("/\{\{\{(.*)\}\}\}/", "<tt>\\1</tt>", $line);

			// ===== text ===== for h5
			$line = preg_replace("/^=====(.*)=====/", "<h5 class=\"wiki\">\\1</h5>", $line);

			// ==== text ==== for h4
			$line = preg_replace("/^====(.*)====/", "<h4 class=\"wiki\">\\1</h4>", $line);

			// === text === for h3
			$line = preg_replace("/^===(.*)===/", "<h3 class=\"wiki\">\\1</h3>", $line);

			// == text == for h2
			$line = preg_replace("/^==(.*)==/", "<h2 class=\"wiki\">\\1</h2>", $line);

			// = text = for h1
			$line = preg_replace("/^=(.*)=/", "<h1 class=\"wiki\">\\1</h1>", $line);

			// TwoWords crammed together for internal links
			$twoWordsPattern = '/([\s;<>\'\(]|^)(([A-Z][a-z]+){2,})([\s.,!?&<>\)]|$)/';
			if ($type == DCL_ENTITY_GLOBAL)
			{
				$line = preg_replace($twoWordsPattern, "\\1<a class=\"wiki\" href=\"".menuLink()."?menuAction=htmlWiki.show&type=".$type."&name=\\2\">\\2</a>\\4", $line);
			}
			else if ($type == DCL_ENTITY_WORKORDER)
			{
				$line = preg_replace($twoWordsPattern, "\\1<a class=\"wiki\" href=\"".menuLink()."?menuAction=htmlWiki.show&type=$type&id=$id&id2=$id2&name=\\2\">\\2</a>\\4", $line);
			}
			else
			{
				$line = preg_replace($twoWordsPattern, "\\1<a class=\"wiki\" href=\"".menuLink()."?menuAction=htmlWiki.show&type=$type&id=$id&name=\\2\">\\2</a>\\4", $line);
			}

			// xxx://target for any link ex. ftp:// http://
			$line = preg_replace("/([\s;<>\(]|^)([a-z0-9]+):\/\/([^[:space:]]*)([[:alnum:]#?\/=])([[:space:].,&<>\)]|$)/i",
								"\\1<a class=\"wiki\" href=\"\\2://\\3\\4\" target=\"_blank\">\\2://\\3\\4</a>\\5", $line);

			// name@domain for email
			$line = preg_replace("/([_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+)/i", "<a class=\"wiki\" href=\"mailto:\\1\">\\1</a>", $line);

			// [http://link] for images
			$line = preg_replace("/\[http:\/\/([^[:space:]]*)([[:alnum:]#?\/&=])\]/i", "<img src=\"http://\\1\\2\" alt=\"http://\\1\\2\">", $line);

			// New syntax for wiki pages ((name|desc)) Where desc can be anything
			$line = preg_replace('/\(\((([A-Z][a-z]+){2,})\|([^\~]+)\)\)/',"<a class=\"wiki\" title='$3' href=\"".menuLink()."?menuAction=htmlWiki.show&name=$1&type=$type\">$3</a>",$line);

			// Replace colors ~~color:text~~
			$line = preg_replace("/\~\~([^\:]+):([^\~]+)\~\~/","<span style='color:$1;'>$2</span>",$line);

			// Underlined text ___ underlined text ___
			$line = preg_replace("/___([^\=]+)___/","<span style='text-decoration:underline;'>$1</span>",$line);

			// fixes Ampersand Problem
			$line = preg_replace("/&(amp;)+/", "&", $line);

			// %% TwoWords %% Wiki Name with no link created.
			$line = preg_replace("/%%(([A-Z][a-z]+){2,})%%/", "\\1", $line);

			// --- or more dashes makes a horizontal bar
			$line = preg_replace("/^---(-)*/", "<hr size=\"1\" />", $line);

			// bullet
			if (preg_match("/^(\s*)/", $line, $match))
			{
				$open = "";
				$close = "";
				$indtype = "dd";
				$indlen = mb_strlen($match[0]);

				if ($indlen > 0)
				{
					$line = mb_substr($line, $indlen);
					if (preg_match("/^(\*\s*)/", $line, $limatch))
					{
						$line=preg_replace("/^(\*\s*)/", "<li>",$line);
						if ($indent_list[$in_li] == $indlen)
							$line = "</li>\n" . $line;

						$numtype = "";
						$indtype = "ul";
					}
					else if (preg_match("/^((\d+|[aAiI])\.)(#\d+)?/", $line, $limatch))
					{
						$line = preg_replace("/^((\d+|[aAiI])\.(#\d+)?)/", "<li>", $line);
						if ($indent_list[$in_li] == $indlen)
							$line = "</li>\n" . $line;

						$numtype=$limatch[2];
						if (isset($limatch[3]))
							$numtype .= mb_substr($limatch[3], 1);
						$indtype = "ol";
					}
				}

				if ($indent_list[$in_li] < $indlen)
				{
					$in_li++;
					$indent_list[$in_li] = $indlen;
					$indent_type[$in_li] = $indtype;

					if (isset($numtype))
						$open .= $this->_list(1, $indtype, $numtype);
				}
				else if ($indent_list[$in_li] > $indlen)
				{
					while($in_li >= 0 && $indent_list[$in_li] > $indlen)
					{
						if ($indent_type[$in_li] != 'dd' && $li_open == $in_li)
							$close.="</li>\n";

						$close .= $this->_list(0, $indent_type[$in_li], "", $in_li);
						unset($indent_list[$in_li]);
						unset($indent_type[$in_li]);
						$in_li--;
					}
				}

				if ($indent_list[$in_li] <= $indlen || $limatch)
					$li_open = $in_li;
				else
					$li_open = 0;
			}

			// tables
			if (!$in_table && preg_match("/^\|\|.*\|\|$/", $line))
			{
				$open .= $this->_table(1);
				$in_table = 1;
			}
			else if ($in_table && !preg_match("/^\|\|.*\|\|$/", $line))
			{
				$close = $this->_table(0) . $close;
				$in_table = 0;
			}

			if ($in_table)
			{
				$line = preg_replace_callback('/^((?:\|\|)+)(.*)\|\|$/', 'self::TableRow', $line);
				$line = preg_replace_callback('/((\|\|)+)/', 'self::TableCell', $line);
				$line = str_replace('\"', '"' ,$line);
			}

			$line = $close . $open . $line;
			$open = "";
			$close = "";

			$retVal .= $line;
			if (!$in_table)
				$retVal .= '<br>';
		}

		// Tidy up any loose ends
		$close = '';
		if ($in_table)
			$close .= "</table>\n";

		while ($in_li >= 0 && $indent_list[$in_li] > 0)
		{
			$close .= $this->_list(0, $indent_type[$in_li]);
			unset($indent_list[$in_li]);
			unset($indent_type[$in_li]);
			$in_li--;
		}

		return $retVal . $close;
	}

	function TableRow($matches)
	{
		return '<tr class=\"wiki\"><td class=\"wiki\"' . $this->_table_span($matches[1]) . '>' . $matches[2] . '</td></tr>';
	}

	function TableCell($matches)
	{
		return '</td><td class=\"wiki\"' . $this->_table_span($matches[1]) . '>';
	}

	function RecentChanges()
	{
		if (($type = Filter::ToInt($_REQUEST['type'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$id = @Filter::ToInt($_REQUEST['id']);
		$id2 = @Filter::ToInt($_REQUEST['id2']);
		
		$list = "||||||'''" . DCL_WIKI_RECENTCHANGES . "'''||\n";
		$list .= "||'''" . DCL_WIKI_PAGE;
		$list .= "'''||'''" . DCL_WIKI_LASTMODIFIED;
		$list .= "'''||'''" . DCL_WIKI_LASTMODIFIEDBY . "'''||\n";

		$obj = new WikiModel();
		$obj->ListRecentChanges($type, $id, $id2);

		$count = 0;
		$twoWordsPattern = '/([\s;<>\'\(]|^)(([A-Z][a-z]+){2,})([\s.,!?&<>\)]|$)/';

		while ($obj->next_record())
		{
			$count++;
			$pageLink = $obj->f(0);
			if (preg_match($twoWordsPattern, $pageLink) !== 1)
				$pageLink = "(($pageLink))";

			$list .= sprintf("|| %s ||%s||%s||\n",
				$pageLink,
				$obj->FormatTimestampForDisplay($obj->f(1)),
				$obj->f(2));
		}

		$list .= "||||||'''" . sprintf(DCL_WIKI_TOTALPAGESFORMAT, $count) . "'''||";

		return $list;
	}

	function getlongip()
	{
		if (getenv('HTTP_X_FORWARDED_FOR'))
			$ipe = getenv('HTTP_X_FORWARDED_FOR');
		else
			$ipe = getenv('REMOTE_ADDR');

		return gethostbyaddr($ipe);
	}

	function postwiki()
	{
		global $g_oSession, $g_oSec, $dcl_info;

		if (($type = Filter::ToInt($_REQUEST['type'])) === null ||
			($id = Filter::ToInt($_REQUEST['id'])) === null
			)
		{
			throw new InvalidDataException();
		}
		
		$name = Filter::RequireWikiName(GPCStripSlashes($_REQUEST['name']));
		$editmode = $_REQUEST['editmode'];
		$text = GPCStripSlashes($_REQUEST['text']);
		$id2 = @Filter::ToInt($_REQUEST['id2']);
		if ($type != DCL_ENTITY_WORKORDER)
			$id2 = 0;
		
		$o = new WikiModel();
		if ($o->LoadPage($type, $id, $id2, $name) != -1)
		{
			$ddate = date("d M Y - H:i"); // Replace <d> tag with current date
			$o->page_text = str_replace("<d>", $ddate, $text);
			$o->page_text = preg_replace("/&(amp;)+/", "&", $text);
			$o->page_ip = $g_oSession->Value('DCLNAME') . ' [' . $this->getlongip() . ']';

			$o->Edit();
		}

		$this->show();
	}

	function quickwiki($type, $id, $id2, $name)
	{
		global $g_oSession;
		
		$o = new WikiModel();
		$o->dcl_entity_type_id = $type;
		$o->dcl_entity_id = $id;
		$o->dcl_entity_id2 = $id2;
		$o->page_name = $name;
		$o->page_text = sprintf(DCL_WIKI_NEWPAGEFORMAT, $name);
		$o->page_ip = $g_oSession->Value('DCLNAME') . ' [' . $this->getlongip() . ']';

		Filter::RequireWikiName($o->page_name);

		$o->Add();
		return $o;
	}
}
