<?php

Croogo::hookHelper('Nodes', 'NodeMenu.NodeMenu');
Croogo::hookHelper('*', 'NodeMenu.ButtonMenu');

CroogoNav::add('node-menu', 'edit', array(
	'icon'  => array('comments', 'large'),
	'title' => __d('croogo', 'Edit'),
	'url'   => array(
		'admin'      => true,
		'plugin'     => 'nodes',
		'controller' => 'nodes',
		'action'     => 'edit',
		'_id'
	)
));
