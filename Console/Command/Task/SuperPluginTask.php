<?php

/**
 * SuperBake Shell script - superPlugin Task - Generates plugins structures
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       ELCMS.superBake.Task
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @version       0.3
 *
 * This file is based on the lib/Cake/Console/Command/Task/PluginTask.php file 
 * from CakePHP.
 * 
 * ----
 * 
 *  This file is part of EL-CMS.
 *
 *  EL-CMS is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  EL-CMS is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 * 
 *
 *  You should have received a copy of the GNU General Public License
 *  along with EL-CMS. If not, see <http://www.gnu.org/licenses/> 
 */

// SbShell
App::uses('SbShell', 'Sb.Console/Command');

// Files from Cake
App::uses('File', 'Utility');
// Folder from Cake
App::uses('Folder', 'Utility');

/**
 * This class generates plugins structure.
 */
class SuperPluginTask extends SbShell {

	/**
	 * path to plugins directory
	 *
	 * @var array
	 */
	public $path = null;

	/**
	 * Path to the bootstrap file. Changed in tests.
	 *
	 * @var string
	 */
	public $bootstrap = null;

	/**
	 * Plugin to bake (passed from shell)
	 * @var string
	 */
	public $currentPlugin = null;

	/**
	 * Plugin configuration (name, path,...) (passed from shell)
	 * @var array
	 */
	public $pluginConfig = null;

	/**
	 * Update bootstrap (passed from shell)
	 * @var bool
	 */
	public $updateBootstrap = null;

	/**
	 * initialize
	 *
	 * @return void
	 */
	public function initialize() {
		$this->path = current(App::path('plugins'));
		$this->bootstrap = APP . 'Config' . DS . 'bootstrap.php';
	}

	/**
	 * Execution method always used for tasks
	 *
	 * @return void
	 */
	public function execute() {
		// No interactive mode. Creates the structure. Dot.
		$this->path = $this->cleanPath($this->pluginConfig['path'], true);
		$this->bake($this->currentPlugin);
	}

	/**
	 * Bake the plugin, create directories and files
	 *
	 * @param string $plugin Name of the plugin in CamelCased format
	 * @return boolean
	 */
	public function bake($plugin) {
		$pathOptions = App::path('plugins');
		$ereg = '@' . $this->path . '$@';

		// Verify if the path is in the possible plugins pathes
		foreach ($pathOptions as $possiblePath) {
			if (preg_match($ereg, $possiblePath)) {
				$this->path = $possiblePath;
				$this->out(__d('superBake', 'The "%s" plugin will be created in the "%s" dir.', $plugin, $this->path), 1, Shell::VERBOSE);
			}
		}
		if (!is_dir($this->path . $plugin)) {
			$Folder = new Folder($this->path . $plugin);
			$directories = array(
				'Config' . DS . 'Schema',
				'Model' . DS . 'Behavior',
				'Model' . DS . 'Datasource',
				'Console' . DS . 'Command' . DS . 'Task',
				'Controller' . DS . 'Component',
				'Lib',
				'View' . DS . 'Helper',
				'Test' . DS . 'Case' . DS . 'Controller' . DS . 'Component',
				'Test' . DS . 'Case' . DS . 'View' . DS . 'Helper',
				'Test' . DS . 'Case' . DS . 'Model' . DS . 'Behavior',
				'Test' . DS . 'Fixture',
				'Vendor',
				'webroot'
			);

			foreach ($directories as $directory) {
				$dirPath = $this->path . $plugin . DS . $directory;
				$Folder->create($dirPath);
				new File($dirPath . DS . 'empty', true);
			}

			foreach ($Folder->messages() as $message) {
				$this->out($message, 1, Shell::VERBOSE);
			}

			$errors = $Folder->errors();
			if (!empty($errors)) {
				foreach ($errors as $message) {
					$this->error($message);
				}
				return false;
			}

			$controllerFileName = $plugin . 'AppController.php';

			$out = "<?php\n\n";
			$out .= "App::uses('AppController', 'Controller');\n\n";
			$out .= "class {$plugin}AppController extends AppController {\n\n";
			$out .= "}\n";
			$this->createFile($this->path . $plugin . DS . 'Controller' . DS . $controllerFileName, $out);

			$modelFileName = $plugin . 'AppModel.php';

			$out = "<?php\n\n";
			$out .= "App::uses('AppModel', 'Model');\n\n";
			$out .= "class {$plugin}AppModel extends AppModel {\n\n";
			$out .= "}\n";
			$this->createFile($this->path . $plugin . DS . 'Model' . DS . $modelFileName, $out);

			/**	***************************************************************
			 * 
			 * Additionnal files are handled here
			 * 
			 */
			
			// Bootstrap file
			if ($this->pluginConfig['haveBootstrap']) {
				$out = "<?php\n\n";
				$out .="/* This is the " . $this->pluginConfig['displayName'] . "'s bootstrap file.\n *\n * Use it to define your plugin's configuration values.\n *\n */\n";
				$this->createFile($this->path . $plugin . DS . 'Config' . DS . 'bootstrap.php', $out);
			}

			//
			// Route file
			if ($this->pluginConfig['haveRoutes']) {
				$out = "<?php\n\n";
				$out .="/* Here comes your plugin's routes here.\n * \n * @todo Remember to define the \"$plugin\" plugin's routes\n */\n";
				$this->createFile($this->path . $plugin . DS . 'Config' . DS . 'routes.php', $out);
			}

			// Main bootstrap file update
			if ($this->updateBootstrap == 'Y') {
				$this->_modifyBootstrap($plugin);
			}

			$this->hr();
			$this->out(__d('cake_console', '<success>Created:</success> %s in %s', $plugin, $this->path . $plugin), 2);
		} else {
			$this->out(__d('superBake', '<warning>The "%s" plugin was not created, as its folder already exists.</warning>', $plugin));
		}

		return true;
	}

	/**
	 * Update the app's bootstrap.php file.
	 *
	 * @param string $plugin Name of plugin
	 * @return void
	 */
	protected function _modifyBootstrap($plugin) {
		$bootstrap = new File($this->bootstrap, false);
		$contents = $bootstrap->read();
		if (!preg_match("@\n\s*CakePlugin::loadAll@", $contents)) {
			$bootstrap->append("\nCakePlugin::load('$plugin', array('bootstrap' => false, 'routes' => false));\n");
			$this->out('');
			$this->out(__d('superBake', '%s modified', $this->bootstrap));
		}
	}

}
