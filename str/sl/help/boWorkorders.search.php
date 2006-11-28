<?php
include("helpCommon.php");

helpHeader();
?>
<H3>Work Order Searches</H3>
The work order search screen is divided into two parts.  The first part searches for a specific work order by its number and (optionally) sequence.  The second allows one or more options to be selected for filtering the query.
<BR>
<BR>
<H4>Search By WO#/Seq</H4>
If you know the work order number, you can enter it in the top of the form to display the detail of the work order.  You do not have to enter a sequence number for display.  DCL will display the work order detail if only one sequence exists for that work order number.  Otherwise, if two or more sequences are found, DCL will display a search result list and allow you to browse as if you had performed a search.
<BR>
<BR>
<H4>Search By Parameters</H4>
DCL also allows you to perform a search by specifying one or more filters to query work orders by.  This interface is rather powerful even though it is not quite finished.  Note that for search parameters that use list boxes, you can select multiple entries to search for (i.e., if the status is open, unassigned, or deferred).
<H5>Personnel</H5>
Checkboxes allow you to search different fields within work orders to find out who did what.  They are:
<UL>
<LI><B>Responsible</B>&nbsp;-&nbsp;Check this box to narrow the work orders down to work orders the selected personnel are responsible for.</LI>
<LI><B>Opened By</B>&nbsp;-&nbsp;Select this to get the work orders opened by the selected personnel.</LI>
<LI><B>Closed By</B>&nbsp;-&nbsp;This option allows you to search for work orders closed by selected personnel.</LI>
</UL>
<H5>Product, Priority, Severity, Accounts, Status</H5>
If you want to narrow your search down by any of these fields, just select the appropriate items.
<H5>Dates</H5>
You can restrict date ranges of certain fields between dates.  Select the checkbox(es) of the appropriate fields and adjust the From: and To: dates as necessary.  The small calendar icons to the right of the date fields will pop up a JavaScript calendar if you prefer to select your dates the GUI way.
<H5>Summary, Notes, Description</H5>
Select any combination of the three fields and type your search text in the input area below.  Note that searches on these fields are currently running as &quot;phrase&quot;.  This means that in order for a field to match, it must contain the specified phrase <EM>exactly as typed</EM>.  In the cases of some SQL servers (such as PostgreSQL default install), you may even have to make sure your case is correct.  Others (i.e., Microsoft SQL Server) are usually set up to perform case insensitive queries.
<?php
helpFooter();
?>
