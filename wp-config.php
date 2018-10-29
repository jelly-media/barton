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
define('DB_NAME', 'bartonwordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1:8889');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define( 'WP_ALLOW_REPAIR', true );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'AKq59E[XO:{0l!G.sKb`/{v{;[c5C8D<V8|C_9KpAyhzsotpq&42V%NfvgFs[#8n');
define('SECURE_AUTH_KEY',  'V!w@*tEyLZPFy.{N3(6]JvJ,{E|) ?I7f<P3S`}pJ!)qsR>8*i7:DT<byp2QQUkT');
define('LOGGED_IN_KEY',    'qD3?jUmFD=qEBZW-A#9qWz<^C9@v1zecsS#Y4zRP_fh5`/6R3@m DL,h&Dj=8fuS');
define('NONCE_KEY',        '@/<n0?>oyc>j~YCm{]m8d[zR77?sx:5IQU!H@y9UkQ0:eTuXhdUJ!o&&^6&H0`L6');
define('AUTH_SALT',        'vVZAcHn$<ZOG*b`8r=~(]un(b`G6X~F/jh&c}$c@kA1d_zC(>.z27e_~PC=y(J5]');
define('SECURE_AUTH_SALT', 'vh;TfjdrHm)*l!!#6w=<(g9-)Y7hyEL+0},WFAQ!m&1n%Z)PBZhJq:wb9EH]*V^h');
define('LOGGED_IN_SALT',   'Z3Gzb4Q;/G<92xTW ;[HNePXY98acT}8?.c<)BnA<%@p[mL!.Tbk~/W#7BiQSum1');
define('NONCE_SALT',       '+Y+%N5yVkB:ZH:!N<$W]scSkKOi0t~Mlp#U$<7%X7X&_6;PoVywY75JtRY/7A}SE');

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
