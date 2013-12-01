<?php
/**
 * Menu template for SuperBake
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Menus
 * @version       0.3
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
 *  You should have received a copy of the GNU œGeneral Public License
 *  along with EL-CMS. If not, see <http://www.gnu.org/licenses/> 
 */
// Hidden plugins
if (!isset($hiddenPlugins) || !is_array($hiddenPlugins)) {
	$hiddenPlugins = array();
}
// Hidden controllers
if (!isset($hiddenControllers) || !is_array($hiddenControllers)) {
	$hiddenControllers = array();
}
// Hidden actions in controllers
if (!isset($hiddenControllerActions) || !is_array($hiddenControllerActions)) {
	$hiddenControllerActions = array();
}
// Globally hidden actions
if (!isset($hiddenActions) || !is_array($hiddenActions)) {
	$hiddenActions = array();
}
?>

<h3><?php echo "<?php echo " . $this->iString('Menu') . "?>"; ?></h3>
<?php
$menu = $this->sbc->getActionsAll();
?>
<ul>
	<?php
	// plugins
	foreach ($menu as $plugin => $pluginConfig) {
		if (!in_array($plugin, $hiddenPlugins)) {
			?>
			<li><strong><?php echo "<?php echo " . $this->iString($pluginConfig['displayName']) . "?>" ?></strong></li>
			<ul>
				<?php
				//Controllers
				foreach ($pluginConfig['controllers'] as $controller => $controllerConfig) {
					if (!in_array($controller, $hiddenControllers)) {
						?>
						<li><?php echo "<?php echo " . $this->iString($controllerConfig['displayName']) . "?>" ?></li>
						<ul>
							<?php
							// Prefixes
							foreach ($controllerConfig['prefixes'] as $prefix => $actions) {
								// Adding controller to hidden controllers to avoid index notices later.
								if (!isset($hiddenControllerActions[$controller])) {
									$hiddenControllerActions[$controller] = array();
								}

								// $prefixes is one option from the config file.
								if (in_array($prefix, $prefixes)) {
									// Actions
									foreach ($actions as $action) {
										if (!in_array($action, $hiddenActions) && !in_array($action, $hiddenControllerActions[$controller])) {
											switch ($action) {
												case 'index':
													$actionName = "List " . $controllerConfig['displayName'];
													break;
												case 'add':
													$actionName = "New " . $controllerConfig['displayName'];
													break;
												case 'register':
													$actionName = 'Register';
													break;
												case 'login':
													$actionName = 'Log in';
													break;
												case 'logout':
													$actionName = 'Log out';
												default:
													$actionName = Inflector::humanize(Inflector::underscore($action)) . ' ' . $controllerConfig['displayName'];
											}
											echo "<li><?php echo \$this->Html->link(" . $this->iString($actionName) . ', ' . $this->url($action, $controller, $prefix) . "); ?></li>\n";
										}
									}
								}
							}
							?>
						</ul>
						<?php
					}
				}
				?>
			</ul>
			<?php
		}
	}
	?>
</ul>