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
define('DB_NAME', 'resturant_db123');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'm371D:AbB=II9g*|+#sN=o`$H.{27DMI/7_9CiLkViJ/8?DF_TG$>zlK]YhvLJ`?');
define('SECURE_AUTH_KEY',  'jB}Z9Z9IFyHcoe[7: n WyfnE6E(h*>%8cIhuq3h|)0w_vKIUdrI&tS,Q7/+z/s0');
define('LOGGED_IN_KEY',    '8~eI@$L?[dFDiI~}bz[1 i{=7<H{Odim:XdaxYMAC4lkN)W|6A{gU U_)mSg=lB~');
define('NONCE_KEY',        '=%vVG?,2&g WxPBK{h>)Exg !:sP;@,Kl-[r~]~.o9+<CA8~_5-sS1*3F7CyRORB');
define('AUTH_SALT',        '2eZTYZ3qTG,eC:.mmGc#30pk{XC`c:`w4RW>6LnWaGhLbtK6~1F;2m0aTQ1Rlo.j');
define('SECURE_AUTH_SALT', 'OVf;izk7aK<hnJ;4(&sMXD;fUkCK^8lLq-un?#PlE/)M{0Aojm9Yd6I8c&|BhGNR');
define('LOGGED_IN_SALT',   ')S;> Df@f0VMeb)@{dvEMv<T=?iZ;;[[9Y8F]f_/{!v^:U89&H/MEx^K|W[-uT&o');
define('NONCE_SALT',       'Y2T]D2Eyo&wYmFad}Ji&r%q=r#4y4;?LY1lB=7@}6$s}7/wgAS`UJ.1.BpEy9&3w');

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

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
