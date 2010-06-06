<?php
include("helpCommon.php");

helpHeader();
?>
<H3>Work Order CSV Upload</H3>
Work orders can be upload via a properly formatted CSV (comma separated values) file.  This format is a common import and export option for most software packages that deal with data in different formats.
<BR>
<BR>
<H4>Supported Fields</H4>
Required fields are in bold:
<UL>
<LI><B>product</B>&nbsp;-&nbsp;Name or numeric ID of product.</LI>
<LI>account&nbsp;-&nbsp;Name or numeric ID of product.</LI>
<LI><B>deadlineon</B></LI>
<LI>eststarton</LI>
<LI>estendon</LI>
<LI><B>esthours</B></LI>
<LI><B>priority</B>&nbsp;-&nbsp;Name or numeric ID of priority.</LI>
<LI><B>severity</B>&nbsp;-&nbsp;Name or numeric ID of severity.</LI>
<LI>contact</LI>
<LI>contactphone</LI>
<LI><B>summary</B></LI>
<LI>notes</LI>
<LI><B>description</B></LI>
<LI><B>responsible</B>&nbsp;-&nbsp;Name or numeric ID of personnel responsible.</LI>
<LI>revision</LI>
</UL>
Note that your permissions may not permit you to specify some of the fields that are listed here.
<H4>File Format</H4>
The file must be a comma-delimited text file.  The first row should contain the field names as listed above.  Date and text fields must be enclosed in double quotes ( &quot; ).  Dates should be submitted in the same format expected by DCL.
<?php
helpFooter();
?>
