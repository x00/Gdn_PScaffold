<?php
define('DS',DIRECTORY_SEPARATOR);
define('EOL', PHP_EOL);

if(!version_compare(phpversion(),'5.2','>=')){
  exit('Error: your php version ('.phpversion().') needs to be >=5.2'.PHP_EOL);
}

$ScaffoldDir = dirname(__FILE__);


include $ScaffoldDir.DS.'functions.php';
include $ScaffoldDir.DS.'conf.php.example';
@include $ScaffoldDir.DS.'conf.php';

SetConf();


$gdn_ps = file_get_contents($ScaffoldDir.DS.'gdn_pscaffold.php');

$gdn_ps = preg_replace_callback('`include\s*\$ScaffoldDir.DS.\'([^\']+)\'\s*;`','IncludeFilesCallback', $gdn_ps);

$gdn_ps = preg_replace_callback('`FileContents(\s*\$ScaffoldDir.DS.\'([^\']+)\'\s*);`','FileContentCallback', $gdn_ps);

$gdn_ps = preg_replace_callback('`ParseTemplate\(\s*\$ScaffoldDir.DS.\'([^\']+)\'\s*,\s*\$PluginInfo\s*\)`', 'ParseTemplateReplaceCallback', $gdn_ps);

$gdn_ps = preg_replace_callback('`(SaveImg\([^,]+,)\s*\$ScaffoldDir.DS.\'([^\']+)\'\)`', 'SaveImgCallback', $gdn_ps);

file_put_contents($ScaffoldDir.DS.'gdn_ps.php', $gdn_ps);

print 'gdn_ps.php made!'.EOL;

$ReadMe = file_get_contents($ScaffoldDir.DS.'README.md');
$ReadMeTopics = ReadMeTopics($ReadMe);

print <<<EOT

# GENERAL USAGE #{$ReadMeTopics['GENERAL USAGE']}

EOT;
