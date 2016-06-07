<?php
/**
 * Copyright (C) 2016 Cimbura.com
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Plugin Name: WP Spatial Capabilities Check
 * Description: Check what spatial functions your version of MySQL or MariaDB has available.
 * Plugin URI: https://github.com/cimburadotcom/wp-spatial-capabilities-check
 * Author: Michael Moore
 * Author URI: http://cimbura.com
 * Version: 0.0.1
 * Text Domain: wp-spatial-capabilities-check
 * Domain Path: /lang
 * License: GPLv2
 *
 * @package wp-spatial-capabilities-check
 */

add_action( 'admin_menu', 'wpscc_admin_menu' );

/**
 * Add the management page menu listing.
 */
function wpscc_admin_menu() {
	add_management_page( esc_html__( 'Spatial Capabilites', 'wp-spatial-capabilities-check' ), esc_html__( 'Spatial Capabilites','wp-spatial-capabilities-check' ), 'install_plugins', 'spatial-capabilites', 'wpscc_show_spatial_capabilites' );
}

/**
 * Query the database and determine what spatial functions are available.
 */
function wpscc_show_spatial_capabilites() {
	global $wpdb;

	$all_funcs = array(
		'Area',
		'AsBinary',
		'AsText',
		'AsWKB',
		'AsWKT',
		'Boundary',
		'Buffer',
		'Centroid',
		'Contains',
		'ConvexHull',
		'Crosses',
		'Dimension',
		'Disjoint',
		'Distance',
		'EndPoint',
		'Envelope',
		'Equals',
		'ExteriorRing',
		'GeomCollFromText',
		'GeomCollFromWKB',
		'GeometryCollection',
		'GeometryCollectionFromText',
		'GeometryCollectionFromWKB',
		'GeometryFromText',
		'GeometryFromWKB',
		'GeometryN',
		'GeometryType',
		'GeomFromText',
		'GeomFromWKB',
		'GLength',
		'InteriorRingN',
		'Intersects',
		'IsClosed',
		'IsEmpty',
		'IsRing',
		'IsSimple',
		'LineFromText',
		'LineFromWKB',
		'LineString',
		'LineStringFromText',
		'LineStringFromWKB',
		'MBRContains',
		'MBRCoveredBy',
		'MBRDisjoint',
		'MBREqual',
		'MBREquals',
		'MBRIntersects',
		'MBROverlaps',
		'MBRTouches',
		'MBRWithin',
		'MLineFromText',
		'MLineFromWKB',
		'MPointFromText',
		'MPointFromWKB',
		'MPolyFromText',
		'MPolyFromWKB',
		'MultiLineString',
		'MultiLineStringFromText',
		'MultiLineStringFromWKB',
		'MultiPoint',
		'MultiPointFromText',
		'MultiPointFromWKB',
		'MultiPolygon',
		'MultiPolygonFromText',
		'MultiPolygonFromWKB',
		'NumGeometries',
		'NumInteriorRings',
		'NumPoints',
		'Overlaps',
		'Point',
		'PointFromText',
		'PointFromWKB',
		'PointOnSurface',
		'PointN',
		'PolyFromText',
		'PolyFromWKB',
		'Polygon',
		'PolygonFromText',
		'PolygonFromWKB',
		'SRID',
		'ST_Area',
		'ST_AsBinary',
		'ST_AsGeoJSON',
		'ST_AsText',
		'ST_AsWKB',
		'ST_AsWKT',
		'ST_Boundary',
		'ST_Buffer',
		'ST_Buffer_Strategy',
		'ST_Centroid',
		'ST_Contains',
		'ST_ConvexHull',
		'ST_Crosses',
		'ST_Difference',
		'ST_Dimension',
		'ST_Disjoint',
		'ST_Distance',
		'ST_Distance_Sphere',
		'ST_EndPoint',
		'ST_Envelope',
		'ST_Equals',
		'ST_ExteriorRing',
		'ST_GeoHash',
		'ST_GeomCollFromText',
		'ST_GeomCollFromWKB',
		'ST_GeometryCollectionFromText',
		'ST_GeometryCollectionFromWKB',
		'ST_GeometryFromText',
		'ST_GeometryFromWKB',
		'ST_GeometryN',
		'ST_GeometryType',
		'ST_GeomFromGeoJSON',
		'ST_GeomFromText',
		'ST_GeomFromWKB',
		'ST_InteriorRingN',
		'ST_Intersection',
		'ST_Intersects',
		'ST_IsClosed',
		'ST_IsEmpty',
		'ST_IsRing',
		'ST_IsSimple',
		'ST_IsValid',
		'ST_LatFromGeoHash',
		'ST_Length',
		'ST_LineFromText',
		'ST_LineFromWKB',
		'ST_LineStringFromText',
		'ST_LineStringFromWKB',
		'ST_LongFromGeoHash',
		'ST_NumGeometries',
		'ST_NumInteriorRings',
		'ST_NumPoints',
		'ST_Overlaps',
		'ST_PointFromGeoHash',
		'ST_PointFromText',
		'ST_PointFromWKB',
		'ST_PointOnSurface',
		'ST_PointN',
		'ST_PolyFromText',
		'ST_PolyFromWKB',
		'ST_PolygonFromText',
		'ST_PolygonFromWKB',
		'ST_Relate',
		'ST_Simplify',
		'ST_SRID',
		'ST_StartPoint',
		'ST_SymDifference',
		'ST_Touches',
		'ST_Union',
		'ST_Validate',
		'ST_Within',
		'ST_X',
		'ST_Y',
		'StartPoint',
		'Touches',
		'Within',
		'X',
		'Y',
	);

	// Generate a table of capabilities.
	// Suppress errors during this test.
	$suppress = $wpdb->suppress_errors( true );
	$errors = $wpdb->show_errors( false );

	$capabilites_table = '<table class="spatialcapabilites"><tr><th>' . esc_html__( 'Function Name','wp-spatial-capabilities-check' ) . '</th><th>' . esc_html__( 'Function Exists?','wp-spatial-capabilities-check' ) . '</th></tr>';

	foreach ( $all_funcs as $func ) {
		$q = "SELECT $func() AS worked";
		$wpdb->query( $q ); // @codingStandardsIgnoreLine -- PHPCS: We need to run these queries, regardless of the warnings.

		$capabilites_table .= '<tr><th>' . esc_html( $func ) . '</th>';
		if ( strpos( $wpdb->last_error,'Incorrect parameter count' ) !== false || strpos( $wpdb->last_error,'You have an error in your SQL syntax' ) !== false ) {
			$capabilites_table .= '<td class="hassupport">' . esc_html__( 'Yes','wp-spatial-capabilities-check' ) . '</td>';
		} else {
			$capabilites_table .= '<td class="lackssupport">' . esc_html__( 'No','wp-spatial-capabilities-check' ) . '</td>';
		}
		$capabilites_table .= '</tr>';
	}

	$capabilites_table .= '</table>';

	// Re-set the error settings.
	$wpdb->suppress_errors( $suppress );
	$wpdb->show_errors( $errors );

	// Generate a table of MySQL information.
	$version_info = $wpdb->get_row( 'SELECT VERSION() as v' ); // @codingStandardsIgnoreLine
	$version_info = $version_info->v;

	include( dirname( __FILE__ ) . '/spatial-layout.php' );
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wpscc_add_plugin_action_links' );

/**
 * Add a link to the plugins listing page to let admins check their database from there.
 *
 * @param array $links The links to show under the plugin name on the plugin page.
 */
function wpscc_add_plugin_action_links( $links ) {
	return array_merge(
		array(
			'checknow' => '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/tools.php?page=spatial-capabilites">' . esc_html__( 'Check Now','wp-spatial-capabilities-check' ) . '</a>',
		),
		$links
	);
}

add_action( 'plugins_loaded', 'wpscc_load_textdomain' );

/**
 * Set up the I18N path.
 */
function wpscc_load_textdomain() {
	load_plugin_textdomain( 'wp-spatial-capabilities-check', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
}
