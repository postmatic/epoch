<?php
/**
 * Base class for settings save and sanitization
 *
 * @package   Epoch
 * @author    Postmatic
 * @license   GPL-2.0+
 * @link
 * Copyright 2016 Transitive, Inc.
 */
namespace postmatic\epoch\two\admin;


abstract  class settings {

	/**
	 * Key for our option
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $option_key;

	/**
	 * Keys to allow in options array
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $settings_keys;

	/**
	 * save constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param string $option_key
	 * @param array $settings_keys
	 */
	public function __construct( $option_key, array $settings_keys ) {
		$this->option_key = $option_key;
		$this->settings_keys = $settings_keys;
	}
	
	
}
