<?php if (!defined('APPLICATION')) exit();
abstract class {{Index}}Utility extends Gdn_Plugin {

  /**
   * @@ GDN Plugin Scaffold Utility Method @@
   *
   * Loading the mini framework / scaffold
   *
   * First loads models matching model/class.*model.php
   * Then all class.*.php in plugin root
   *
   * @return @void
   */

  public static function AutoLoad(){
    $PluginRoot = dirname(__FILE__);
    if(file_exists($PluginRoot.DS.'models')){
      $ModelFiles = glob($PluginRoot.DS.'models'.DS.'class.*model.php');

      foreach ($ModelFiles As $ModelFile)
        include_once($PluginRoot.DS.'models'.DS.basename($ModelFile));
    }


    $PluginFiles = glob($PluginRoot.DS.'class.*.php');

    foreach ($PluginFiles As $PluginFile)
      include_once($PluginRoot.DS.basename($PluginFile));
     
     
  }

  /**
   * @@ GDN Plugin Scaffold Utility Method @@
   *
   * Used with HotLoad
   *
   * Required abstract method
   * @abstract
   */

  abstract function PluginSetup();

  /**
   * @@ GDN Plugin Scaffold Utility Method @@
   *
   * Way to ensure any new db struture gets created
   * And new setup is allied without
   *
   * @param bool $Force do regardless of version change (optional) default FALSE
   *
   * @return void
   */

  protected function HotLoad($Force =  FALSE) {
    if($Force || C('Plugins.'.$this->GetPluginIndex().'.Version')!=$this->PluginInfo['Version']){
      $this->PluginSetup();

      SaveToConfig('Plugins.'.$this->GetPluginIndex().'.Version', $this->PluginInfo['Version']);
    }
  }

  /**
   * @@ GDN Plugin Scaffold Utility Method @@
   *
   * Pluggable dispatcher
   * e.g. public function PluginNameController_Test_Create($Sender){}
   *
   * or
   *
   * public function Controller_Test($Sender){}
   *
   * Internally
   *
   * @param string $Sender the Controller object from which to dispatch from
   * @param string $PluggablePrefix for new methods in other plugins (optional) default behaviour is  {{Index}}.Controller_
   * @param string $LocalPrefix for new methods locally (optional) default behaviour is Controller_
   *
   * @return void
   */

  protected function MiniDispatcher($Sender, $PluggablePrefix = NULL, $LocalPrefix = NULL){
    $PluggablePrefix = $PluggablePrefix ? $PluggablePrefix : $this->GetPluginIndex().'Controller_';
    $LocalPrefix = $LocalPrefix ? $LocalPrefix : 'Controller_';
    $Sender->Form = new Gdn_Form();
     
    $Plugin = $this;

    $ControllerMethod = '';
    if(count($Sender->RequestArgs)){
      list($MethodName) = $Sender->RequestArgs;
    }else{
      $MethodName = '_Index';
    }

    $DeclaredClasses = get_declared_classes();

    $TempControllerMethod = $LocalPrefix.$MethodName;
    if (method_exists($Plugin, $TempControllerMethod)){
      $ControllerMethod = $TempControllerMethod;
    }
    if(!$ControllerMethod){
      $TempControllerMethod = $PluggablePrefix.$MethodName.'_Create';

      foreach ($DeclaredClasses as $ClassName) {
        if (Gdn::PluginManager()->GetPluginInfo($ClassName)){
          $CurrentPlugin = Gdn::PluginManager()->GetPluginInstance($ClassName);
          if($CurrentPlugin && method_exists($CurrentPlugin, $TempControllerMethod)){
            $Plugin = $CurrentPlugin;
            $ControllerMethod = $TempControllerMethod;
            break;
          }
        }
      }

    }

    if (method_exists($Plugin, $ControllerMethod)) {
      $Sender->Plugin = $Plugin;
      return call_user_func(array($Plugin,$ControllerMethod),$Sender);
    } else {
      $PluginName = get_class($this);
      throw NotFoundException();
    }
  }
  /**
   * @@ GDN Plugin Scaffold @@
   *
   * Set view that can be copied over to current theme
   * e.g. view.php -> themes/the_theme/views/plugins/{{Index}}/view.php
   *
   * @param string $View name of view
   *
   * @return string Absolute path of view
   *
   */

  protected function ThemeView($View){

    $ThemeViewLoc = CombinePaths(array(
        PATH_THEMES, Gdn::Controller()->Theme, 'views', $this->GetPluginFolder()
    ));
    if(file_exists($ThemeViewLoc.DS.$View.'.php')){
      $View=$ThemeViewLoc.DS.$View.'.php';
    }else{
      $View=$this->GetView($View.'.php');
    }
    return $View;

  }
  /**
   *  @@ GDN Plugin Scaffold @@
   *
   *  Add a route on the fly
   *
   *  Typically set in Base_BeforeLoadRoutes_Handler
   *
   *  @param string $Routes loaded
   *  @param string $Route RegExp of route
   *  @param string $Destination to rout to
   *  @param string $Type of redirect (optional), default 'Internal' options Internal,Temporary,Permanent,NotAuthorized,NotFound
   *  @param bool $OneWay if an Internal request prevents direct access to destination  (optional), default FALSE
   *
   *  @return void
   */

  protected function DynamicRoute(&$Routes, $Route, $Destination, $Type = 'Internal', $Oneway = FALSE){
    $Key = str_replace('_','/',base64_encode($Route));
    $Routes[$Key] = array($Destination, $Type);
    if($Oneway && $Type == 'Internal'){
      if(strpos(strtolower($Destination), strtolower(Gdn::Request()->Path()))===0){
        Gdn::Dispatcher()->Dispatch('Default404');
        exit;
      }
    }
  }

}