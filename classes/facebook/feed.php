<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcFacebookFeed
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    17 Aug 2012
 **/

class nxcFacebookFeed
{
	private $facebookIni;
	private $tokenIni;
	private $facebook;
	private $cacheSettings;

	public function __construct() {
		$this->facebookIni = eZINI::instance( 'nxcfacebook.ini' );
		$this->tokenIni    = eZINI::instance( 'nxcfacebookaccesstoken.ini' );

		$this->facebook = new Facebook(
			array(
				'appId'  => $this->facebookIni->variable( 'FacebookAPI', 'AppID' ),
				'secret' => $this->facebookIni->variable( 'FacebookAPI', 'Secret' )
			)
		);

		$this->cacheSettings = array(
			'path' => eZSys::cacheDirectory() . '/nxc-facebook/',
			'ttl'  => 30
		);
	}

	public function getHomeTimeline( $pageID = false, $limit = 20 ) {
		$params = array( $pageID, $limit );
		$cacheFileHandler = eZClusterFileHandler::instance(
			$this->cacheSettings['path'] . md5( serialize( $params ) ) . '_home_timeline.php'
		);

		try{
			if(
				$cacheFileHandler->fileExists( $cacheFileHandler->filePath ) === false ||
				time() > ( $cacheFileHandler->mtime() + $this->cacheSettings['ttl'] )
			) {
				$response = $this->facebook->api(
					( $pageID === false ) ? 'me/home' : '/' . $pageID . '/feed',
					array(
						'access_token' => $this->tokenIni->variable( 'AccessToken', 'Token' ),
						'limit'        => $limit
					)
				);

				$messages     = array();
				$current_time = time();
				foreach( $response['data'] as $message ) {
					$created_at   = strtotime( $message['created_time'] );
					$created_diff = $current_time - $created_at;
					if( $created_diff < 60 ) {
						$created_ago = ezi18n(
							'extension/nxc_social_networks', '%secons seconds ago', null, array( '%secons' => ceil( $created_diff ) )
						);
					} elseif( $created_diff < 60 * 60 ) {
						$created_ago = ezpI18n::tr(
							'extension/nxc_social_networks', '%minutes minutes ago', null, array( '%minutes' => floor( $created_diff / 60 ) )
						);
					} elseif( $created_diff < 60 * 60 * 24 ) {
						$created_ago = ezpI18n::tr(
							'extension/nxc_social_networks', 'About %hours hours ago', null, array( '%hours' => floor( $created_diff / ( 60 * 60 ) ) )
						);
					} elseif( $created_diff < 60 * 60 * 24 * 7 ) {
						$created_ago = ezpI18n::tr(
							'extension/nxc_social_networks', 'About %days days ago', null, array( '%days' => floor( $created_diff / ( 60 * 60 * 24 ) ) )
						);
					} else {
						$created_ago = ezpI18n::tr(
							'extension/nxc_social_networks', 'About %weeks weeks ago', null, array( '%weeks' => floor( $created_diff / ( 60 * 60 * 24 * 7 ) ) )
						);
					}

					$message['created_ago']       = $created_ago;
					$message['created_timestamp'] = $created_at;
					$messages[] = $message;
				}

				$cacheFileHandler->fileStoreContents( $cacheFileHandler->filePath, serialize( $messages ) );
			} else {
				$messages = unserialize( $cacheFileHandler->fetchContents() );
			}

			return $messages;
		} catch( Exception $e ) {
			eZDebug::writeError( $e, 'NXC Facebook feed' );
			return false;
		}
	}
}
?>
