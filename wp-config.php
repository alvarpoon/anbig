<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
switch ($_SERVER['SERVER_NAME']) {

	case "anbig.local.com":
		define('DB_NAME', 'anbig');
		define('WP_SITEURL',  'http://anbig.local.com' );
    	define('WP_HOME', 'http://anbig.local.com' );
		define('DB_USER', 'root');
		define('DB_PASSWORD', 'root');
		define('DB_HOST', 'localhost');

	case "anbig.nowwhat.hk":
		define('DB_NAME', 'nowwhat_anbig');
		define('WP_SITEURL',  'http://anbig.nowwhat.hk' );
    	define('WP_HOME', 'http://anbig.nowwhat.hk' );
		define('DB_USER', 'nowwhat');
		define('DB_PASSWORD', '20273214');
		define('DB_HOST', 'localhost');

	case "www.anbig.org":
		define('DB_NAME', 'anbig_wp');
		define('WP_SITEURL',  'http://www.anbig.org' );
    	define('WP_HOME', 'http://www.anbig.org' );
		define('DB_USER', 'anbig_wp');
		define('DB_PASSWORD', 'P@ssw0rd');
		define('DB_HOST', 'localhost');
}


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
define('AUTH_KEY',         'adtA0H2+w5<=74b7fAR|pI0Wl)zR,/OP|y_ihq+|,I,iXce-7W=S`<4O+CzaYYEd');
define('SECURE_AUTH_KEY',  '!B @:78a+rZperf|affE@3+YNIl[t(/%o:Q^<k0rM2OPJD,vt[BpM)C@D3btPKCW');
define('LOGGED_IN_KEY',    'e2duHLq~W@/d;]*no? .]%.Qq;h+bv l~nB5pQv`h@bu(l[)pN@=s+bX(b?WHqZF');
define('NONCE_KEY',        '%_kcTY/wt<H-bNJ5KYx#K2($X+%-6dF?a[bbxIeGBL %K.?-[9G$V&c n9~IC,;5');
define('AUTH_SALT',        '5_;IkZnWa:HxVQM81(m]8}k$@<[Kd}/I $P/y-[d!Z[ULHYFn. L=-S$,=}GW|0=');
define('SECURE_AUTH_SALT', 'z^>hPnH/z$)19rk6y2:6$9eXE}VPirmx<d`U:%yug@/#gpw|XZ[d,Q]J|{DkG+z1');
define('LOGGED_IN_SALT',   'p[6iv/q#hN~d!.x1ma5PUOJI$9%&jV$p<c,ePFu`)bE:}MNc.CP+mY-;o0oi!ou|');
define('NONCE_SALT',       'Jo~uODk-?SjLyDg7xcO5)5:#Oppk+}aqhL|D!CB&VUiRh#R0bjG8U|c-ARVN{:&C');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'anbig_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

define('WP_ENV', 'development');

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
