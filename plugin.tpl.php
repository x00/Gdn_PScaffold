<?php if (!defined('APPLICATION')) exit();

/* 
 * @@ GDN Plugin Scaffold Suggestion @@
 * 
 * Plugin Info
 * 
 * Please check and change!
 * 
 */

$PluginInfo['{{Index}}'] = array(
   'Name' => '{{Name}}',
   'Description' => "{{Description}}",
   'SettingsUrl' => '{{SettingsUrl}}',
   'Version' => '{{Version}}',
   'RequiredPlugins'=> array(),
   'RequiredApplications' => array('Vanilla' => '2.0.18.8'),
   'RegisterPermissions' => array(),
   'Author' => '{{Author}}',
   'AuthorEmail' => '{{AuthorEmail}}',
   'AuthorUrl' => '{{AuthorUrl}}',
   'MobileSite' => FALSE
);

/*
 * @@ GDN Plugin Scaffold @@
 * 
 * readme: see readme.markdown
 * changelog: see changlog.txt
 */


/* 
 * @@ GDN Plugin Scaffold @@
 * 
 * GDN Plugin Scaffold Init,
 * which includes scaffold utility methods
 */
include_once(dirname(__FILE__).DS.'class.utility.php');


/* 
 * @@ GDN Plugin Scaffold @@
 * 
 * loads models, ui, settings, plugin api files
 */
{{Index}}Utility::AutoLoad();


/* 
 * @@ GDN Plugin Scaffold @@
 * 
 * inherits Gnd_Plugin 
 * through the mini framework
 */
class {{Index}} extends {{Index}}UI{

/* @@ GDN Plugin Scaffold Examples @@
 * 
 * put general plugin specific methods, and helpers in class.api.php
 * put any interface stuff in class.ui.php, class.settings.php
 * 
 * shallow interface front ends
 * 
 *  public function NameController_SomeEvent_Handler($Sender, &$Args){
 *		// refernce to protected method in class.ui.php, class.settings.php
 *		// or other abstract interface class
 *		$this->Name_SomeEvent($Sender, $Args);
 *  }
 *  
 *  public function NameController_SomeMethod_Create($Sender, &$Args){
 *		// refernce to protected method in class.ui.php, class.settings.php
 *		// or other abstract interface class in the chain
 *  	$this->Name_YourMethod($Sender, $Args);
 *  }
 *  
 *  public function NameController_Render_Before($Sender, &$Args){
 *		// refernce to protected method in class.ui.php, class.settings.php
 *		// or other abstract interface class in the chain
 *  	$this->Name_RenderBefore($Sender, $Args);
 *  }
 *  
 *  public function NameModel_Event_Handler($Sender, &$Args){
 *		// refernce to protected method in class.ui.php, class.settings.php
 *		// or other abstract interface class in the chain
 *  	$this->NameModel_Event($Sender, $Args);
 *  }
 *  
 *  // settings
 *  
 *  protected function SettingsController_MenuItems_Handler($Sender) {
 *  	$this->Settings_MenuItems($Sender, $Args);
 *  }
 *  
 *  protected function SettingsController_{{Index}}_Create($Sender, &$Args){
 *  	$this->Settings_Index($Sender, $Args)
 *  }
 */
  
  public function Base_BeforeDispatch_Handler($Sender){    
    /*
     * @@ GDN Plugin Scaffold  @@
     * 
     * generally speaking you want to "hot load" 
     * as to make any migration changes
     * and new configuration automatically update
     */ 
    
    $this->HotLoad();
  }
  
	public function Setup() {
    $this->HotLoad(TRUE);
	}
  
  /*
   * @@ GDN Plugin Scaffold @@
   * 
   * PluginSetup is a required abstract method
   * include Gdn::Structure() related stuff
   * include any other setup
   * ensure "hot load" friendly
   */ 
	public function PluginSetup(){
    
	}
	
  /*
   * @@ GDN Plugin Scaffold Suggestions @@
   * 
   * sometimes you need to make special 
   * "Block Exceptions" in order allow 
   * access to HTTP apis and webhooks, etc
   * or just some page that you want to 
   * ensure are public especially is very
   * closed environment. 
   * 
   * public function Base_BeforeBlockDetect_Handler($Sender,$Args){
   * 		// use valid PCRE expression
   * 		$Sender->EventArguments['BlockExceptions']['`your/uri`']=Gdn_Dispatcher::BLOCK_NEVER;
   * 		$Sender->EventArguments['BlockExceptions']['`go/away`']=Gdn_Dispatcher::BLOCK_ANY;
   * }
   */
  
}