<?php
define('DS',DIRECTORY_SEPARATOR);
define('EOL', PHP_EOL);


if(!version_compare(phpversion(),'5.2','>=')){
  exit('Error: your php version ('.phpversion().'   needs to be >=5.2'.PHP_EOL);
}

$ScaffoldDir = dirname(__FILE__);
include $ScaffoldDir.DS.'functions.php';

if (php_sapi_name() != 'cli') {
  exit('Error: Must run from command line!'.EOL);
}

$PermittedOps = array(
  'dir::',
  'refactor',
  'bklimit::',
  'help',
  'light'
);

$Options = getopt("", $PermittedOps);

$Dir = ArrayI('dir',$Options) ? ArrayI('dir',$Options) : getcwd();

if($Dir){
  $Dir = AbsPath($Dir);
  if(!file_exists($Dir)){
    if(!AskYesOrNo('Directory '.$Dir.' does not exist, you want to create it?'.EOL))
      exit;
    if(!mkdir($Dir,0755,TRUE)){
      exit('Error: Unable to create dir'.$Dir.'!'.EOL);
    }
    if(realpath(dirname($Dir).DS.'Gdn_PScaffold')){
      rmdir($Dir);
      exit('Cannot create directory in Gdn_PScaffold'.EOL);
    }
  }
  if(file_exists($Dir.DS.'gdn_pscaffold.php')){
    exit('Cannot create directory in Gdn_PScaffold'.EOL);
  }
  
  if(!chdir(realpath($Dir))){
    exit('Error: Unable to move to dir'.$Dir.'!'.EOL);
  }
}


$BkLimit = ArrayI('bklimit',$Options) ? ArrayI('bklimit',$Options): 3;

$ScaffoldDir = dirname(__FILE__);

$Dir = realpath(getcwd());

if(file_exists($Dir.DS.'gdn_pscaffold.php')){
  exit('Cannot use gdn_ps in Gdn_PScaffold folder'.EOL);
}

$DirName = basename($Dir);

include $ScaffoldDir.DS.'conf.php';
include $ScaffoldDir.DS.'questions.php';

if(!IsValidCheck($DirName,'`[A-Za-z][A-Za-z0-9]+`')){
  exit('Error: Plugin folder should be alpha-numerically named starting with a letter, no spaces hyphens, underscores, etc!'.EOL);
}

$PluginFile = '';

if(file_exists($Dir.DS.'default.php')){
  $PluginFile = $Dir.DS.'default.php';
}

$IsLight = ArrayI('light',$Options,NULL)!==NULL;

$PluginGlob = glob($Dir.DS.'*plugin.php');

foreach($PluginGlob As $File){
  if(strtolower(basename($File))=='class.'.strtolower($DirName).'plugin.php'){
    $PluginFile = $File;
    break;
  }
}

if(!$PluginFile && count($PluginGlob)){
  exit('Error: plugin file does not match class.'.strtolower($DirName).'plugin.php'.EOL);
  
}

$ReadMe = FileContents($ScaffoldDir.DS.'README.md');
$ReadMeTopics = ReadMeTopics($ReadMe);

if(ArrayI('help',$Options,NULL)!==NULL){ 
  print <<<EOT

# HELP #{$ReadMeTopics['HELP']}

EOT;
  exit;
}
  

BackUp();




  
if(ArrayI('refactor',$Options,NULL)!==NULL){
  $ClassChain = GetClassChain($PluginFile,$DirName);
  $First = $ClassChain[0];
  if(AskYesOrNo('Do you wish to extend the class chain?'.EOL)){
    print 'Backup in ps_backup* folder'.EOL.EOL;
    $ValidNum = array();
    $Num = 0;
    $PrintStr = '';
    foreach ($ClassChain As $ClassI => $Class){
      $Num++;
      $PrintStr .= '  '.($Num).'  '.$Class['Class'].EOL;
      if(!preg_match('`^('.$First['Class'].'|'.$DirName.'Utility)$`i',$Class['Class'])){
        $Num++;
        $PrintStr .= ' ['.($Num).']'.EOL;
        $ValidNum[$Num] = $ClassI;
      }
    }
    $SelNum = AskValidateInput('Enter a valid number shown in square brackets'.EOL.EOL.$PrintStr.EOL, '`^('.join('|',array_flip($ValidNum)).')$`', 'Not a valid number'.EOL);
    $NewClassName = AskValidateInput('Enter a valid class name (prefix '.$DirName.' will be added)'.EOL, '`^[A-Za-z][A-Za-z0-9]+$`', 'Not a valid class name'.EOL);
    $NewClass = $DirName.$NewClassName;
    $Before = $ClassChain[$ValidNum[$SelNum]];
    $After = $ClassChain[$ValidNum[$SelNum]+1];
    $BackupGlob = glob($Dir.DS.'ps_backup*',GLOB_ONLYDIR);
    $BackupFolder = array_pop($BackupGlob);
    copy($Before['File'], $BackupFolder.DS.basename($Before['File']));
    copy($After['File'], $BackupFolder.DS.basename($After['File']));
    RefactorClass($Before['File'], $After['Class'], $NewClass, '[A-Za-z][A-Za-z0-9]+');
    $PluginInfo = array('NewClass'=>$NewClass, 'ExtendsClass'=> $After['Class']);
    file_put_contents($Dir.DS.'class.'.strtolower($NewClassName).'.php', ParseTemplate($ScaffoldDir.DS.'newclass.tpl.php', $PluginInfo));
    print(EOL.'Success! Class added to the chain'.EOL);
  }else if(AskYesOrNo('Do you wish to remove a class from the class chain?'.EOL)){
    print 'Backup in ps_backup* folder'.EOL.EOL;
    $ValidNum = array();
    $Num = 0;
    
    $PrintStr = '';
    foreach ($ClassChain As $ClassI => $Class){
      $Num++;
      if(!preg_match('`^('.$First['Class'].'|'.$DirName.'Utility|'.$DirName.'UI)$`i',$Class['Class'])){
        $ValidNum[$Num] = $ClassI;
        $PrintStr .= ' ['.($Num).'] ';
      }else{
        $PrintStr .= '  '.($Num).'  ';
      }
      $PrintStr .= $Class['Class'].EOL;
    }
    
    $SelNum = AskValidateInput('Enter a valid number shown in square brackets'.EOL.EOL.$PrintStr.EOL, '`^('.join('|',array_flip($ValidNum)).')$`', 'Not a valid number'.EOL);
    $Before = $ClassChain[$ValidNum[$SelNum]-1];
    $Current = $ClassChain[$ValidNum[$SelNum]];
    $After = $ClassChain[$ValidNum[$SelNum]+1];
    $BackupGlob = glob($Dir.DS.'ps_backup*',GLOB_ONLYDIR);
    $BackupFolder = array_pop($BackupGlob);
    copy($Before['File'], $BackupFolder.DS.basename($Before['File']));
    copy($Current['File'], $BackupFolder.DS.basename($Current['File']));
    unlink($Current['File']);
    RefactorClass($Before['File'], $Current['Class'], $After['Class'], '[A-Za-z][A-Za-z0-9]+');
    print(EOL.'Success! Class removed from the chain'.EOL);
  }
  exit;
}

if(!$PluginFile){
  $PluginInfo = array();
  $PluginInfo['Index'] = $DirName;
  $PluginInfo['Author'] = $Conf['Author'];
  $PluginInfo['AuthorEmail'] = $Conf['AuthorEmail'];
  $PluginInfo['AuthorUrl'] = $Conf['AuthorUrl'];
  $PluginInfo['Name'] = AskValidateInput($Questions['Name'],'`^[\w ]{3,100}$`',$QuestionsInvaild['Name']);
  $PluginInfo['Description'] = AskValidateInput($Questions['Description'],'`^.{10,250}$`',$QuestionsInvaild['Description']);
  $PluginInfo['LongDescription'] = AskInputMultiLine($Questions['LongDescription']);
  $PluginInfo['Version'] = AskValidateInput($Questions['Version'],'`^[\.0-9a-z,+\-_]+$`i',$QuestionsInvaild['Version']);
  $PluginInfo['SettingsUrl'] = 'settings/'.strtolower($DirName);
  print 'Creating plugin file class.'.strtolower($DirName).'plugin.php'.EOL;
  $PluginFile = $Dir.DS.'class.'.strtolower($DirName).'plugin.php';
  file_put_contents($PluginFile, ParseTemplate($ScaffoldDir.DS.'plugin.tpl.php', $PluginInfo));
}else{
  $Plugin = file_get_contents($PluginFile);
  preg_match('`\$PluginInfo\[[^\]]+?\]\s*=\s*[Aa]rray\s*\([^;]+;\s*\n`m',$Plugin,$Match);
  if(!count($Match))
    exit('Error: Could not find $PluginInfo in '.$PluginFile.EOL);
      
  @eval(trim($Match[0])); 
  $PluginInfo = count($PluginInfo) ? array_pop($PluginInfo) : array();
  $PluginInfo['Index'] = ArrayI('Index', $PluginInfo, $DirName);
  $PluginInfo['Name'] = ArrayI('Name', $PluginInfo, $PluginInfo['Index']);
  $PluginInfo['Author'] = ArrayI('Author', $PluginInfo, $Conf['Author']);
  $PluginInfo['AuthorEmail'] = ArrayI('AuthorEmail', $PluginInfo, $Conf['AuthorEmail']);
  $PluginInfo['AuthorUrl'] = ArrayI('AuthorUrl', $PluginInfo, $Conf['AuthorUrl']);
  $ps_examples = AskYesOrNo('Plugin file already exists. Do you wish to create ps_examples files in the ps_example folders?'.EOL);
  if($ps_examples){
    if(!file_exists($Dir.DS.'ps_example')){
      mkdir($Dir.DS.'ps_example',0755);
    }
    file_put_contents($Dir.DS.'ps_example'.DS.basename($PluginFile), ParseTemplate($ScaffoldDir.DS.'plugin.tpl.php', $PluginInfo));
    file_put_contents($Dir.DS.'ps_example'.DS.'class.utility.php', ParseTemplate($ScaffoldDir.DS.'utility.tpl.php', $PluginInfo));
    if(!$IsLight){
      file_put_contents($Dir.DS.'ps_example'.DS.'class.api.php', ParseTemplate($ScaffoldDir.DS.'api.tpl.php', $PluginInfo));
      file_put_contents($Dir.DS.'ps_example'.DS.'class.settings.php', ParseTemplate($ScaffoldDir.DS.'settings.tpl.php', $PluginInfo));
    }
    file_put_contents($Dir.DS.'ps_example'.DS.'class.ui.php', ParseTemplate($ScaffoldDir.DS.'ui.tpl.php', $PluginInfo));
    file_put_contents($Dir.DS.'ps_example'.DS.'readme.markdown', ParseTemplate($ScaffoldDir.DS.'readme.tpl.markdown', $PluginInfo));
    if($IsLight){
       RefactorClass($Dir.DS.'ps_example'.DS.'class.ui.php', ArrayI('Index', $PluginInfo, $DirName).'[A-Za-z0-9]+', ArrayI('Index', $PluginInfo, $DirName).'Utility' , '[A-Za-z][A-Za-z0-9]+', '', TRUE);
    }
    print 'Creating ps_example plugin files in ps_example!'.EOL;
    

  }
}

if(!file_exists($Dir.DS.'class.utility.php')){
   print 'Creating plugin file class.utility.php'.EOL;
   file_put_contents($Dir.DS.'class.utility.php', ParseTemplate($ScaffoldDir.DS.'utility.tpl.php', $PluginInfo));
}

if(!$IsLight){
  if(!file_exists($Dir.DS.'class.api.php')){
    print 'Creating plugin file class.api.php'.EOL;
    file_put_contents($Dir.DS.'class.api.php', ParseTemplate($ScaffoldDir.DS.'api.tpl.php', $PluginInfo));
  }
  
  if(!file_exists($Dir.DS.'class.settings.php')){
    print 'Creating plugin file class.settings.php'.EOL;
    file_put_contents($Dir.DS.'class.settings.php', ParseTemplate($ScaffoldDir.DS.'settings.tpl.php', $PluginInfo));
  }
  
}

if(!file_exists($Dir.DS.'class.ui.php')){
  print 'Creating plugin file class.ui.php'.EOL;
  file_put_contents($Dir.DS.'class.ui.php', ParseTemplate($ScaffoldDir.DS.'ui.tpl.php', $PluginInfo));
}


if(!file_exists($Dir.DS.'readme.markdown')){
  print 'Creating plugin file readme.markdown'.EOL;
  file_put_contents($Dir.DS.'readme.markdown', ParseTemplate($ScaffoldDir.DS.'readme.tpl.markdown', $PluginInfo));
}
  
if(!$IsLight){
  if(!file_exists($Dir.DS.'models')){
    print 'Creating models folder'.EOL;
    mkdir($Dir.DS.'models',0755);
    file_put_contents($Dir.DS.'models'.DS.'readme.txt', ParseTemplate($ScaffoldDir.DS.'models.tpl.txt', $PluginInfo));
  }
  
  if(!file_exists($Dir.DS.'views')){
    print 'Creating views folder'.EOL;
    mkdir($Dir.DS.'views',0755);
    file_put_contents($Dir.DS.'views'.DS.'readme.txt', ParseTemplate($ScaffoldDir.DS.'views.tpl.txt', $PluginInfo));
  }
  
  if(!file_exists($Dir.DS.'design')){
    print 'Creating design folder'.EOL;
    mkdir($Dir.DS.'design',0755);
    file_put_contents($Dir.DS.'design'.DS.'readme.txt', ParseTemplate($ScaffoldDir.DS.'design.tpl.txt', $PluginInfo));
  }
  
  if(!file_exists($Dir.DS.'js')){
    print 'Creating js folder'.EOL;
    mkdir($Dir.DS.'js',0755);
    file_put_contents($Dir.DS.'js'.DS.'readme.txt', ParseTemplate($ScaffoldDir.DS.'js.tpl.txt', $PluginInfo));
  }
  
  if(!file_exists($Dir.DS.'library')){
    print 'Creating library folder'.EOL;
    mkdir($Dir.DS.'library',0755);
    file_put_contents($Dir.DS.'library'.DS.'readme.txt', ParseTemplate($ScaffoldDir.DS.'library.tpl.txt', $PluginInfo));
  }
}

if (!file_exists($Dir.DS.'icon.png')) {
  print 'Creating icon.png'.EOL;
  SaveImg($Dir.DS.'icon.png', $ScaffoldDir.DS.'icon.png');
}

RefactorClass($PluginFile, 'Gdn_Plugin', ArrayI('Index', $PluginInfo, $DirName).'UI' , '[A-Za-z][A-Za-z0-9]+');

if($IsLight){
  RefactorClass($Dir.DS.'class.ui.php', ArrayI('Index', $PluginInfo, $DirName).'[A-Za-z0-9]+', ArrayI('Index', $PluginInfo, $DirName).'Utility' , '[A-Za-z][A-Za-z0-9]+', '', TRUE);
}
