<?php
function ArrayI($Index, $Array, $Alterative = false){
  if(array_key_exists($Index, $Array)){
    return $Array[$Index];
  }else{
    return $Alterative;
  }
}

function AskYesOrNo($Question){
  print $Question."[y / n]".EOL;
  print '> ';
  while(($Input = fgets(STDIN))!==FALSE){
    $Input = rtrim($Input);
    if($Input == 'y')
      return TRUE;
    if($Input == 'n')
      return FALSE;
    print 'Not an option!'.EOL;
    print $Question."[y / n]".EOL;
    print '> ';
  }
  exit;

}

function AskInput($Question){
  print $Question;
  print '> ';
  while(($Input = fgets(STDIN))!==FALSE){
    $Input = rtrim($Input);
    if($Input)
      return $Input;
  }
  exit;

}

function AskInputMultiLine($Question){
  $Input = array();
  print $Question." [to exit type ~x and enter]".EOL;
  print '> ';
  while(($InputLine = fgets(STDIN))!==FALSE){
    $InputLine = rtrim($InputLine);
    if(strpos($InputLine,'~x')!==FALSE)
      break;

    print '> ';

    $Input[] = $InputLine;
  }

  return join("\n",$Input);
}

function AskValidateInput($Question, $Match, $InValidStr){
  while(($Result = AskInput($Question))!==FALSE){
    if(preg_match($Match, $Result)){
      return $Result;
    }else{
      print $InValidStr;
    }
  }
}

function AskValidateEmail($Question, $InValidStr){
  while(($Result = AskInput($Question))!==FALSE){
    if(filter_var($Result, FILTER_VALIDATE_EMAIL) !== FALSE){
      return $Result;
    }else{
      print $InValidStr;
    }
  }
}

function AskValidateWebAddress($Question, $InValidStr){
  while(($Result = AskInput($Question))!==FALSE){
    if(filter_var(str_replace('-', 'x', $Result), FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED) !== FALSE){
      return $Result;
    }else{
      print $InValidStr;
    }
  }
}

function IsValidCheck($Input,$Match){
  if(preg_match($Match, $Input)){
    return true;
  }else{
    return false;
  }
}

function ParseTemplate($Template, $Args = array(), $IsString = FALSE) {
  $String = $IsString ? $Template : file_get_contents($Template);
  ParseTemplateCallback($Args, TRUE);
  $Result = preg_replace_callback('/{{([^}]+?)}}/', 'ParseTemplateCallback', $String);

  return $Result;
}

function ParseTemplateCallback($Match, $SetArgs = FALSE) {
  static $Args = array();
  if ($SetArgs) {
    $Args = $Match;
    return;
  }

  $Result = ArrayI(trim($Match[1]), $Args, '');
   
  return $Result;
}


function RefactorClass($File, $ExtendsClass, $ExtendsClassReplace, $Class, $ClassReplace = ''){

  RefactorClassCallback(array('ExtendsClassReplace' => $ExtendsClassReplace, 'ClassReplace' => $ClassReplace), TRUE);
  $String = file_get_contents($File);

  $Result = preg_replace_callback('`(?P<ClassPadding>[ \t]*)(?P<ClassOpening>abstract\s+class\s+|class\s+)(?P<Class>'.$Class.')(?P<ClassMiddle>\s+extends\s+)(?P<ExtendsClass>'.$ExtendsClass.')`i', 'RefactorClassCallback', $String);
  if($Result)
    file_put_contents($File,$Result);
}

function RefactorClassCallback($Match, $SetArgs = FALSE) {
  static $Args = array('ExtendsClassReplace' => '', 'ClassReplace' => '');
  if ($SetArgs) {
    $Args = $Match;
    return;
  }
  $Args['ClassReplace'] = $Args['ClassReplace']!='' ? $Args['ClassReplace'] : $Match['Class'];
  $Args['ExtendsClassReplace'] = $Args['ExtendsClassReplace']!='' ? $Args['ExtendsClassReplace'] : $Match['ExtendsClass'];
  $Result = <<<EOT
{$Match['ClassPadding']}/*
{$Match['ClassPadding']}*  @@ GDN Plugin Scaffold Refactor class @@
{$Match['ClassPadding']}*
{$Match['ClassPadding']}*  [{$Match['ClassOpening']}]
{$Match['ClassPadding']}*  - {$Match['Class']}{$Match['ClassMiddle']}{$Match['ExtendsClass']}
{$Match['ClassPadding']}*  + {$Args['ClassReplace']}{$Match['ClassMiddle']}{$Args['ExtendsClassReplace']}
{$Match['ClassPadding']}*/
{$Match['ClassPadding']}{$Match['ClassOpening']}{$Args['ClassReplace']}{$Match['ClassMiddle']}{$Args['ExtendsClassReplace']}
EOT;
  print EOL;
  print 'Refactor class:'.EOL;
  print " - {$Match['ClassPadding']}{$Match[0]}".EOL;
  print " + {$Match['ClassPadding']}{$Match['ClassOpening']}{$Args['ClassReplace']}{$Match['ClassMiddle']}{$Args['ExtendsClassReplace']}".EOL;
return $Result;
}



function SaveImg($SaveLoc, $Img, $IsString = FALSE){
  $Img = $IsString ? $Img : file_get_contents($Img);
  if($Img)
    file_put_contents($SaveLoc, $Img);
}

function IncludeFilesCallback($Matches){
  global $ScaffoldDir;
  return preg_replace('`^\s*<\?php\s*`',"\n",@file_get_contents($ScaffoldDir.DS.$Matches[1]));
}

function FileContentCallback($Matches){
  global $ScaffoldDir;
  return 'base64_decode(\''.base64_encode(@file_get_contents($ScaffoldDir.DS.$Matches[1])).'\');';
}


function ParseTemplateReplaceCallback($Matches){
  global $ScaffoldDir;
  $Template = @file_get_contents($ScaffoldDir.DS.$Matches[1]);
  return 'ParseTemplate(base64_decode(\''.base64_encode($Template).'\'), $PluginInfo, TRUE)';
}

function SaveImgCallback($Matches){
  global $ScaffoldDir;
  $Img = @file_get_contents($ScaffoldDir.DS.$Matches[2]);
  return $Matches[1].'base64_decode(\''.base64_encode($Img).'\'), TRUE)';
}

function SetConf(){
  ;
  global $ScaffoldDir;
  global $Conf;
  $CheckSum = ArrayI('CheckSum',$Conf);
  $Change = TRUE;
  if($CheckSum){
    unset($Conf['CheckSum']);
    if(md5(serialize($Conf))==$CheckSum){
      print 'Config options: '.EOL;
      foreach ($Conf As $I => $V)
        print '  '.$I.' => '.$V.EOL;
      $Change = AskYesOrNo('Do you want to change your author details / config?'.EOL);
    }
  }
  if($Change){
    $Conf['Author'] = AskValidateInput('Enter your name'.EOL,'`^.{0,100}$`','Invalid name, keep under 100 characters'.EOL);
    $Conf['AuthorUrl'] = AskValidateWebAddress('Enter a web address e.g. http://vanillaforums.org/profile/yournick'.EOL,'Invalid web address'.EOL);
    $Conf['AuthorEmail'] = AskValidateEmail('Enter an email'.EOL,'Invalid email address'.EOL);
    $Conf['CheckSum'] = md5(serialize($Conf));
    $ConfString = '<?php'.EOL;
    foreach ($Conf As $I => $V)
      $ConfString .= '$Conf[\''.$I.'\'] = \''.$V.'\';'.EOL;
    file_put_contents($ScaffoldDir.DS.'conf.php', $ConfString);
  }
}

function RecursiveCopy($SourceFolder, $DestFolder){
  $Iterator = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($SourceFolder, RecursiveDirectoryIterator::SKIP_DOTS),
      RecursiveIteratorIterator::SELF_FIRST
  );
  foreach ($Iterator as $File) {
    if(strpos($File->getPathname(),DS.'ps_backup')!==FALSE){
      continue;
    }
    if ($File->isDir()) {
      mkdir($DestFolder.DS.$Iterator->getSubPathname());
    } else {
      if(!copy($File, $DestFolder.DS.$Iterator->getSubPathname())){

        throw new Exception("Could not move $File to ".$DestFolder.DS.$Iterator->getSubPathname());
      }
    }
  }
}

function RecursiveDelete($SourceFolder){
  $Iterator = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($SourceFolder, RecursiveDirectoryIterator::SKIP_DOTS),
      RecursiveIteratorIterator::CHILD_FIRST
  );
  foreach ($Iterator as $File) {
    if ($File->isDir()){
      rmdir($File->getRealPath());
    } else {
      unlink($File->getRealPath());
    }

  }
}
function BackUp(){
  global $Dir;
  global $BkLimit;
  if(!count(glob($Dir.DS.'*.php')))
    return;
  try{
    $BackupGlob = glob($Dir.DS.'ps_backup*',GLOB_ONLYDIR);
    if(!count($BackupGlob)){
      $BackupDirLastNo = 0;
    }else{
      $BackupDirLast = $BackupGlob[count($BackupGlob)-1];
      $BackupDirLastNo = intval(substr($BackupDirLast, -5))+1;
    }
    $BackupDir = $Dir.DS.'ps_backup'.str_pad($BackupDirLastNo,5,'0',STR_PAD_LEFT);
    @mkdir($BackupDir);
    RecursiveCopy($Dir,$BackupDir);
    print "Plugin ps_backup created in $BackupDir".EOL.EOL;
    if(count($BackupGlob)>$BkLimit){

      if($BkLimit>1)
        $BackupGlob = array_slice($BackupGlob,0,-($BkLimit-1));
      foreach ($BackupGlob As $BackupDir){
        RecursiveDelete($BackupDir);
        rmdir($BackupDir);
      }

    }

  }catch (Exception $e){
    print 'Error: '.$e->getMessage().EOL;
    exit('Error: Failed to create ps_backup! Please copy plugin to ps_backup folder to override'.EOL);
  }

}

function GetClassChain($PluginFile,$Index){
  global $Dir;
  $ScaffoldFiles = glob($Dir.DS.'class.*.php');
  if(!$ScaffoldFiles)
    exit('Error: Nothing to refactor!'.EOL);
  $FileClassChain = array();
  if(in_array($PluginFile, $ScaffoldFiles)){
    foreach($ScaffoldFiles As $File){
      $FileContents = file_get_contents($File);
      $Match = array();
      if(preg_match('`(?P<ClassPadding>[ \t]*)(?P<ClassOpening>abstract\s+class\s+|class\s+)(?P<Current>[^\s]+)(?P<ClassMiddle>\s+extends\s+)(?P<Extends>Gdn_Plugin|('.$Index.'[^\s{]+))`i',$FileContents, $Match)){
        $FileClassChain[$Match['Extends']] = array('File'=> $File, 'Class'=> $Match['Current']);
      }
       
    }
    $FileClassChainOrdered = array();
    $Extends = array('Class' => 'Gdn_Plugin');
    while($Extends = ArrayI($Extends['Class'],$FileClassChain)){
      $FileClassChainOrdered[] = $Extends;
    }
    return array_reverse($FileClassChainOrdered);
     
  }

}

function FileContents($File){
  return file_get_contents($File);
}

function ReadMeTopics($ReadMe){
  $Sections = preg_split('`#([A-Z ]+)#`',$ReadMe, NULL, PREG_SPLIT_DELIM_CAPTURE);
  $Topics = array();
  foreach($Sections As $SectionI => $Section){
    if($SectionI % 2 == 1)
      $Topics[trim($Section)] = $Sections[$SectionI+1];

  }
   
  return $Topics;
}

function AbsPath($Path){
  
  $PosixID = posix_geteuid();
  
  if($PosixID && strpos($Path,'~'.DS)===0){
    $UserInfo = posix_getpwuid($PosixID);
    $Path = $UserInfo['dir'].DS.substr($Path,2);
  }else if($PosixID && strpos($Path,'~')===0){
    $UserInfo = posix_getpwuid(0);
    $Path = $UserInfo['dir'].DS.substr($Path,1);
  }else if(!strlen($Path) || strpos($Path,':')===false && substr($Path,0,1)!=DS){
    $Path=getcwd().DS.$Path;
  }
 
  $Path = str_replace(array('/', '\\'), DS, $Path);
  $Dirs = array_filter(explode(DS, $Path), 'strlen');
  $Abs = array();
  foreach($Dirs as $Dir){
    if('.'  == $Dir)
      continue;
    if('..' == $Dir){
      array_pop($Abs);
    } else {
      $Abs[] = $Dir;
    }
  }
  
  $Path=implode(DS, $Abs);
  
  if(file_exists($Path) && linkinfo($Path)>0)
    $Path=readlink($Path);
  
  $Path=!substr($Path,0,1)!=DS ? DS.$Path : $Path;
  
  return $Path;
}
