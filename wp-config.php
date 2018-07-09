<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', getenv('DATABASE_NAME'));

/** MySQL database username */
define('DB_USER', getenv('DATABASE_USERNAME'));

/** MySQL database password */
define('DB_PASSWORD', getenv('DATABASE_PASSWORD'));

/** MySQL hostname */
define('DB_HOST', getenv('DATABASE_HOST'));

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'jo=m_RF8#YcUqa{eLssum&u-1M(}0Kpg(^0ML%t6*3Jl1)s:UqB1O[tJTC9zSiKC');
define('SECURE_AUTH_KEY',  'H$u-;:Q5/.5sTj*-(L&0&X1u)WIz||te{UKD;]$Ol>+S]R.>So_CPMk~?Ej_U(-a');
define('LOGGED_IN_KEY',    'nlhnn| kx_zi8 8INWZExw U$OWgMdNj5MY;BL4.xuO9w2zzXw?&Sy|rc+~Z]*+6');
define('NONCE_KEY',        'hE,t&.3eh~(d^Tz,E :^6iXzD D(JX{>/ajhOd89LM{MG%}Mb.$2v]b$ql]p}XJz');
define('AUTH_SALT',        '?oM@3`):uktc1#k|DH#wI6 1~ZJW9SF!{@O,@:KZELv*7|x`1.#Y1-9&sE!$7L+T');
define('SECURE_AUTH_SALT', 'r#elW!#/wmk|KwOXO#Ym-h%)he36NksIF9H-k(Tr+j2->{-hl]:~j1#+!EXvr|14');
define('LOGGED_IN_SALT',   '={lC^xG2nb.^M~wM%2 Fpr{TyY+bI!j,8V6+3RupFWUsp(qe!o%M5<@HIa7`6MZL');
define('NONCE_SALT',       ':<l_-Y+>fKPijJIe+NzK=!|,2%Zw++NzgwCNT=!hzKBv3!{lx)WABmeO/2`CkokO');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

define( 'WP_MEMORY_LIMIT', '256M' );

define( 'AS3CF_AWS_ACCESS_KEY_ID', getenv('S3_ACCESS_KEY'));
define( 'AS3CF_AWS_SECRET_ACCESS_KEY', getenv('S3_SECRET_KEY'));

/** SSL */  
define('FORCE_SSL_ADMIN', true);  
define('WP_HOME','https://'. $_SERVER['SERVER_NAME']);
define('WP_SITEURL','https://'. $_SERVER['SERVER_NAME']);
// in some setups HTTP_X_FORWARDED_PROTO might contain  
// a comma-separated list e.g. http,https  
// so check for https existence  
if (strpos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false)  
    $_SERVER['HTTPS']='on';

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

//Always have Amazon S3 Plugin Active
$plugins = get_option('active_plugins');
$s3 = 'amazon-s3-and-cloudfront/wordpress-s3.php';
if (!in_array($s3, $plugins)){
	array_push($plugins, $s3);
	update_option('active_plugins',$plugins);
}