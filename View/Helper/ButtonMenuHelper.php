<?php

App::uses('AppHelper', 'View/Helper');

class ButtonMenuHelper extends AppHelper {

	public $helpers = array(
		'Html',
		'Croogo.Layout',
	);

	public function __construct(View $View, $settings = array()) {
		$this->helpers[] = Configure::read('Site.acl_plugin') . '.' . Configure::read('Site.acl_plugin');

		parent::__construct($View, $settings);
	}


	public function buttonMenu($nodeId, $menus, $options = array(), $depth = 0) {
		$options = Hash::merge(array(
			'type' => 'dropdown',
			'htmlAttributes' => array(
				'class' => 'btn-group'
			),
			'buttonClass' => 'btn btn-default btn-flat',
			'children' => true,
		), $options);

		$aclPlugin = Configure::read('Site.acl_plugin');
		$userId = AuthComponent::user('id');

		$sidebar = $options['type'] === 'sidebar';
		$htmlAttributes = $options['htmlAttributes'];
		$out = null;
		$sorted = Hash::sort($menus, '{s}.weight', 'ASC');
		if (empty($this->Role)) {
			$this->Role = ClassRegistry::init('Users.Role');
			$this->Role->Behaviors->attach('Croogo.Aliasable');
		}
		$currentRole = $this->Role->byId($this->Layout->getRoleId());

		foreach ($sorted as $menu) {
			if (isset($menu['separator'])) {
				$liOptions['class'] = 'divider';
				$out .= $this->Html->tag('li', null, $liOptions);
				continue;
			}
			if ($userId) {
				if ($currentRole != 'admin' && !$this->{$aclPlugin}->linkIsAllowedByUserId($userId, $menu['url'])) {
					continue;
				}
			} else {
				if (!$this->{$aclPlugin}->linkIsAllowedByRoleId(3, $menu['url'])) {
					continue;
				}
			}


			if (empty($menu['htmlAttributes']['class'])) {
				$menuClass = Inflector::slug(strtolower('menu-' . $menu['title']), '-');
				$menu['htmlAttributes'] = Hash::merge(array(
					'class' => $menuClass
				), $menu['htmlAttributes']);
			}
			$title = '';
			if ($menu['icon'] === false) {
			} elseif (empty($menu['icon'])) {
				$menu['htmlAttributes'] += array('icon' => 'white');
			} else {
				$menu['htmlAttributes'] += array('icon' => $menu['icon']);
			}

			if ($depth > 0) {
				$title = $menu['title'];
			} else {
//				$title .= '<span>' . $menu['title'] . '</span>';
				$title .= $menu['title'];
			}

			$children = '';
			if (!empty($menu['children'])) {
				$childClass = '';
				if ($sidebar) {
					$childClass = 'nav nav-stacked sub-nav ';
					$childClass .= ' submenu-' . Inflector::slug(strtolower($menu['title']), '-');
					if ($depth > 0) {
						$childClass .= ' dropdown-menu';
					}
				} else {
					if ($depth == 0) {
						$childClass = 'dropdown-menu';
					}
				}
				$children = $this->buttonMenu($menu['children'], array(
					'type' => $options['type'],
					'children' => true,
					'htmlAttributes' => array(
						'class' => $childClass,
						'role'  => 'menu'
					),
				), $depth + 1);

				//$menu['htmlAttributes']['class'] .= ' hasChild dropdown-close';
			}
			//$menu['htmlAttributes']['class'] .= ' sidebar-item';

			$menuUrl = $this->url($menu['url']);
			if ($menuUrl == env('REQUEST_URI')) {
				if (isset($menu['htmlAttributes']['class'])) {
					$menu['htmlAttributes']['class'] .= ' current';
				} else {
					$menu['htmlAttributes']['class'] = 'current';
				}
			}

			/*if (!$sidebar && !empty($children)) {
				if ($depth == 0) {
					$title .= ' <b class="caret"></b>';
				}
				$menu['htmlAttributes']['class'] = 'dropdown-toggle';
				$menu['htmlAttributes']['data-toggle'] = 'dropdown';
			}*/

			if (isset($menu['before'])) {
				$title = $menu['before'] . $title;
			}

			if (isset($menu['after'])) {
				$title = $title . $menu['after'];
			}

			if ($depth === 0) {
				$menu['htmlAttributes']['class'] .= ' ' . $options['buttonClass'];
			}

			$menu['url'] = str_replace('_id', $nodeId, $menu['url']);

			$menu['htmlAttributes']['target'] = '_blank';
			$link = $this->Html->link($title, $menu['url'], $menu['htmlAttributes']);
			$liOptions = array();
			if (!$sidebar && !empty($children)) {
				if ($depth === 0) {
					$liOptions['class'] = 'btn-group';
				}
			}
			if ($depth > 0) {
				$out .= $this->Html->tag('li', $link . $children, $liOptions);
			} else {
				if (!empty($menu['children'])) {
					$out .= $this->Html->tag('div', $link . '<button type="button" class="' . $options['buttonClass'] . ' dropdown-toggle" data-toggle="dropdown"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>'. $children, array(
						'class' => 'btn-group'
					));
				} else {
					$out .= $link;
				}
			}
		}

		if ($depth === 0) {
			return $this->Html->tag('div', $out, $htmlAttributes);
		} else {
			return $this->Html->tag('ul', $out, $htmlAttributes);
		}
	}


}
