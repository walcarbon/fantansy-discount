<?php

/**
 * Activate the plugin
 *
 * @package    Includes
 * @subpackage Includes/MS_Activator
 * @version    1.0.0
 */

namespace Includes;

defined( 'ABSPATH' ) || exit;

class VCPL_Activator
{
  // activate the plugin
  public static function activate(): void
  {
    VCPL_Session_Handler::register_rewrite_endpoints();
    flush_rewrite_rules();
  }
}
