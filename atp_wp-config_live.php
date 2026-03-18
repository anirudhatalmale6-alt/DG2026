<?php
define( 'WP_CACHE', true );

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u911260017_LgxxQ' );

/** Database username */
define( 'DB_USER', 'u911260017_diW5j' );

/** Database password */
define( 'DB_PASSWORD', 'YZRu2uKSZF' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'b)A1?i5XtVidDc^a?9O5fVjCpo3@P}`!(-O|ETUT)XtZEYm-7!Zgw+83b5KUqCb7' );
define( 'SECURE_AUTH_KEY',   'w9rn@Yctwe#aVIzAkl8!?*H1/lVR){ b2]q{%yq5z}L=2  ]eDv+i($]:=r2Fk){' );
define( 'LOGGED_IN_KEY',     ']T;<=]3L$V7#WuVLh8DcnjGoF bS!0/^nxl!?`%D:,R1>=;@u%2?:%4ruLc0LE:o' );
define( 'NONCE_KEY',         '`^BQl*M3hA!~0+]w3n|`(U+ mM]F=uic<HaJkT3eY968LuKf*S-k/=_g%0<AZKa*' );
define( 'AUTH_SALT',         'I+FBAqJnkt2P,yp_/;@i0l:d=%K7%A[LOQoDFIOK,]`q^HcbJmMBe|XCp:tpD_$K' );
define( 'SECURE_AUTH_SALT',  '1O(JU$>J;r]<tg31P14&`gM+xwM9xt6Xyo0C|07U2Y6jBlENR!<3i,p{te9yDd95' );
define( 'LOGGED_IN_SALT',    '`;eOanwyl=8Q^r*EDJgSWGN84ZmK<S.0_MMD6HNq9M1|BW}HwJjwL:Cv)d2+DhSN' );
define( 'NONCE_SALT',        '3;cqw19.9:+y7fJ0 !0l0.Wc;|L_vv4[%jm&N*IKOC]TzlMn;e+N#pQR1-pf5Ad5' );
define( 'WP_CACHE_KEY_SALT', '8~M4pU#X[%f ~i($1t:#uNMz%o;ey!QsGaZ4MH%JfwSe+Z5rkBEBxm3FO:7]o8ny' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */

/** Increase memory limits for theme demo import */
define( 'WP_MEMORY_LIMIT', '512M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );



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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', true );
}
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );

define( 'FS_METHOD', 'direct' );
define( 'COOKIEHASH', '8ac56c2ec99e271bcdc5d3e52411f78d' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
