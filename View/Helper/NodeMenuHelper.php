<?php
App::uses('AppHelper', 'View/Helper');

/**
 * Example Helper
 *
 * An example hook helper for demonstrating hook system.
 *
 * @category Helper
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class NodeMenuHelper extends AppHelper {

/**
 * Other helpers used by this helper
 *
 * @var array
 * @access public
 */
	public $helpers = array(
		'Html',
		'Croogo.Layout',
		'Nodes' => array('className' => 'Nodes.Nodes'),
	);

/**
 * Called after LayoutHelper::nodeBody()
 *
 * @return string
 */
	public function afterNodeInfo() {
		return $this->_View->element('NodeMenu.node-menu', array(
			'id' => (int) $this->Nodes->field('id')
		));
	}

}
