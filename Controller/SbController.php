<?php

app::uses('Spyc', 'Sb.Yaml');
app::uses('Sbc', 'Sb.Superbake');
app::uses('Folder', 'Utility');

class SbController extends SbAppController {

	public function index() {
		
	}

	private function _selectConfigFile() {
		$sbc = new Sbc();

		if ($this->request->is('post')) {
			$fileToLoad = $this->request->data['configFile'];
		} else {
			$fileToLoad = Configure::read('Sb.defaultConfig');
		}
		// Find the different configuration files
		$configFolder = new Folder($sbc->getConfigPath());

		// Loads the file
		$sbc->loadFile($fileToLoad);

		// Giving the array to view
		$this->set('configFiles', $configFolder->find('(.*)\.yml', true));
		$this->set('configFile', $fileToLoad);
		$this->set('configFileDescription', $sbc->getConfig('description'));
		$this->set('log', $sbc->displayLog());
		$this->set('logErrors', $sbc->getErrors());
		$this->set('logWarnings', $sbc->getWarnings());

		return $sbc;
	}

	public function check() {
		$sbc = $this->_selectConfigFile();
		$this->set('completeConfig', Spyc::YAMLDump($sbc->getConfig()));
	}

	public function tree() {
		$sbc = $this->_selectConfigFile();
		// Prefixes and actions list:
		$defaults_prefixes_list = '';
		foreach ($sbc->getConfig('defaults.actions') as $prefix => $action) {
			$defaults_prefixes_list.=$prefix . ', ';
		}
		$this->set('defaults_prefixes_list', rtrim($defaults_prefixes_list, ', '));
		$this->set('completeConfig', $sbc->getConfig());
	}

	public function arraymerge() {
		$result = '';
		if ($this->request->is('post')) {
			$sbc = new Sbc();
			$spyc = new Spyc();
			$default = $spyc->YAMLLoadString($this->request->data['default']);
			$defined = $spyc->YAMLLoadString($this->request->data['defined']);
			$result = $spyc->YAMLDump($sbc->updateArray($default, $defined));
		}
		$this->set('default', $this->request->data['default']);
		$this->set('defined', $this->request->data['defined']);

		$this->set('result', $result);
	}

}