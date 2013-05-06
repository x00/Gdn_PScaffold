<?php if (!defined('APPLICATION')) exit();
abstract class {{Index}}Settings extends {{Index}}API {

  /*
   * @@ GDN Plugin Scaffold Example @@
   *
   * Settings Interface
   */

  protected function Settings_MenuItems($Sender, &$Args) {
    //$Menu = $Sender->EventArguments['SideMenu'];
    //$Menu->AddItem('{{Index}}', T('{{Name}}'),FALSE);
    //$Menu->AddLink('{{Index}}', T('Settings'), '{{SettingsUrl}}', 'Garden.Settings.Manage');
  }


  protected function Settings_Index($Sender, &$Args){
    $Sender->Permission('Garden.Settings.Manage');
    $Sender->Render($this->ThemeView('settings'));
  }

  /*
   * @@ GDN Plugin Scaffold Example @@
   *
   * protected function Settings_SomeMethod($Sender, &$Args){
   *		$Sender->Permission('Garden.Settings.Manage');
   *		$Sender->Render($this->ThemeView('someview'));
   * }
   *
   */


}
