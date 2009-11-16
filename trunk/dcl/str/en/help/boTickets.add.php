<?php
include("helpCommon.php");

helpHeader();
?>
<H3>Ticket Entry</H3>
You use this screen to enter new tickets. This help file explains the fields and how to fill them in.
<BR>
<BR>
<H4>Responsible</H4>
Unless you are otherwise authorised, you should always
leave this as 'Tickets/WOs, New'

<H4>Contact</H4>
If there is an individual who may be contacted for more
information regarding this fault, put their name/organization here.

<H4>Contact Phone</H4>
If you have a phone number for the contact person, enter it here.

<H4>Contact e-Mail</H4>
If you have an email address for the contact person, enter it here.

<H4>Account</H4>
Select the account that the contact or issue is associated with.

<H4>Product</H4>
Select the product that the issue refers to.

<H4>Version</H4>
The affected version of the system.

<H4>Priority</H4>
Select the priority of the issue.

<H4>Severity</H4>
Select the severity of the issue.

<H4>Summary</H4>
A one line description of the problem. Ideally, this will be
about 60 characters and outline the problem as specfifically as
possible. An example is given here to indicate how you should use this
field. Imagine the following problem description.

<pre>
Description

The 'Help' link on the 'Upcoming attractions' page
(/attractions/upcoming/index.html) points to
/help/att-upc-help.html, which does not exist.
</pre>

<UL>
  <LI>Summary: Problem with web site<BR>

	- this is not a very good summary. It doesn't indicate what
	the problem is, where on the web site it is, or what type of
	problem it is.</LI>

  <LI>Summary: Broken link on web site<BR>

	- better, in that it indicates what the problem is, but it
	still does not indicate where or why the link is broken.</LI>

  <LI>Summary: Upcoming attractions help link broken (/attractions/upcoming/)<BR>

	- Good. We now know where the broken link is and that it is
	the help link.

  <LI>Summary: Upc. Attr. help link broken (/help/att-upc-help.html not found)<BR>

	- good as well. This time we also know the name of the file
	that is not found.</LI>

</UL>

<H4>Issue</H4>

This is where you put the detail of the problem. Include as
much information as you think is relevant. If you know what needs to
be done to solve the problem, state it here; otherwise, state as much
as you know about the problem. How/when it occurs. Can it be
reproduced? Has a workaround been put in place? How many customers
does it affect, and how. What attempts have been made to resolve the
problem. What part of the system does the fault affect.

<?php
helpFooter();
?>
