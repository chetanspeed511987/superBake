<?php
/**
 * Index view
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default.Views
 * @version       0.3
 * 
 * ---
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
//Page headers and licensing
include($themePath . 'views/common/headers.ctp');
/* ----------------------------------------------------------------------------
 * Current template options
 */
// Hidden fields
if (!isset($hiddenFields) || !is_array($hiddenFields)) {
	$hiddenFields = array();
}

// Sortable fields
if (!isset($sortableFields) || !is_array($sortableFields)) {
	$sortableFields = array();
}

// Additionnal CSS
if (!isset($additionnalCSS) || !is_array($additionnalCSS)) {
	$additionnalCSS = array();
}
// Additionnal JS
if (!isset($additionnalJS) || !is_array($additionnalJS)) {
	$additionnalJS = array();
}

// Hide actions list for entries
if (!isset($hideActionsList)) {
	$hideActionsList = false;
}

/* ----------------------------------------------------------------------------
 * Toolbar options
 */
// No toolbar option
if (!isset($noToolbar)) {
	$noToolbar = false;
}

// Toolbar : Hidden controllers are handled in the toolbar template file
if ($noToolbar === false) {
	include(dirname(__FILE__) . DS . 'common' . DS . 'toolbar_buttons.ctp');
}



/* ----------------------------------------------------------------------------
 * View
 */
?>
<div class="<?php echo $pluralVar; ?> index">
	<h2><?php echo "<?php echo __('{$pluralHumanName}'); ?>"; ?></h2>
	<table cellpadding="0" cellspacing="0">
		<tr>
			<?php foreach ($fields as $field): ?>
				<th><?php echo "<?php echo \$this->Paginator->sort('{$field}'); ?>"; ?></th>
			<?php endforeach; ?>
			<th class="actions"><?php echo "<?php echo " . $this->iString('Actions') . "; ?>"; ?></th>
		</tr>
		<?php
		echo "<?php foreach (\${$pluralVar} as \${$singularVar}): ?>\n";
		echo "\t<tr>\n";
		foreach ($fields as $field) {
			$isKey = false;
			if (!empty($associations['belongsTo'])) {
				foreach ($associations['belongsTo'] as $alias => $details) {
					if ($field === $details['foreignKey']) {
						$isKey = true;
						echo "\t\t<td>\n\t\t\t<?php echo \$this->Html->link(\${$singularVar}['{$alias}']['{$details['displayField']}'], array('controller' => '{$details['controller']}', 'action' => 'view', \${$singularVar}['{$alias}']['{$details['primaryKey']}'])); ?>\n\t\t</td>\n";
						break;
					}
				}
			}
			if ($isKey !== true) {
				echo "\t\t<td><?php echo h(\${$singularVar}['{$modelClass}']['{$field}']); ?>&nbsp;</td>\n";
			}
		}

		echo "\t\t<td class=\"actions\">\n";
		echo "\t\t\t<?php echo \$this->Html->link(" . $this->iString('View') . ", array('action' => 'view', \${$singularVar}['{$modelClass}']['{$primaryKey}'])); ?>\n";
		echo "\t\t\t<?php echo \$this->Html->link(" . $this->iString('Edit') . ", array('action' => 'edit', \${$singularVar}['{$modelClass}']['{$primaryKey}'])); ?>\n";
		echo "\t\t\t<?php echo \$this->Form->postLink(" . $this->iString('Delete') . ", array('action' => 'delete', \${$singularVar}['{$modelClass}']['{$primaryKey}']), null, __('Are you sure you want to delete # %s?', \${$singularVar}['{$modelClass}']['{$primaryKey}'])); ?>\n";
		echo "\t\t</td>\n";
		echo "\t</tr>\n";

		echo "<?php endforeach; ?>\n";
		?>
	</table>

	<?php
	// Pagination goes here
	include($themePath . 'views/common/pagination.ctp');
	?>
</div>
