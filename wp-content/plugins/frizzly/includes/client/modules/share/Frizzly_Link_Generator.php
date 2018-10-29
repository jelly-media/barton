<?php

class Frizzly_Link_Generator {

	/**
	 * @param $network string
	 * @param $data_provider Frizzly_Social_Data_Provider
	 *
	 * @param null $additional_data
	 *
	 * @return string
	 */
	static function generate( $network, $data_provider, $additional_data = null ) {
		if ( ! $additional_data ) {
			$additional_data = $data_provider->get_additional_data( $network );
		}
		switch ( $network ) {
			case 'digg':
				return sprintf( 'http://digg.com/submit?url=%s&title=%s',
					self::encodeURIComponent( $data_provider->get_url() ),
					self::encodeURIComponent( $data_provider->get_title( $network ) )
				);
			case 'email':
				$subject = '['.__( 'Shared Post', 'frizzly' ).'] '. $data_provider->get_title( $network );
				$subject = self::encodeURIComponent( $subject );
				$body    = __( 'You may be interested in the following post:', 'frizzly' ) . "\n\n" . $data_provider->get_url();
				$body    = self::encodeURIComponent( $body );

				return sprintf( 'mailto:?subject=%s&body=%s',
					$subject,
					$body
				);
			case 'facebook':
				return sprintf( 'http://www.facebook.com/sharer.php?u=%s',
					self::encodeURIComponent( $data_provider->get_url() )
				);
			case 'googleplus':
				return sprintf( 'https://plus.google.com/share?url=%s',
					self::encodeURIComponent( $data_provider->get_url() )
				);
			case 'linkedin':
				return sprintf( 'https://www.linkedin.com/shareArticle?mini=true&url=%s&title=%s&summary=%s',
					self::encodeURIComponent( $data_provider->get_url() ),
					self::encodeURIComponent( $data_provider->get_title( $network ) ),
					self::encodeURIComponent( $data_provider->get_description( $network ) )
				);
			case 'pinterest':
				if ( ! isset( $additional_data['image'] ) || !is_array( $additional_data['image'] ) || !isset( $additional_data['image']['url']) ) {
					return '';
				}
				$source = $additional_data[ 'source' ];
				$data = $additional_data[ 'image' ];
				$data['post_title'] = $data_provider->get_title( $network );
				$pin_description = self::find_first( $source, $data);

				return sprintf( 'http://pinterest.com/pin/create/bookmarklet/?is_video=false&url=%s&media=%s&description=%s',
					self::encodeURIComponent( $data_provider->get_url() ),
					self::encodeURIComponent( $additional_data['image']['url'] ),
					self::encodeURIComponent( $pin_description )
				);
			case 'reddit':
				return sprintf( 'https://www.reddit.com/submit?url=%s',
					self::encodeURIComponent( $data_provider->get_url() )
				);
			case 'stumbleupon':
				return sprintf( 'http://www.stumbleupon.com/submit?url=%s&title=%s',
					self::encodeURIComponent( $data_provider->get_url() ),
					self::encodeURIComponent( $data_provider->get_title( $network ) )
				);
			case 'twitter':
				$additional_data = $data_provider->get_additional_data( $network );

				return sprintf( 'https://twitter.com/share?url=%s&text=%s%s',
					self::encodeURIComponent( $data_provider->get_url() ),
					self::encodeURIComponent( $data_provider->get_title( $network ) ),
					isset( $additional_data['handle'] ) ? sprintf( '&via=%s', self::encodeURIComponent( $additional_data['handle'] ) ) : ''
				);
			default:
				return '';
		}
	}

	private static function find_first($seq, $data) {
		foreach($seq as $key) {
			if ( isset( $data[$key] ) && strlen( $data[$key] ) > 0 ) {
				return $data[$key];
			}
		}
		return '';
	}

	private static function encodeURIComponent( $str ) {
		$revert = array( '%21' => '!', '%2A' => '*', '%27' => "'", '%28' => '(', '%29' => ')' );

		return strtr( rawurlencode( $str ), $revert );
	}
}