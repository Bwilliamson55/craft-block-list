# Block List plugin for Craft CMS 3.x

A CP extension for basic ip block-list entries

![Screenshot](resources/img/plugin-logo.png)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require /block-list

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Block List.
   1. Alternatively, from the project root- `php craft plugin/install block-list`

## Block List Overview

This plugin provides a simple Create, Read, Update, and Delete (CRUD) interface for managing block-listed IP addresses.  
The usage of this functionality is up to the developer, it is not, by default, plugged into any Craft authentication mechanisms.

## Configuring Block List

The default install file contains some entries already.  
No configuration is needed beyond adding your Site License(s) and their related IP entries.

## Using Block List

After installation a new CP area 'Block List' will be available.  
Inside this area you can Create Site Licenses, which then present you with detail fields for that license.  
In addition, each license can hold any number of IP addresses associated with it.
Currently the License details do not play a roll in authenticating IPs. ie any IP in any license is blocked, or allowed depending on how you implement this.  
This functionality may change with future releases.  

Once there are IP entries associated with a License, from any template you can authenticate an IP via:  
`craft.blockList.auth($ip)`  
The 'blockList' variable is available to create licenses and entries programmatically as well.  

To use the plugin in PHP:  
`$authenticated = BlockList::$plugin->blockListService->authenticate($ip);`
## Block List Roadmap

Some things to do, and ideas for potential features:

* Release it
* Improve it
* Use it

Brought to you by [Benjamin Williamson](github.com/Bwilliamson55)
