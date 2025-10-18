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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'u608786266_stretch');

/** Database username */
define('DB_USER', 'u608786266_stretch');

/** Database password */
define('DB_PASSWORD', 'CTn56KxGHazt7px');

/** Database hostname */
define('DB_HOST', 'localhost');

/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

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
define('AUTH_KEY',         'K]X7v%M=BX+cJ,${T]_.M)=Up:1N:&mq>VH{s9U0@A8):CJW7G#g3<3(R#&e%,F$');
define('SECURE_AUTH_KEY',  'TfwuwcM2)WnywP-Ux%@@)= MHVfvy:8-*952Y-N9=2drFF0:~*B-/-#ku<TlkDvT');
define('LOGGED_IN_KEY',    's6L0@LY9 O%C*tjW=)r6g[P5-wG{^*U-K9!pW8H$?.O#acv8j, a*V& OvCs)6b8');
define('NONCE_KEY',        'Egd/?Eq!}Woz,;u$]3->NSZevmzX&Ambb;/b0]ZR/>;RQ0}<y3_EslL|wI(KtLIQ');
define('AUTH_SALT',        'a6X) TJ)$$Oc]UO5eiQ0,R0D^7]o^e_EDuZoay0EEc}(_XR9.m*6Jw:lpC&I-iLu');
define('SECURE_AUTH_SALT', 'NGH#j5~Y:EzAUi401BlA-riL#F~U(A!b65w9|Dn-G/9q~%-ZdlGX~C3ecwBw9-J]');
define('LOGGED_IN_SALT',   'f3UfR<Gvo6a0YQl26V`G[Z!&pana=J(U.y9R=rBdCwO`|3T:Yx5GcVMpye)w6@(<');
define('NONCE_SALT',       'Ewor6=sed@G,>Su.&xlBd}%e*B|nugqf5Docc`G#_c<g!c=B3zln4yy$m=w>vHoI');

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
define('WP_DEBUG', false);

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/* Multisite */
define('WP_ALLOW_MULTISITE', true);

define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', 'banskostretching.com');
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);

/** Absolute path to the WordPress directory. */
if (! defined('ABSPATH')) {
  define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
