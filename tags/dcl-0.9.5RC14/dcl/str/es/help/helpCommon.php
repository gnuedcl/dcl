<?php
	function helpToolbar($showMailLink = false)
	{
?>
<DIV CLASS="toolbar">
<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0"><TR>
<TD WIDTH="20%" ALIGN="CENTER"><A HREF="#" onClick="javascript:window.print();">Imprimir</A>&nbsp;</TD>
<TD WIDTH="20%" ALIGN="CENTER"><A HREF="#" onClick="javascript:history.back();">Volver</A>&nbsp;</TD>
<TD WIDTH="20%" ALIGN="CENTER"><A HREF="#" onClick="javascript:history.go(0);">Refresh</A>&nbsp;</TD>
<TD WIDTH="20%" ALIGN="CENTER"><A HREF="#" onClick="javascript:history.forward();">Forward</A>&nbsp;</TD>
<TD WIDTH="20%" ALIGN="CENTER"><A HREF="#" onClick="javascript:window.close();">Cerrar</A></TD>
</TR></TABLE>
</DIV>
<?php
		if ($showMailLink)
		{
?>
Haga click <A HREF="mailto:mdean@users.sourceforge.net">aqui</A> si desea mas informacion o le gustaria informar de algun error en la documentacion.
<?php
		}
	}

	function helpHeader()
	{
?>
<HTML><HEAD>
<TITLE>Ayda de DCL</TITLE>
<STYLE TYPE="text/css">
	BODY {background: white;}
	H1,H2,H3,H4 { color: white; background: black; }
	A { color: blue; text-decoration: none; font-weight: bold; }
	A:hover { color: red; }
	.toolbar { background: #cecece }
	.toolbar TD { background: #cecece }
</STYLE>
</HEAD>
<BODY BGCOLOR="#FFFFFF">
<?php helpToolbar(); ?>
<HR NOSHADE WIDTH="100%">
<?php
	}
	function helpFooter()
	{
?>
<HR NOSHADE WIDTH="100%">
<?php helpToolbar(true); ?>
</BODY></HTML>
<?php
	}
?>
