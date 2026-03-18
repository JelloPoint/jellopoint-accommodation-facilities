<?php
namespace JelloPoint\AccommodationFacilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jpaf_get_icon_html( array $facility ) : string {
	$icon_type  = isset( $facility['icon_type'] ) ? sanitize_key( $facility['icon_type'] ) : 'dashicon';
	$icon_value = isset( $facility['icon_value'] ) ? (string) $facility['icon_value'] : '';

	if ( 'svg' === $icon_type ) {
		$mask_image = jpaf_get_svg_mask_image( $facility );
		if ( '' !== $mask_image ) {
			$style = sprintf(
				'-webkit-mask-image:url("%1$s");mask-image:url("%1$s");',
				$mask_image
			);
			return '<span class="jpaf-facility__icon-inner jpaf-facility__icon-inner--svg-mask" style=' . "'" . esc_attr( $style ) . "'" . ' aria-hidden="true"></span>';
		}

		$svg_url = jpaf_get_svg_icon_url( $facility );
		if ( '' !== $svg_url ) {
			$style = sprintf(
				'-webkit-mask-image:url("%1$s");mask-image:url("%1$s");',
				esc_url_raw( $svg_url )
			);
			return '<span class="jpaf-facility__icon-inner jpaf-facility__icon-inner--svg-mask" style=' . "'" . esc_attr( $style ) . "'" . ' aria-hidden="true"></span>';
		}
	}

	if ( 'custom_class' === $icon_type && '' !== $icon_value ) {
		return '<span class="jpaf-facility__icon-inner" aria-hidden="true"><i class="' . esc_attr( sanitize_text_field( $icon_value ) ) . '"></i></span>';
	}

	$icon_value = sanitize_text_field( $icon_value );
	if ( '' === $icon_value || 0 !== strpos( $icon_value, 'dashicons-' ) ) {
		$icon_value = 'dashicons-admin-site';
	}

	return '<span class="jpaf-facility__icon-inner dashicons ' . esc_attr( $icon_value ) . '" aria-hidden="true"></span>';
}

function jpaf_get_svg_icon_url( array $facility ) : string {
	$attachment_id = isset( $facility['icon_attachment_id'] ) ? (int) $facility['icon_attachment_id'] : 0;

	if ( $attachment_id > 0 ) {
		$url = wp_get_attachment_url( $attachment_id );
		if ( $url ) {
			return (string) $url;
		}
	}

	$icon_value = isset( $facility['icon_value'] ) ? trim( (string) $facility['icon_value'] ) : '';
	if ( '' === $icon_value ) {
		return '';
	}

	return $icon_value;
}

function jpaf_get_svg_icon_path( array $facility ) : string {
	$attachment_id = isset( $facility['icon_attachment_id'] ) ? (int) $facility['icon_attachment_id'] : 0;

	if ( $attachment_id > 0 ) {
		$path = get_attached_file( $attachment_id );
		if ( $path && file_exists( $path ) ) {
			return (string) $path;
		}
	}

	$url = jpaf_get_svg_icon_url( $facility );
	if ( '' === $url ) {
		return '';
	}

	$uploads = wp_get_upload_dir();
	if ( empty( $uploads['basedir'] ) || empty( $uploads['baseurl'] ) ) {
		return '';
	}

	if ( 0 !== strpos( $url, $uploads['baseurl'] ) ) {
		return '';
	}

	$relative = wp_parse_url( $url, PHP_URL_PATH );
	$base     = wp_parse_url( $uploads['baseurl'], PHP_URL_PATH );

	if ( ! is_string( $relative ) || ! is_string( $base ) || 0 !== strpos( $relative, $base ) ) {
		return '';
	}

	$candidate = wp_normalize_path( trailingslashit( $uploads['basedir'] ) . ltrim( substr( $relative, strlen( $base ) ), '/' ) );
	if ( file_exists( $candidate ) ) {
		return $candidate;
	}

	return '';
}

function jpaf_get_svg_mask_image( array $facility ) : string {
	$path = jpaf_get_svg_icon_path( $facility );
	if ( '' === $path ) {
		return '';
	}

	$svg = @file_get_contents( $path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged,WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( ! is_string( $svg ) || '' === trim( $svg ) ) {
		return '';
	}

	$normalized = jpaf_prepare_svg_for_mask( $svg );
	if ( '' === $normalized ) {
		return '';
	}

	return 'data:image/svg+xml;utf8,' . rawurlencode( $normalized );
}

function jpaf_prepare_svg_for_mask( string $svg ) : string {
	$svg = trim( $svg );
	if ( '' === $svg || false === stripos( $svg, '<svg' ) ) {
		return '';
	}

	$svg = preg_replace( '/<\?xml.*?\?>/is', '', $svg );
	$svg = preg_replace( '/<!DOCTYPE.*?>/is', '', $svg );
	$svg = preg_replace( '#<script\b[^>]*>.*?</script>#is', '', $svg );
	$svg = preg_replace( '/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\')/i', '', $svg );

	$pattern = '/<(path|rect|circle|ellipse|polygon|polyline|line)\b[^>]*\/?>/i';
	$svg     = preg_replace_callback( $pattern, __NAMESPACE__ . '\\jpaf_normalize_svg_shape_tag', $svg );

	return $svg;
}

function jpaf_normalize_svg_shape_tag( array $matches ) : string {
	$tag = $matches[0];

	$has_stroke = jpaf_tag_has_paint( $tag, 'stroke' );
	$has_fill   = jpaf_tag_has_paint( $tag, 'fill' );

	$tag = jpaf_remove_svg_attr( $tag, 'fill' );
	$tag = jpaf_remove_svg_attr( $tag, 'stroke' );
	$tag = jpaf_cleanup_svg_style_attr( $tag );

	if ( $has_stroke ) {
		$tag = jpaf_insert_svg_attrs( $tag, [
			'fill'   => 'none',
			'stroke' => '#000',
		] );
	} elseif ( $has_fill ) {
		$tag = jpaf_insert_svg_attrs( $tag, [
			'fill' => '#000',
		] );
	}

	return $tag;
}

function jpaf_tag_has_paint( string $tag, string $property ) : bool {
	if ( preg_match( '/\b' . preg_quote( $property, '/' ) . '\s*=\s*("|\')(.*?)\1/i', $tag, $attr_match ) ) {
		$value = strtolower( trim( $attr_match[2] ) );
		if ( '' !== $value && 'none' !== $value && 'transparent' !== $value ) {
			return true;
		}
	}

	if ( preg_match( '/\bstyle\s*=\s*("|\')(.*?)\1/i', $tag, $style_match ) ) {
		$style = strtolower( $style_match[2] );
		if ( preg_match( '/(?:^|;)\s*' . preg_quote( $property, '/' ) . '\s*:\s*([^;]+)/i', $style, $paint_match ) ) {
			$value = trim( $paint_match[1] );
			if ( '' !== $value && 'none' !== $value && 'transparent' !== $value ) {
				return true;
			}
		}
	}

	return false;
}

function jpaf_remove_svg_attr( string $tag, string $attr ) : string {
	return preg_replace( '/\s+' . preg_quote( $attr, '/' ) . '\s*=\s*("[^"]*"|\'[^\']*\')/i', '', $tag );
}

function jpaf_cleanup_svg_style_attr( string $tag ) : string {
	return preg_replace_callback(
		'/\sstyle\s*=\s*("|\')(.*?)\1/i',
		static function ( array $matches ) : string {
			$quote        = $matches[1];
			$declarations = array_filter( array_map( 'trim', explode( ';', $matches[2] ) ) );
			$kept         = [];

			foreach ( $declarations as $declaration ) {
				if ( false === strpos( $declaration, ':' ) ) {
					continue;
				}

				list( $property, $value ) = array_map( 'trim', explode( ':', $declaration, 2 ) );
				$property = strtolower( $property );
				if ( in_array( $property, [ 'fill', 'stroke', 'color' ], true ) ) {
					continue;
				}
				$kept[] = $property . ':' . $value;
			}

			if ( empty( $kept ) ) {
				return '';
			}

			return ' style=' . $quote . implode( ';', $kept ) . ';' . $quote;
		},
		$tag
	);
}

function jpaf_insert_svg_attrs( string $tag, array $attrs ) : string {
	$insert = '';
	foreach ( $attrs as $name => $value ) {
		$insert .= ' ' . $name . '="' . htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ) . '"';
	}

	if ( preg_match( '/\/\s*>$/', $tag ) ) {
		return preg_replace( '/\/\s*>$/', $insert . ' />', $tag, 1 );
	}

	return preg_replace( '/>$/', $insert . '>', $tag, 1 );
}
