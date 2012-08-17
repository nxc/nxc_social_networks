<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcFacebookFeedOperations
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    17 Aug 2012
 **/

class nxcFacebookFeedOperations
{
	public function __construct() {}

	public static function getHomeTimeline( $pageID = false, $limit = 20 ) {
		$nxcFacebookFeed = new nxcFacebookFeed();
		return array( 'result' => $nxcFacebookFeed->getHomeTimeline( $pageID, $limit ) );
	}
}
?>
