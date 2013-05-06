# ABOUT #

A utility/script for the Vanilla / Garden Framework, which 
will help your organise, often cluttered plugins.

# SETUP #

To make cd to Gdn_PScaffold and run

$ php make.php

As go through all the steps

You can make a link/shortcut in a convenient location e.g.

$ ln -s gdn_ps.php ~/gdn_ps

# GENERAL USAGE #

To run

$ php ~/gdn_ps --bklimit=3 --dir='/path/to/plugin'

optional bklimit is the amount of backup
history you want to keep (the default is 3)

dir is the path to you plugin directory
if the directory doesn't exist, creation
will be attempted.

You can also cd to the plugin folder and run
$ php ~/gdn_ps

If you wish to refactor 
(add or remove from the class chain)

$ php ~/gdn_ps --refactor

On Windows do the equivalent...

for help run 

$ php ~/gdn_ps --help

# EXAMPLES #

How to make  

![make](https://dl.dropboxusercontent.com/u/15933183/gdn_ps/ps_make_out.gif)

How to run 

![run](https://dl.dropboxusercontent.com/u/15933183/gdn_ps/ps_run_out.gif)

Scaffold existing plugin 

![existing](https://dl.dropboxusercontent.com/u/15933183/gdn_ps/ps_existing_out.gif)

Refactor an existing plugin 

![refactor](https://dl.dropboxusercontent.com/u/15933183/gdn_ps/ps_refactor_out.gif)

Demo of scaffolding and refactoring 

![show](https://dl.dropboxusercontent.com/u/15933183/gdn_ps/ps_show_out.gif)

File examples

- [class.utility.php](https://dl.dropboxusercontent.com/u/15933183/gdn_ps/ps_example/class.utility.php)
- [class.api.php](https://dl.dropboxusercontent.com/u/15933183/gdn_ps/ps_example/class.api.php)
- [class.settings.php](https://dl.dropboxusercontent.com/u/15933183/gdn_ps/ps_example/class.settings.php)
- [class.ui.php](https://dl.dropboxusercontent.com/u/15933183/gdn_ps/ps_example/class.ui.php)
- [class.existingpluginplugin.php](https://dl.dropboxusercontent.com/u/15933183/gdn_ps/ps_example/class.existingpluginplugin.php)

Also provided is additional readme's in how to implement
the various resources in each directory

# HELP #

NAME

  gdn_ps.php - Garden Plugin Scaffold (Gdn_PScaffold)
  
SYNOPSIS
  
  php gdn_ps.php [--refactor] [--bklimit=num] [--dir=path]
  
DESCRIPTION

  Gdn_PScaffold creates a scaffold or mini-framework
  for the purpose of organizing you code.
  
  It is a loose methodology for the Garden Framework
  https://github.com/vanillaforums/Garden
  
  It is designed to be as unobtrusive and minimal as 
  possible
  
  The general structure of a scaffolded plugin is as
  follows:
  
    ./class.yourplugin.php OR ./default.php
    ./class.ui.php
       [optional additional abstract classes]
    ./class.settings.php
    ./class.api.php
    ./class.utility.php
    ./views
    ./design
    ./js
    ./library
    ./models
    ./icon.png
    ./readme.markdown
    
  You have suggested directories, but specifically a 
  chain of abstract classes that extend Gdn_Pluign
  culminating in your plugin file.
  
  You can then keep your plugin file lean and shallow
  and move much of the logic to appropriate files in
  the chain and still be derivative of Gdn_Pluign.
  
  This program will generate the structure for you
  although if the plugin file exists, it will not 
  change it, except to refactor the class declaration
  to be at the top of the chain of inheritance. 

  However it can only try to do this. Please check that
  your plugin class extends YourPluginUI
  
  If it exist, you will be given the option for an example
  folder to be created, full of suggestions. gdn_ps will 
  also create any file structure not there, plus a 
  generated backup each time you run gdn_ps.
  
  It DOES NOT automatically move your code for you. It 
  isn't that smart. The files contain suggestions, use
  them for guidance. You can remove the backup and
  examples folder when appropriate. 
    
  The 'framework' extends Gdn_Pluign with an abstract
  YourPluginUtility which will automatically contain a 
  handful of methods that can be useful in developing 
  plugins
  
  Notably:
  
    Autoload - which will load all models matching
    models/class.*.php, and all class.*.php in the
    base of the plugin.
    
    HotLoad - which will load a setup each time the
    plugin is updated. You MUST implement abstract 
    method PluginSetup which should be friendly to
    hot loading. Typically this contains your db
    structure declaration and any other necessary
    setup.
    
    MiniDispatcher - works like the mini dispatcher
    that comes with Gdn_Plugin except it is pluggable
    meaning other plugins can create additional methods
    for the pseudo-controller. What's more you are not
    limited to one pseudo-controller you can set a 
    local and pluggable prefix, therefore implement
    the dispatcher multiple created methods. 
    
    ThemeView - gets a view location, checking first in
    the enabled theme, then in the plugin views enabling
    folk to copy the views across to their theme. 
  
  
  If you want additional files in the chain you can
  "refactor" which gives the option to add or remove
  abstract classes. The convention is the name is
  prefixed with the Plugin's index or class name
  and gdn_ps works on that basis, so will not know
  what to do otherwise.
  
  The plugin file itself extends YourPluginUI. You would
  not be able to add a class before this using gdn_ps
  
  You will also not be able to add a class before 
  YourPluignUtility which as it is extending Gdn_Plugin
  
  Other than that you can add a class anywhere along
  the chain if it makes any difference to you. 
  
  You can insert any number of classes, or remove those
  in between  YourPluignUtility and YourPluignUI

  In each directory that it implements as part of the
  scaffolded plugin it provides a readme suggesting
  how to implement such resources. 
  
OPTIONS

  --dir=path
     Specifies a path to a plugin. If the plugin folder
     doesn't exist it will try to create it.
     
  --bklimit=num
    Allows you to specify how many copies of backup is 
    kept, if you are using gnd_ps often
    
    by default the 3 last copies are kept. 
    
  --refactor
    will give you options to refactor the class chain 
    in an existent scaffolded plugin.
    
  --help 
    will bring up this help file
    
  --light
    stripped down plugins with:
    ./class.yourplugin.php
    ./class.ui.php
    [optional additional abstract classes]
    ./class.utility.php
    ./icon.png
    ./readme.markdown

AUTHORS

  Paul Thomas
       
COPYRIGHT

  Copyright (C) Paul Thomas 2013 all rights reserved. 
   
  This  script is released under the MIT License 
  shown in the LICENCE file.
