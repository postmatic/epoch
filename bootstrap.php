<?php


use postmatic\epoch\two\epoch;

spl_autoload_register(function ($class) {

	$prefix = 'postmatic\\epoch\\two\\';


	$base_dir = EPOCH_DIR . 'src/';


	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) {

		return;
	}

	$relative_class = substr($class, $len);
	$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
	if (file_exists($file)) {
		require $file;
	}


});


epoch::get_instance();
