#!/usr/bin/perl -w
# $Id$

##################################################################################
# Derived From dcl-gateway.pl - see comments about origin there :)               #
# Michael L. Dean, February 2003                                                 #
#                                                                                #
# This script will accept email to an address formatted as                       #
#         <product_abbreviation>-support@yourdomain.com                          #
# and create a work order using the information from the email, filling in the   #
# required fields as accurately as possible.                                     #
##################################################################################

##################################################################################
# Database Configuration                                                         #
##################################################################################
# dbType:     Pg    = PostgreSQL                                                 #
#             mysql = MySQL                                                      #
# dbHost:     Where the database server is running                               #
# dbPort:     Port the database server is listening on                           #
# dbName:     Name of the DCL database to connect to                             #
# dbUser:     Database user to connect as                                        #
# dbPassword: Password for the database user                                     #
##################################################################################

%dcl_domain_info = (
		dbType => 'Pg',
		dbHost => 'localhost',
		dbPort => '5432',
		dbName => 'dcl',
		dbUser => 'dcl',
		dbPassword => ''
	);

##################################################################################
# Regular Expression for e-Mail Addresses - uncomment the regex for your style   #
##################################################################################
$reEmail = "^(.*)-support@.*"; # ex: dcl-support@mydomain.com
#$reEmail = "^support-(.*)@.*"; # ex: support-dcl@mydomain.com

##################################################################################
# DO NOT MODIFY ANYTHING BELOW THESE LINES UNLESS YOU KNOW WHAT YOU ARE DOING!!! #
##################################################################################

use DBI;
use MIME::Parser;
use File::Basename;

# First init email, then check for validity before anything else is done
&setDefaults();
&initEmail();
&processHeaders();

&setConnectString();
$dbh = DBI->connect($connectString,
					$dcl_domain_info{'dbUser'},
					$dcl_domain_info{'dbPassword'},
					{ RaiseError => 1, AutoCommit => 1 }
					) or die('Cannot connect to database!');

&getConfig();
&processEmail();
&setContactInfo();
&setProductInfo();
&setConfigInfo();
&createWorkOrder();
&storeAttachments();
&sendMail();

$dbh->disconnect();

exit();

##################################################################################
# End of main program                                                            #
##################################################################################

sub setConnectString
{
	$connectString = "DBI:$dcl_domain_info{'dbType'}:";
	if ($dcl_domain_info{'dbType'} eq "mysql")
	{
		# MySQL
		$connectString .= "$dcl_domain_info{'dbName'}:$dcl_domain_info{'dbHost'}:$dcl_domain_info{'dbPort'}";
	}
	elsif ($dcl_domain_info{'dbType'} eq "Pg")
	{
		# Postgres
		$connectString .= "dbname=$dcl_domain_info{'dbName'};host=$dcl_domain_info{'dbHost'};port=$dcl_domain_info{'dbPort'}";
	}
}

sub setDefaults
{
	$product_id = 0;
	$account_id = 0;
	$created_by = 0;
	$created_on = "";
	$created_by = 0;
	$created_on = "";
	$responsible = 0;
	$status_id = 0;
	$priority_id = 0;
	$severity_id = 0;
	$wo_id = 0;
	$wo_seq = 0;
	$status_id = 0;
	$contact_id = 0;
	$contact = "";
	$contactemail = "";

	$isReply = 0;
}

sub initEmail
{
	undef $/; # We want to treat everything read from STDIN as one line
	$input = <>;
	$/ = "\n";
	$body = "";
	($headers, $body) = split (/\n\n/, $input, 2);
}

sub getConfig
{
	# Get gateway and smtp settings
	$sql = "SELECT dcl_config_name, dcl_config_field, dcl_config_int, dcl_config_varchar FROM dcl_config WHERE ";
	$sql .= "dcl_config_name like 'DCL_GATEWAY_WO_%' or dcl_config_name like 'DCL_SMTP_%' or ";
	$sql .= "dcl_config_name = 'DCL_FILE_PATH'";

	$sth = $dbh->prepare($sql);
	$sth->execute() or die("Could not get configuration for gateway.");

	my($configName, $configField, $configInt, $configVarchar);
	$sth->bind_columns(undef, \$configName, \$configField, \$configInt, \$configVarchar);

	while ($sth->fetch())
	{
		if ($configField eq "dcl_config_int")
		{
			$dcl_config{$configName} = $configInt;
		}
		else
		{
			$dcl_config{$configName} = $configVarchar;
		}
	}

	$sth->finish();

	if ($dcl_config{'DCL_GATEWAY_WO_ENABLED'} ne "Y")
	{
		$dbh->disconnect();
		print "The work order gateway is not enabled for Double Choco Latte.";
		exit(255);
	}
}

sub getUpperSQL
{
	my($field) = @_;

	if ($dcl_domain_info{'dbType'} eq "Pg")
	{
		$field = "RTrim(Upper($field))";
	}

	return $field;
}

sub setProductInfo
{
	my $productAbb = $header{'To'};
	if ($header{'To'} =~ /(.*)\s?[\<](.*)[\>]\s?$/)
	{
		# Retrieve only the email address
		$productAbb =~ s/.*\s?[\<](.*)[\>]\s?$/$1/;
	}

	if ($productAbb =~ /$reEmail/i)
	{
		$productAbb =~ s/$reEmail/$1/i;
	}
	else
	{
		$productAbb = $header{'Cc'};
		if ($header{'Cc'} =~ /(.*)\s?[\<](.*)[\>]\s?$/i)
		{
			# Retrieve only the email address
			$productAbb =~ s/.*\s?[\<](.*)[\>]\s?$/$1/i;
		}

		if ($productAbb =~ /$reEmail/i)
		{
			$productAbb =~ s/$reEmail/$1/i;
		}
		else
		{
			print "No suitable email addresses for " . $productAbb . " were found.";
			exit(255);
		}
	}

	# Set product_id and responsible (reportto)
	$sql = "SELECT id, reportto, name FROM products WHERE " . getUpperSQL("short") . "=" . $dbh->quote(uc($productAbb));
	$sth = $dbh->prepare($sql);
	$sth->execute();

	$productName = "";
	$sth->bind_columns(undef, \$product_id, \$responsible, \$productName);
	if (!$sth->fetch())
	{
		$sth->finish();
		$dbh->disconnect();
		print "Could not fetch product information for $productAbb";
		exit(255);
	}

	$sth->finish();

	# Set created by based off of email From: address if possible
	$sql = "SELECT personnel.id FROM personnel, dcl_contact_email WHERE personnel.contact_id = dcl_contact_email.contact_id AND " . getUpperSQL("email_addr") . "=" . $dbh->quote(uc($contactemail)) . " ORDER BY preferred DESC";
	$sth = $dbh->prepare($sql);
	$sth->execute();

	$created_by = "";
	$sth->bind_columns(undef, \$created_by);
	if (!$sth->fetch())
	{
		# set created by to be reportto
		$created_by = $responsible;
	}

	$sth->finish();
}

sub setContactInfo
{
	$contact = $header{'From'};
	$contactemail = $header{'From'};

	if ($header{'From'} =~ /(.*)\s?[\<](.*)[\>]\s?$/)
	{
		$contact =~ s/(.*)\s?[\<].*[\>]\s?$/$1/;
		$contactemail =~ s/.*\s?[\<](.*)[\>]\s?$/$1/;

		$contact =~ s/\s*$//;
		$contactemail =~ s/\s*$//;
	}

	$sql = "SELECT contact_id FROM dcl_contact_email WHERE " . getUpperSQL("email_addr") . "=" . $dbh->quote(uc($contactemail)) . " ORDER BY preferred DESC";
	$sth = $dbh->prepare($sql);
	$sth->execute();

	$sth->bind_columns(undef, \$contact_id);
	if (!$sth->fetch())
	{
		$sth->finish();

		# Need a new contact record
		use Lingua::EN::NameParse;
		%npargs = (auto_clean => 1, force_case => 1, lc_prefix => 1, initials => 3, allow_reversed => 1, joint_names => 0, extended_titles => 0);
		$nameparse = new Lingua::EN::NameParse(%npargs);

		$nperror = $nameparse->parse($contact);
		%contactname = $nameparse->components;

		my $first_name = $contactname{given_name_1};
		if ($first_name eq "")
		{
			return 0;
		}

		my $last_name = $contactname{surname_1};
		if ($last_name eq "")
		{
			return 0;
		}

		$sql = "INSERT INTO dcl_contact (first_name, last_name, created_on, created_by) VALUES (" . $dbh->quote($first_name) . "," . $dbh->quote($last_name) . ",now()," . $created_by . ")";

		$sth = $dbh->prepare($sql);
		$sth->execute();

		setContactID($sth);

		$sth->finish();

		if ($contact_id > 0)
		{
			$sql = "INSERT INTO dcl_contact_email (contact_id, email_type_id, email_addr, preferred, created_on, created_by) VALUES (" . $contact_id . ",1," . $dbh->quote($contactemail) . ",'Y',now()," . $created_by . ")";
			$sth = $dbh->prepare($sql);
			$sth->execute();
			$sth->finish();
		}
	}
	else
	{
		$sth->finish();
	}
}

sub setConfigInfo
{
	$status_id = $dcl_config{'DCL_GATEWAY_WO_STATUS'};
	$priority_id = $dcl_config{'DCL_GATEWAY_WO_PRIORITY'};
	$severity_id = $dcl_config{'DCL_GATEWAY_WO_SEVERITY'};
	$account_id = $dcl_config{'DCL_GATEWAY_WO_ACCOUNT'};
}

sub processHeaders
{
	# Process the headers:
	$procheaders = $headers;
	$procheaders =~ s/\?=\s\n/\?=\n/g; # Lines ending with an encoded-word
                               # have an extra space at the end. Remove it.
	$procheaders =~ s/\n[ |\t]//g; # Merge multi-line headers into a single line.
	$transheaders = '';

	foreach $line (split(/\n/, $procheaders))
	{
		while ($line =~ m/=\?[^?]+\?(.)\?([^?]*)\?=/)
		{
			$encoding   = $1;
			$txt        = $2;
			$str_before = $`;
			$str_after  = $';

			# Base64
			if ($encoding =~ /b/i)
			{
				require MIME::Base64;
				MIME::Base64->import(decode_base64);
				$txt = decode_base64($txt);
			}
			# QP
			elsif ($encoding =~ /q/i)
			{
				require MIME::QuotedPrint;
				MIME::QuotedPrint->import(decode_qp);
				$txt = decode_qp($txt);
			}

			$line = $str_before . $txt . $str_after;
		}

		# The decode above does not do underline-to-space translation:
		$line =~ tr/_/ /;
		$transheaders .= $line . "\n";
	}

	$transheaders .= "\n";

	@transheaders = split('\n', $transheaders);
	foreach (@transheaders)
	{
		$_ =~ s/:\s/:/g;
		if (/:/)
		{
			@vars = split(':', $_, 2);
			if (@vars)
			{
				$header{$vars[0]} = $vars[1];
			}
		}
	}

	$header{'Cc'} = "" if (!$header{'Cc'});
	$header{'Subject'} = "" if (!$header{'Subject'});
	$header{'From'} = "" if (!$header{'From'});

	exit(0) if ($header{'From'} =~ /^mailer-daemon/i);
	exit(0) if ($header{'From'} =~ /^postmaster/i);

	$header{'Cc'} =~ s/\s*$//;
	$header{'Subject'} =~ s/\s*$//;
	$header{'From'} =~ s/\s*$//;

    # determine work order id and seq
	# FIXME: I needs hep!
	$wo_id = $header{'Subject'};
	if ($wo_id =~ /\[\#(\d+)\]/)
	{
		$wo_id =~ s/.*\[\#(\d+)\].*/$1/;
		$isReply = 1;
	}
	else
	{
		$wo_id = 0;
	}
}

sub getDateSQL
{
	if ($dcl_domain_info{'dbType'} eq "mysql")
	{
		return "now()";
	}
	elsif ($dcl_domain_info{'dbType'} eq "Pg")
	{
		return "now()";
	}
}

sub getNewIDSQL
{
	if ($dcl_domain_info{'dbType'} eq "Pg")
	{
		$idsth = $dbh->prepare("INSERT INTO dcl_wo_id (seq) VALUES (1)");
		$idsth->execute();
		setWorkOrderID($idsth);
		$idsth->finish();

		return $wo_id;
	}

	return "";
}

sub getSeqSQL
{
	my $jcn;

	if ($dcl_domain_info{'dbType'} eq "Pg")
	{
		return "nextval('seq_jcn')";
	}

	return "";
}

sub getTimeCardSQL
{
	if ($dcl_domain_info{'dbType'} eq "Pg")
	{
		return "nextval('seq_timecards')";
	}

	return "";
}

sub createWorkOrder
{
	if ($wo_id == 0 || $wo_seq == 0)
	{
		$sql  = "INSERT INTO workorders (";
		$sql .= "jcn,seq,product,createby,createdon,status,statuson,deadlineon,eststarton,estendon,esthours,priority,";
		$sql .= "severity,contact_id,summary,notes,description,responsible,etchours,totalhours,wo_type_id";
		$sql .= ") VALUES (";

		if ($wo_id > 0)
		{
			$sql .= $wo_id . "," . getSeqSQL($wo_id) . ",";
		}
		else
		{
			$idSQL = getNewIDSQL();
			$sql .= $idSQL . ",1,";
		}


		$sql .= $product_id . ",";
		$sql .= $created_by . ", ";
		$sql .= getDateSQL() . ", ";
		$sql .= $status_id . ", ";
		$sql .= getDateSQL() . ", ";
		$sql .= getDateSQL() . ", ";
		$sql .= getDateSQL() . ", ";
		$sql .= getDateSQL() . ", ";
		$sql .= "1, ";
		$sql .= $priority_id . ", ";
		$sql .= $severity_id . ", ";

		if ($contact_id < 1)
		{
			$sql .= "NULL, ";
		}
		else
		{
			$sql .= $contact_id . ", ";
		}

		$sql .= $dbh->quote($header{'Subject'}) . ",";
		$sql .= $dbh->quote("e-Mail Gateway: " . $contactemail) . ", ";
		$sql .= $dbh->quote("Received via e-Mail Gateway: " . $transbody) . ", ";
		$sql .= $responsible . ", ";
		$sql .= "1, 0, 1)";
	}
	else
	{
		# Add a time card entry based on email
		$dcl_config{'DCL_GATEWAY_WO_REPLY'} =~ /Y/i or die("Cannot reply to generated work order e-mails");
		die("Cannot reply to generated work order e-mails");

		my $resolution;
		$resolution = "From: " . $header{'From'} . "\n";
		$resolution .= "To: " . $header{'To'} . "\n";
		$resolution .= "Subject: " . $header{'Subject'} . "\n\n";
		$resolution .= $transbody;

		$resSQL = getTimeCardSQL();

		$sql = "INSERT INTO timecards (";
		if ($resSQL)
		{
			$sql .= "resid, ";
		}
		$sql .= "ticketid, loggedby, loggedon, status, resolution, startedon) VALUES (";
		if ($resSQL)
		{
			$sql .= $resSQL . ", ";
		}
		$sql .= $wo_id . ", ";
		$sql .= $dcl_config{'DCL_GATEWAY_WO_REPLY_LOGGED_BY'} . ", ";
		$sql .= getDateSQL() . ", ";
		$sql .= $status_id . ", ";
		$sql .= $dbh->quote($resolution) . ", ";
		$sql .= getDateSQL() . ")";
	}

	$sth = $dbh->prepare($sql);
	$sth->execute();

	if ($wo_id == 0)
	{
		setWorkOrderID($sth);
	}

	$sth->finish();
}

sub setWorkOrderID
{
	my($sth) = @_;

	$wo_seq = 1;

	if ($dcl_domain_info{'dbType'} eq "mysql")
	{
		$wo_id = $dbh->{'mysql_insertid'};
	}
	elsif ($dcl_domain_info{'dbType'} eq "Pg")
	{
		my $sth2 = $dbh->prepare("SELECT currval('seq_dcl_wo_id')");
		$sth2->execute();
		$sth2->bind_columns(undef, \$wo_id);
		if (!$sth2->fetch())
		{
			print "Could not retrieve new work order number.";
			$sth2->finish();
			$sth->finish();
			$dbh->disconnect();
			exit(255);
		}

		$sth2->finish();
	}
}

sub setContactID
{
	my($sth) = @_;

	if ($dcl_domain_info{'dbType'} eq "mysql")
	{
		$contact_id = $dbh->{'mysql_insertid'};
	}
	elsif ($dcl_domain_info{'dbType'} eq "Pg")
	{
		my $sth2 = $dbh->prepare("SELECT currval('seq_dcl_contact')");
		$sth2->execute();
		$sth2->bind_columns(undef, \$contact_id);
		if (!$sth2->fetch())
		{
			print "Could not retrieve new contact ID.";
			$sth2->finish();
			$sth->finish();
			$dbh->disconnect();
			exit(255);
		}

		$sth2->finish();
	}
}

sub sendMail
{
	my $smtp;

	use Net::SMTP; # Yes, this will work on windows.

	$server = $dcl_config{'DCL_SMTP_SERVER'};
	$sendFrom = $dcl_config{'DCL_GATEWAY_WO_AUTORESPONSE_EMAIL'};
	$sendTo = $header{'From'};

	if ($dcl_config{'DCL_GATEWAY_WO_AUTORESPOND'} =~ /Y/i)
	{
		$smtp = Net::SMTP->new("$server", Hello => 'localhost.localdomain', Debug => 1) or die $!;
		$smtp->mail($sendFrom);
		$smtp->to($sendTo);
		$smtp->data();
		$smtp->datasend("To: $sendTo\n");
		$smtp->datasend("From: $sendFrom\n");

		if ($isReply == 1)
		{
			$smtp->datasend("Subject: Work Order Amended [#$wo_id-$wo_seq]\n\n");
			$smtp->datasend("Your e-Mail for Work Order #$wo_id-$wo_seq has been added to our system.");
		}
		else
		{
			$smtp->datasend("Subject: Work Order Created [#$wo_id-$wo_seq]\n\n");
			$smtp->datasend("Your work order has been entered into our system.");
		}

		$smtp->dataend();
		$smtp->quit();
	}

	if ($dcl_config{'DCL_SMTP_ENABLED'} =~ /Y/i)
	{
		$sql = "SELECT DISTINCT email_addr FROM personnel JOIN dcl_contact_email ON personnel.contact_id=dcl_contact_email.contact_id AND dcl_contact_email.preferred = 'Y' ";
		$sql .= "LEFT JOIN watches ON id=whoid LEFT JOIN dcl_wo_account ON typeid = 6 AND wo_id = $wo_id AND dcl_wo_account.seq = $wo_seq AND whatid1 = account_id ";
		$sql .= "WHERE (id = $responsible OR (actions IN (1, 3, 4) AND ((typeid = 1 AND whatid1 = $product_id) OR (typeid = 3 AND whatid1 = $wo_id AND whatid2 in (0, $wo_seq)) OR (typeid = 6 AND whatid1 = account_id)))) AND ";
		$sql .= "active = 'Y'";

		my $sth2 = $dbh->prepare($sql);
		$sth2->execute();

		$sth2->bind_columns(undef, \$sendTo);

		if ($sth2->fetch())
		{
			$smtp = Net::SMTP->new("$server", Hello => 'localhost.localdomain', Debug => 1) or die $!;
			$smtp->mail($sendFrom);
			$smtp->to($sendTo);
			$sendToList = $sendTo;

			while ($sth2->fetch())
			{
				$smtp->to($sendTo);
				$sendToList .= ", " . $sendTo;
			}

			$smtp->data();
			$smtp->datasend("To: $sendToList\n");
			$smtp->datasend("From: $sendFrom\n");

			if ($isReply == 1)
			{
				$smtp->datasend("Subject: Work Order Amended [#$wo_id-$wo_seq]\n\n");
				$smtp->datasend("Work Order #$wo_id has been amended from an e-Mail received via the gateway.\n\n");
				$smtp->datasend("From: " . $header{'From'} . "\n");
				$smtp->datasend("Resolution: " . $transbody);
			}
			else
			{
				$smtp->datasend("Subject: Work Order Created [#$wo_id-$wo_seq]\n\n");
				$smtp->datasend("A new work order has been received via the e-Mail gateway.\n\n");
				$smtp->datasend("From: " . $header{'From'} . "\n");
				$smtp->datasend("Product: " . $productName . "\n");
				$smtp->datasend("Summary: " . $header{'Subject'} . "\n");
				$smtp->datasend("Description: " . $transbody);
			}

			$smtp->dataend();
			$smtp->quit();
		}
	}
}

sub storeAttachments
{
	if ($wo_id > 0)
	{
		use File::Copy;

		$oldumask = umask 0002;
		$attachPath = $dcl_config{'DCL_FILE_PATH'} . "/attachments/wo/";
		$attachPath .= substr($wo_id, -1);
		mkdir($attachPath, 0777);
		$attachPath .= "/" . $wo_id;
		mkdir($attachPath, 0777);
		$attachPath .= "/" . $wo_seq;
		mkdir($attachPath, 0777);

		while (my $file = pop(@attachments))
		{
			if (!$origPath)
			{
				$origPath = dirname($file);
			}
			move($file, $attachPath);
		}
		
		rmdir($origPath) if ($origPath);
		umask $oldumask;
	}
}

sub processEmail
{
	# Split MIME-multipart messages and store the parts in subdirectories
	# under the directory indicated by $output_path. Depending on which
	# mail system your site uses, the directory specified by $output_path might
	# have to have special permissions. If you have qmail, the dir should
	# be owned by the user 'alias'. Sendmail should be content with 'root'
	# as owner.
	my $output_path = $dcl_config{'DCL_GATEWAY_WO_FILE_PATH'};
	my ($parsed) = (basename($0))[0];
	my $parser = MIME::Parser->new();

	# Permission mask for output files.
	# These permissions are very lax. Replace with what is appropriate
	# for your system.
	$oldumask = umask 0002;

	$parser->output_under($output_path);
	$parser->output_prefix($parsed);
	$parser->output_to_core();

	my $entity = $parser->parse_data($input);

	# Permissions for the directory containing the output files.
	# These permissions are very lax. Replace with what is appropriate
	# for your system.
	chmod 0775, ($parser->output_dir);

	foreach $file ($parser->filer->purgeable)
	{
    	# Strip trailing spaces from filenames:
		$file =~ s/\s*$//;
		if ($file =~ /\.txt$/)
		{
			# We have found a plaintext part. Include it in the new body:
			open PART, $file;
			while (<PART>)
			{
				$transbody .= $_;
			}

			close PART;

			# Build list of files included in the new body. We will delete
			# these files further down.
			unshift @purgeables, $file;
		}
		else
		{
			# We have found a non-plaintext part. Add a reference to it in the
			# new body:
			#$transbody .= "\n** File Attachment: $file**\n";
			push @attachments, $file;
		}
	}

	$transbody =~ s/\s*$//;

	# Make the list we built the new list of purgeable files:
	$parser->filer->purgeable(\@purgeables);

	# Delete them:
	$parser->filer->purge;

	umask $oldumask;
}
