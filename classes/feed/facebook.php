<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksFeedFacebook
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    20 Sep 2012
 **/

class nxcSocialNetworksFeedFacebook extends nxcSocialNetworksFeed
{
	protected static $cacheDirectory     = 'nxc-facebook';
	protected static $debugMessagesGroup = 'NXC Social Networks Facebook feed';

	private $acessToken = null;

	public function __construct() {
		parent::__construct();

		$OAuth2 = nxcSocialNetworksOAuth2::getInstanceByType( 'facebook' );
		$this->API = new Facebook(
			$OAuth2->appSettings['key'],
			$OAuth2->appSettings['secret']
		);

		$OAuth2Token = $OAuth2->getToken();
		$this->acessToken = $OAuth2Token->attribute( 'token' );
	}

	public function getTimeline( $pageID = false, $limit = 20, $type = 'feed' ) {
		$result = array( 'result' => array() );

		$accumulator = $this->debugAccumulatorGroup . '_facebook_timeline';
		eZDebug::accumulatorStart(
			$accumulator,
			$this->debugAccumulatorGroup,
			'timeline'
		);

		$cacheFileHandler = $this->getCacheFileHandler( '_timeline', array( $pageID, $limit, $type ) );
		try{
			if( $this->isCacheExpired( $cacheFileHandler ) ) {
				eZDebug::writeDebug(
					array( 'page_id' => $pageID, 'limit' => $limit ),
					self::$debugMessagesGroup
				);

				$response = $this->API->api(
					( ( $pageID === false ) ? 'me/home' : '/' . $pageID ) . '/' . $type,
					array(
						'access_token' => $this->acessToken,
						'limit'        => $limit
					)
				);

				$messages    = array();
				$currentTime = time();
				foreach( $response['data'] as $message ) {
					$createdAt = strtotime( $message['created_time'] );

					$message['created_ago']       = self::getCreatedAgoString( $createdAt, $currentTime );
					$message['created_timestamp'] = $createdAt;
					if( isset( $message['message'] ) ) {
						$message['message'] = self::fixMessageLinks( $message['message'] );
					}
					$messages[] = $message;
				}

				$cacheFileHandler->fileStoreContents( $cacheFileHandler->filePath, serialize( $messages ) );
			} else {
				$messages = unserialize( $cacheFileHandler->fetchContents() );
			}

			eZDebug::accumulatorStop( $accumulator );
			$result['result'] = $messages;
			return $result;
		} catch( Exception $e ) {
			eZDebug::accumulatorStop( $accumulator );
			eZDebug::writeError( $e->getMessage(), self::$debugMessagesGroup );
			return $result;
		}
	}

	private static function fixMessageLinks( $message ) {
		$message = preg_replace( '|[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]|', '<a href="\\0">\\0</a>', $message );

		return $message;
	}
}
?>
