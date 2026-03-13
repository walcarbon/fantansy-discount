<?php

/**
 * Autoload classes from the includes directory
 *
 * @package Sales_System_Metrics
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

// Define the path to the includes directory, if it's not already defined
if ( !defined( 'VCPL_PLUGIN_PATH' ) ) define( 'VCPL_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/' );


// Register the autoload function, if it's not already registered.
// Keep the legacy misspelled function name for backwards compatibility.
if ( !function_exists( 'vcpl_autoload' ) ) {
  function vcpl_autoload()
  {
    spl_autoload_register( 'vcpl_plugin_autoload' );
  }
}

if ( !function_exists( 'vcpl_autolaod' ) ) {
  function vcpl_autolaod()
  {
    vcpl_autoload();
  }
}

/**
 * Get the file path from the class namespace
 *
 * @param string $class The fully-qualified class name
 * @return string|bool The file path if found, false otherwise
 */
if ( !function_exists( 'vcpl_get_file_from_namespace' ) ) {
  function vcpl_get_file_from_namespace( $class )
  {
    $classLower = strtolower( $class );
    $namespace = str_replace( ['\\', '_'], ['/', '-'], $classLower );

    $prefix = 'class';
    if ( strpos( $classLower, 'abstracts' ) !== false ) {
      $prefix = 'abstract-class';
    } elseif ( strpos( $classLower, 'traits' ) !== false ) {
      $prefix = 'trait';
    }

    $namespace = strpos( $class, '\\' ) !== false
      ? dirname( $namespace ) . '/' . $prefix . '-' . basename( $namespace ) . '.php'
      : $prefix . '-' . $namespace . '.php';

    return $namespace;
  }
}

/**
 * Load a file
 *
 * @param string $path The file path
 * @return bool True if the file was loaded, false otherwise
 */
if ( !function_exists( 'vcpl_load_file' ) ) {
  function vcpl_load_file( $path )
  {
    if ( $path && is_readable( $path ) ) {
      require_once $path;
      return true;
    }
    return false;
  }
}

/**
 * Autoload classes
 *
 * @param string $class The fully-qualified class name
 * @return bool True if the class was loaded, false otherwise
 */
if ( !function_exists( 'vcpl_plugin_autoload' ) ) {
  function vcpl_plugin_autoload( $class )
  {
    require VCPL_PLUGIN_PATH . 'helper/vcpl-functions.php';
    $class = strtolower( $class );
    $namespace = vcpl_get_file_from_namespace( $class );
    $namespace ? vcpl_load_file( VCPL_PLUGIN_PATH . $namespace ) : false;
  }
}

vcpl_autoload();
