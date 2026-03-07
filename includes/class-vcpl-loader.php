<?php

/**
 * Define the functionality to load actions and filters
 *
 * @package    Includes
 * @subpackage Includes/Loader
 * @version    1.0.0
 */

namespace Includes;

defined( 'ABSPATH' ) || exit;

class VCPL_Loader
{
  /**
   * Actions registered with WordPress to fire when the plugin loads.
   *
   * @var array
   */
  protected $actions;

  /**
   * Filters registered with WordPress to fire when the plugin loads.
   *
   * @var array
   */
  protected $filters;


  // Initialize the collections used to maintain the actions and filters.
  public function __construct()
  {
    $this->actions = array();
    $this->filters = array();
  }

  /**
   * Register a new action hook with the WordPress.
   *
   * @param  string $hook The name of the WordPress action that is being registered.
   * @param  object $component A reference to the instance of the object on which the action is defined.
   * @param  string $callback The name of the function definition on the $component.
   * @param  int    $priority Optional. he priority at which the function should be fired. Default is 10.
   * @param  int    $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
   * @return self
   */
  public function add_action( string $hook, object $component, string $callback, int $priority = 10, int $accepted_args = 1 ): self
  {
    $this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
    return $this;
  }

  /**
   * Register a new filter hook with the WordPress.
   *
   * @param  string $hook The name of the WordPress filter that is being registered.
   * @param  object $component A reference to the instance of the object on which the filter is defined.
   * @param  string $callback The name of the function definition on the $component.
   * @param  int    $priority Optional. he priority at which the function should be fired. Default is 10.
   * @param  int    $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
   * @return self
   */
  public function add_filter( string $hook, object $component, string $callback, int $priority = 10, int $accepted_args = 1 ): self
  {
    $this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
    return $this;
  }

  /**
   * Register the actions and hooks into a single collection.
   *
   * @param  array  $hooks The collection of hooks that is being registered (that is, actions or filters).
   * @param  string $hook The name of the WordPress filter that is being registered.
   * @param  object $component A reference to the instance of the object on which the filter is defined.
   * @param  string $callback The name of the function definition on the $component.
   * @param  int    $priority The priority at which the function should be fired.
   * @param  int    $accepted_args The number of arguments that should be passed to the $callback.
   * @return array  The collection of actions and filters registered with WordPress.
   */
  private function add( array $hooks, string $hook, object $component, string $callback, int $priority = 10, int $accepted_args = 1 ): array
  {
    $hooks[] = array(
      'hook'          => $hook,
      'component'     => $component,
      'callback'      => $callback,
      'priority'      => $priority,
      'accepted_args' => $accepted_args
    );
    return $hooks;
  }

  // Register the filters and actions with WordPress.
  public function run(): void
  {
    foreach ( $this->actions as $action ) {
      extract( $action, EXTR_OVERWRITE );
      add_action( $hook, array( $component, $callback ), $priority, $accepted_args );
    }

    foreach ( $this->filters as $filter ) {
      extract( $filter, EXTR_OVERWRITE );
      add_filter( $hook, array( $component, $callback ), $priority, $accepted_args );
    }
  }
}
