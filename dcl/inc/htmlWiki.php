<?php
/*
 * $Id$
 *
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

	function htmlWiki()
	{
		$this->t = CreateTemplate(array('hForm' => 'htmlWiki.tpl'));

		$this->t->set_block('hForm', 'display', 'hDisplay');
		$this->t->set_block('hForm', 'edit', 'hEdit');
		$this->t->set_block('hForm', 'editlink', 'hEditlink');
		$this->t->set_block('hForm', 'noedit', 'hNoedit');
		$this->t->set_block('hForm', 'wiki', 'hWiki');

		$this->t->set_var('hDisplay', '');
		$this->t->set_var('hEdit', '');
		$this->t->set_var('hEditlink', '');
		$this->t->set_var('hNoedit', '');
		$this->t->set_var('hWiki', '');

		$this->aLockedPages = array('RecentChanges');
	}

	function ParseDescriptionBlock()
	{
		global $dcl_info, $type;
			
		if (($type = Filter::ToInt($_REQUEST['type'])) === null)
		{
			throw new InvalidDataException();
		}

		if ($type == DCL_ENTITY_GLOBAL)
			return;
		
		if (($id = Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}


		$oMeta = new DisplayHelper();

		$sHTML = '';
		switch ($type)
		{
			case DCL_ENTITY_PROJECT:
				$this->t->set_var('VAL_DESCRIPTION', sprintf(DCL_WIKI_PROJECTWIKI, $id, $oMeta->GetProject($id)));
				$this->t->set_var('LNK_DESCRIPTION', menuLink('', 'menuAction=boProjects.viewproject&project=' . $id));
				break;
			case DCL_ENTITY_PRODUCT:
				$this->t->set_var('VAL_DESCRIPTION', sprintf(DCL_WIKI_PRODUCTWIKI, $oMeta->GetProduct($id)));
				$this->t->set_var('LNK_DESCRIPTION', menuLink('', 'menuAction=Product.Detail&id=' . $id));
				break;
			case DCL_ENTITY_ORG:
				$aOrg = $oMeta->GetOrganization($id);
				$this->t->set_var('VAL_DESCRIPTION', sprintf(DCL_WIKI_ACCOUNTWIKI, $aOrg['name']));
				$this->t->set_var('LNK_DESCRIPTION', menuLink('', 'menuAction=Organization.Detail&org_id=' . $id));
				break;
			case DCL_ENTITY_WORKORDER:
				if (($id2 = Filter::ToInt($_REQUEST['id2'])) === null)
				{
					throw new InvalidDataException();
				}
				
				$o = new WorkOrderModel();
				$o->Load($id, $id2);
				$this->t->set_var('VAL_DESCRIPTION', sprintf(DCL_WIKI_WOWIKI, $id, $id2, $o->summary));
				$this->t->set_var('LNK_DESCRIPTION', menuLink('', 'menuAction=boWorkorders.viewjcn&jcn=' . $id . '&seq=' . $id2));
				break;
			case DCL_ENTITY_TICKET:
				$o = new TicketsModel();
				$o->Load($id);
				$this->t->set_var('VAL_DESCRIPTION', sprintf(DCL_WIKI_TICKETWIKI, $id, $o->summary));
				$this->t->set_var('LNK_DESCRIPTION', menuLink('', 'menuAction=boTickets.view&ticketid=' . $id));
				break;
			default:
				return;
		}

		$this->t->parse('hWiki', 'wiki');
	}

	function show()
	{
		global $dcl_info, $g_oSec;

		commonHeader();

		if (($type = Filter::ToInt($_REQUEST['type'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (($id = @Filter::ToInt($_REQUEST['id'])) === null && $type != DCL_ENTITY_GLOBAL)
		{
			throw new InvalidDataException();
		}
		
		if ($type == DCL_ENTITY_GLOBAL)
			$id = 0;
		
		$name = @$_REQUEST['name'];
		$editmode = @$_REQUEST['editmode'];
		$text = @$_REQUEST['text'];
		$id2 = @Filter::ToInt($_REQUEST['id2']);

		if ($dcl_info['DCL_WIKI_ENABLED'] != 'Y' || !$g_oSec->HasPerm($type, DCL_PERM_VIEWWIKI))
			throw new PermissionDeniedException();

		if (!isset($name) || $name == '')
			$name = 'FrontPage';

		if (in_array($name, $this->aLockedPages))
			unset($editmode);

		$obj = new WikiModel();
		if ((!$obj->Exists($type, $id, $id2, $name) || $obj->Load($type, $id, $id2, $name) == -1) && $name != 'RecentChanges')
			$obj = $this->quickwiki($type, $id, $id2, $name);

		$extraParams = "type=$type";
		if ($type != DCL_ENTITY_GLOBAL)
			$extraParams .= "&id=$id";

		if ($type == DCL_ENTITY_WORKORDER)
			$extraParams .= "&id2=$id2";

		$this->t->set_var('VAL_TITLE', (isset($editmode) && $editmode == 'edit') ? sprintf(DCL_WIKI_EDITINGFORMAT, $name) : $name);
		$this->t->set_var('VAL_TEXT', $text);
		$this->t->set_var('LNK_ACTION', menuLink());
		$this->t->set_var('VAL_PAGENAME', $name);
		$this->t->set_var('VAL_ENTITYTYPEID', $type);
		$this->t->set_var('VAL_ENTITYID', $id);
		$this->t->set_var('VAL_ENTITYID2', $id2);
		$this->t->set_var('TXT_SAVE', STR_CMMN_SAVE);
		$this->t->set_var('TXT_RESET', STR_CMMN_RESET);
		$this->t->set_var('TXT_CANCEL', STR_CMMN_CANCEL);
		$this->t->set_var('TXT_VIEW', STR_CMMN_VIEW);
		$this->t->set_var('LNK_CANCEL', menuLink('', "menuAction=htmlWiki.show&name=$name&$extraParams"));
		$this->t->set_var('LNK_EDITTHISPAGE', menuLink('', "menuAction=htmlWiki.show&editmode=edit&name=$name&$extraParams"));
		$this->t->set_var('TXT_EDITTHISPAGE', DCL_WIKI_EDITTHISPAGE);
		$this->t->set_var('LNK_RETURNTOFRONTPAGE', menuLink('', "menuAction=htmlWiki.show&name=FrontPage&$extraParams"));
		$this->t->set_var('TXT_RETURNTOFRONTPAGE', DCL_WIKI_RETURNTOFRONTPAGE);
		$this->t->set_var('LNK_RECENTCHANGES', menuLink('', "menuAction=htmlWiki.show&name=RecentChanges&$extraParams"));
		$this->t->set_var('TXT_VIEWRECENTCHANGES', DCL_WIKI_VIEWRECENTCHANGES);
		$this->t->set_var('TXT_THISPAGECANNOTBEEDITED', DCL_WIKI_THISPAGECANNOTBEEDITED);

		$this->ParseDescriptionBlock();

		if ($obj->page_name != '' || $name == 'RecentChanges')
		{
			$i = 0;

			if ($obj->page_text == '')
		        $tf = sprintf(DCL_WIKI_NEWPAGEFORMAT, $name);
			else
				$tf =  htmlspecialchars($obj->page_text);

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

				$this->t->set_var('VAL_TEXT', $tf);
				$this->t->parse('hDisplay', 'display');
	        }
			else
			{
				$this->t->set_var('VAL_TEXT', $tf);
				$this->t->parse('hEdit', 'edit');
	        }
		}

		if (in_array($name, $this->aLockedPages))
			$this->t->parse('hNoedit', 'noedit');
		else if (!isset($editmode) || $editmode != 'edit')
			$this->t->parse('hEditlink', 'editlink');

		$this->t->pparse('out', 'hForm');
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
				$start=substr($numtype,1);
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
		$len = strlen($str) / 2;
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

		while (list(, $line) = each($aText))
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
			$twoWordsPattern = "/([\s;<>'\(]|^)(([A-Z][a-z]+){2,})([\s.,!?&<>\)]|$)/";
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
			$line = preg_replace("/\(\((([A-Z][a-z������������]+){2,})\|([^\~]+)\)\)/","<a class=\"wiki\" title='$3' href=\"".menuLink()."?menuAction=htmlWiki.show&name=$1&type=$type\">$3</a>",$line);

			// And just plain ((name))
			$line = preg_replace("/\(\(([^\)\(\|]+)\)\)/","<a class=\"wiki\" title='$1' href=\"".menuLink()."?menuAction=htmlWiki.show&name=$1&type=$type\">$1</a>",$line);

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
				$indlen = strlen($match[0]);

				if ($indlen > 0)
				{
					$line = substr($line, $indlen);
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
							$numtype .= substr($limatch[3], 1);
						$indtype = "ol";
					}
				}

				if ($indent_list[$in_li] < $indlen)
				{
					$in_li++;
					$indent_list[$in_li] = $indlen;
					$indent_type[$in_li] = $indtype;
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
				$line = preg_replace('/^((?:\|\|)+)(.*)\|\|$/e',"'<tr class=\"wiki\"><td class=\"wiki\"'.\$this->_table_span('\\1').'>\\2</td></tr>'", $line);
				$line = preg_replace('/((\|\|)+)/e',"'</td><td class=\"wiki\"'.\$this->_table_span('\\1').'>'", $line);
				$line = str_replace('\"', '"' ,$line);
			}

			$line = $close . $open . $line;
			$open = "";
			$close = "";

			$retVal .= $line . '<br>';
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

		$theList = array();

		$obj = new WikiModel();
		$obj->ListRecentChanges($type, $id, $id2);

		$count = 0;
		while ($obj->next_record())
		{
			$count++;
			$list .= sprintf("||((%s))||%s||%s||\n",
								$obj->f(0),
								$obj->FormatTimestampForDisplay($obj->f(1)),
								$obj->f(2));
		}

		$list .= "||||||'''" . sprintf(DCL_WIKI_TOTALPAGESFORMAT, $count) . "'''||\n\n";

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
		
		$name = GPCStripSlashes($_REQUEST['name']);
		$editmode = $_REQUEST['editmode'];
		$text = GPCStripSlashes($_REQUEST['text']);
		$id2 = @Filter::ToInt($_REQUEST['id2']);
		if ($type != DCL_ENTITY_WORKORDER)
			$id2 = 0;
		
		$o = new WikiModel();
		if ($o->Load($type, $id, $id2, $name) != -1)
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

		$o->Add();
		return $o;
	}
}
