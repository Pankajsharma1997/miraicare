<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */


// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'miraicare' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '-5E{zCMF[!dlz:=!H=2Rcd}$3S=HwFPs1Cej3 BP7V*@!Wc?whi[fG)a=EjXb[`B' );
define( 'SECURE_AUTH_KEY',  'wcXb:5j!xj<gzXIS,)?$EI-oc[)28kx973~kC,eF$jB^*bjyud42$AIG@E-E|U:T' );
define( 'LOGGED_IN_KEY',    '$~y.s ![_u>yj8wlZ<uz`=JkutO?+nl.v:/[Z):6^=YE0>YY!)A64E`>wc3yHs;C' );
define( 'NONCE_KEY',        'y)d/MfD<W!CrxGC>*S?5=y&zsCvGUoKFOK61?s(iEr0ik{OzoX<>)-I<f]LJ2i|w' );
define( 'AUTH_SALT',        '^MFg,a97A|wQP2{x(:}Pe!xL!o{Hcl0i1>X<i~fu)/A7h?AYd[FOrUOI6QiX!c?q' );
define( 'SECURE_AUTH_SALT', 'znT3S>-3oQwayd7:G|d:>AH|,x}Gs03/^4I}z-lxG~YiK]U/`GL1mHQ}?rx@275/' );
define( 'LOGGED_IN_SALT',   ')>&UWu|P#.yU1|>UXLj<EF3$}5=/CzN=K/q/;G[9@X9D%9F:2UmI`qK,MS2ghg/>' );
define( 'NONCE_SALT',       'p0Df^xAij)_!1A.4<n/s*pd61f,j+<[& }RldR{KI%$$<ro0I:4~)YRpoN5dR~d>' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
