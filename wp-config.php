<?php
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
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'Wordpress' );

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
define( 'AUTH_KEY',         'X<BA!#QDU;()FVMiZ~hBSI{UxPEN)hK7c2)|cztdLbUq2.wOsjf<Ucv|bImRO2V?' );
define( 'SECURE_AUTH_KEY',  '23x#$DgkKCp,qC#8.bhDnR~LkX`I:B$U1i3Xl9Jz}2M_# HvOSf[Q[e<Kd~{W9%X' );
define( 'LOGGED_IN_KEY',    '0z<F;vVVLryEbv]iz@z%:W!olU$mdKhl{`em0,x;%/-8LxA p4)_>Cz}i:[([FW*' );
define( 'NONCE_KEY',        'MV&fvn`{ekKUN#5~1fY[$ac2`,5(0g0j1<F}i8a7?izPz*G1>8#pG%rKo&tlc@Lu' );
define( 'AUTH_SALT',        'e4>WRL3*ujZ{i0aI$G2>l0mvf2/Kg}v0Wg_0Y)J:|2oqT~%]2Os~ IDgu,q9w+5M' );
define( 'SECURE_AUTH_SALT', '%hu/Z9U/<[+x63WF~~2EM)Jgbq3cB0:&AjTmx&6--VN<n=[$)T6gXua)S#*M1PRS' );
define( 'LOGGED_IN_SALT',   'FL{NH>6.`aHA6P5^YOu!6-^?<jLyE9/y`lrAAG5gUUZE9@<zRxbB>tnKJ1&Q1:3r' );
define( 'NONCE_SALT',       'ygldzd~N$jI?mFRFu5j1w``-*{bc1K`t#1x0CePc5Cjp_h[j(1N)bS8a<^Bw`N!r' );

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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
