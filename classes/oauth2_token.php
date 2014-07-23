<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksOAuth2Token
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    16 Sep 2012
 **/

class nxcSocialNetworksOAuth2Token extends eZPersistentObject
{
	const TYPE_FACEBOOK  = 1;
	const TYPE_TWITTER   = 2;
	const TYPE_LINKEDIN  = 3;
	const TYPE_GOOGLE    = 4;
	const TYPE_INSTAGRAM = 5;

	public static function definition() {
		return array(
			'fields'              => array(
				'id' => array(
					'name'     => 'ID',
					'datatype' => 'integer',
					'default'  => 0,
					'required' => true
				),
				'type' => array(
					'name'     => 'Type',
					'datatype' => 'integer',
					'default'  => 0,
					'required' => true
				),
				'token' => array(
					'name'     => 'Token',
					'datatype' => 'string',
					'default'  => null,
					'required' => true
				),
				'secret' => array(
					'name'     => 'Secret',
					'datatype' => 'string',
					'default'  => null,
					'required' => true
				)
			),
			'function_attributes' => array(),
			'keys'                => array( 'id' ),
			'sort'                => array( 'id' => 'asc' ),
			'increment_key'       => 'id',
			'class_name'          => __CLASS__,
			'name'                => 'nxc_social_network_tokens'
		);
	}

	public static function fetch( $type ) {
		return eZPersistentObject::fetchObject(
			self::definition(),
			null,
			array( 'type' => $type ),
			true
		);
	}
}
?>
