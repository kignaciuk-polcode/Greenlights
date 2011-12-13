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
/** The name of the database for WordPress */
define('DB_NAME', 'greenlights_wp2');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'qqq');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         'Gca.,dwa{)sSv?}wzbl>GK1pOX`UTBMpRfL=JjKF;GrzJ*#2UUpv1V)YUclnC3^`');
define('SECURE_AUTH_KEY',  '6IwRcYgxKbo{FYbVO]Rxuo}wPT}m&AsW5-o|nYM;FMr--oGg1[a {o-5P)NV:0 K');
define('LOGGED_IN_KEY',    '<O/`h$(+b gn-< o`d@gsQRA.?Oz%+H#D@q^&6cST8[1.XDbRhub0[4b9-|,M3;V');
define('NONCE_KEY',        'X6l9VMa-q5^x0IrPG-0`mPDyN??k2i *esQ}pE 5C@o@M,?c{H:iX!-?ud{J`Wa-');
define('AUTH_SALT',        '|MO#2GNNx2fBfTAock62FfbIZYX!|*iC(+$aVj#yb7h]-F+EZ(2;|wy0p,w@-S[/');
define('SECURE_AUTH_SALT', '@z[A#vZrTq4Ltx(|RZ*{&R],Y5W[81xS$eZ>1%H~l!-6N]5N(HC-j+|QTl(Xw:K@');
define('LOGGED_IN_SALT',   'mT2R663pg.,h]p#*hd4qrnC0~p:bT%NxlMweIl+H1v~}+!>,q@7Cc6)qL|~EYBVW');
define('NONCE_SALT',       ':pMh)+|6|$@~e;[3$P+b[]T>`%QN0k5eAkB>G)|TIQSH9q9>UkfzM+<<+aPS~9)L');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
