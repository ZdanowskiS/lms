#!/usr/bin/perl 
#
#LMS GAMMUPlugin by S. Zdanowski

use strict;
use DBI;
use Config::IniFiles;
use Getopt::Long;
use vars qw($configfile $quiet $help $version);
use POSIX qw(strftime);

my $_version = '0.1';

my %options = (
	"--config-file|C=s"	=>	\$configfile,
	"--quiet|q"		=>	\$quiet,
);

Getopt::Long::config("no_ignore_case");
GetOptions(%options);

if($help)
{
	print STDERR <<EOF;
inject-sms, version $_version

-C, --config-file=/etc/gammu/inject-sms.ini	alternate config file (default: /etc/lms/lms.ini);
-q, --quiet			suppress any output, except errors;

EOF
	exit 0;
}

if($version)
{
	print STDERR <<EOF;
inject-sms, version $_version
EOF
	exit 0;
}

if(!$configfile)
{
	$configfile = "/etc/gammu/inject-sms.ini";
}

if(!$quiet)
{
	print STDOUT "inject-sms, version $_version\n";
	print STDOUT "Using file $configfile as config.\n";
}

if(! -r $configfile)
{
	print STDERR "Fatal error: Unable to read configuration file $configfile, exiting.\n";
	exit 1;
}

my $ini = new Config::IniFiles -file => $configfile;
print @Config::IniFiles::errors;

my $outdir = $ini->val('sms', 'smstools_outdir') || '/var/spool/sms/outgoing';

my $utsfmt;
opendir (DIR, $outdir) or die $!;

while (my $file = readdir(DIR)) {
	if($file eq '.' || $file eq '..'){
		next;
	}
	my $startbody=0;
	my $msg='';
	my $number='';

	my $filename = $outdir.'/'.$file;
	open(my $fh, '<', $filename)
  		or die "Could not open file '$filename' $!";
 
	while (my $row = <$fh>) {
		if($startbody==1){
			$msg.=$row;
		}
		elsif ($row =~ m/To:/) {
   			substr($row, 0, 4) = "";
			chomp $row;
			$number=$row;
		}
		elsif($row eq "\n"){
			$startbody=1;
		}	
	}

	my $len =length($msg);

	$msg =~ s/\"/\\"/ig;

	my $command='gammu-smsd-inject TEXT '.$number.' -len '.$len.' -text "'.$msg.'"';

	unlink($filename);
	system($command);

	if(!$quiet){
		print $command."\n";
	}
}
closedir(DIR);
