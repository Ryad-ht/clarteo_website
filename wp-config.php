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
define( 'DB_NAME', 'clartez40' );

/** Database username */
define( 'DB_USER', 'clartez40' );

/** Database password */
define( 'DB_PASSWORD', 'TUhgqVreVJfp' );

/** Database hostname */
define( 'DB_HOST', 'clartez40.mysql.db:3306' );

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
define( 'AUTH_KEY',         'JJxK9qH8+0l9+owZTNdGPHErm1ufYnMH+Jtwwf3oAOv/+ZKvsiduh0PheZ20' );
define( 'SECURE_AUTH_KEY',  's/f+53QFoeHLMhY3DLMvVFDHaIQQ1/JEOGYf4c3V3H8SDAQJbm25gZGI7DwJ' );
define( 'LOGGED_IN_KEY',    '2qZudid4zRXtwaGfkeGjgwPChlb4OQOwtks2PxSScDf+y4xiKEa1NlWk+1FB' );
define( 'NONCE_KEY',        'BUp37japEItb3AFBHK7xHbIql5boFCB/rFAeQGCTxdvQjCqzN99sa0L9pOWI' );
define( 'AUTH_SALT',        'ZzdvqNJj7cxmF3LGJt5kf7UZ13xUo+Rh/2yKYhiSpuUAtpycmrGeAzSE6tDB' );
define( 'SECURE_AUTH_SALT', 'rwbPOkOh0zFdtonigpev9kizy4wyxosaJ8yFPP3jYVI8/EkP8Du0umUx2qRh' );
define( 'LOGGED_IN_SALT',   '17LFrIKjuJyRWPYdniKPbPVCeYuWmwDaVBMrddJnKKFS21iYK2WTg0SMS6nU' );
define( 'NONCE_SALT',       'K4VFohakVS2of4/mGJJmx1v9JZ/M437d01AW+hY6sgmE3K06N216RIYcwaPZ' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'mod228_';

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
