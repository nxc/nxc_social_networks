<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcFacebookPublishType
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    29 Nov 2010
 **/

class nxcFacebookPublishType extends eZWorkflowEventType {

	const TYPE_ID = 'nxcfacebookpublish';

	public static $eventParams = array(
		'classAttributes'     => 'data_text1',
		'targetID'            => 'data_text2',
		'publishOnlyOnCreate' => 'data_int1',
		'includeURL'          => 'data_int2'
	);

	public function __construct() {
		$this->eZWorkflowEventType( self::TYPE_ID, 'Publishes message to Facebook`s wall' );

		$this->Attributes['current_class_id'] = 0;
	}

	public function execute( $process, $event ) {
		eZDebug::createAccumulatorGroup( 'nxc_facebook', 'NXC Facebook' );

		$processParams = $process->attribute( 'parameter_list' );

		$object  = eZContentObject::fetch( $processParams['object_id'] );
		$dataMap = $object->attribute( 'data_map' );

		$facebookIni = eZINI::instance( 'nxcfacebook.ini' );
		$tokenIni    = eZINI::instance( 'nxcfacebookaccesstoken.ini' );
		$facebook = new Facebook(
			array(
				'appId'  => $facebookIni->variable( 'FacebookAPI', 'AppID' ),
				'secret' => $facebookIni->variable( 'FacebookAPI', 'Secret' )
			)
		);
		$targetID = $event->attribute( self::$eventParams['targetID'] );
		if( empty( $targetID ) ) {
			$targetID = 'me';
		}

		if( (bool) $event->attribute( self::$eventParams['publishOnlyOnCreate'] ) && $object->attribute( 'current_version' ) != 1 ) {
			return eZWorkflowType::STATUS_ACCEPTED;
		}

		$selectedClassAttributes = explode( ',', $event->attribute( self::$eventParams['classAttributes'] ) );
		foreach( $dataMap as $objectAttribute ) {
			if( in_array( $objectAttribute->attribute( 'contentclassattribute_id' ), $selectedClassAttributes ) ) {
				eZDebug::accumulatorStart( 'nxc_facebook_publish', 'nxc_facebook', 'Publishing message to Facebook' );

				$content = $objectAttribute->attribute( 'content' );
				$message = ( is_string( $content ) && !empty( $content ) ) ? $content : false;

				if( $message !== false ) {
					if( (bool) $event->attribute( self::$eventParams['includeURL'] ) ) {
						$url = $object->attribute( 'main_node' )->attribute( 'url_alias' );
						eZURI::transformURI( $url, true, 'full' );
						$message .= ' ' . $url;
					}

					try{
						$facebook->api(
							'/' . $targetID . '/feed',
							'post',
							array(
								'access_token' => $tokenIni->variable( 'AccessToken', 'Token' ),
								'message'      => $message
							)
						);
					} catch( Exception $e ) {
						eZDebug::writeError( $e, 'NXC Facebook publish' );
					}
				}

				eZDebug::accumulatorStop( 'nxc_facebook_publish' );
			}
		}

		return eZWorkflowType::STATUS_ACCEPTED;
	}

	public function attributes() {
		return array_merge(
			array(
				'contentclass_list',
				'contentclass_attribute_list',
				'current_class_id'
			),
			eZWorkflowEventType::attributes()
		);
	}

	public function hasAttribute( $attr ) {
		return in_array( $attr, $this->attributes() );
	}

	public function attribute( $attr ) {
		switch( $attr ) {
			case 'contentclass_list': {
				return eZContentClass::fetchList( eZContentClass::VERSION_STATUS_DEFINED, true );
			}
			case 'contentclass_attribute_list': {
				$classList = $this->attribute( 'contentclass_list' );
				if( $this->attribute( 'current_class_id' ) !== 0 ) {
					$classID = $this->attribute( 'current_class_id' );
				} else {
					$classID = isset( $classList[0] ) ? $classList[0]->attribute( 'id' ) : false;
				}

				return is_numeric( $classID ) ? eZContentClassAttribute::fetchListByClassID( $classID ) : array();
			}
			default:
				return eZWorkflowEventType::attribute( $attr );
		}
	}

	public function customWorkflowEventHTTPAction( $http, $action, $event ) {
		$selectedClassAttributes = explode( ',', $event->attribute( self::$eventParams['classAttributes'] ) );
		if( $selectedClassAttributes[0] == '' ) {
			unset( $selectedClassAttributes[0] );
		}

		switch( $action ) {
			case 'load_class_attribute_list': {
				$var = 'WorkflowEvent_event_nxcfacebookpublish_class_' . $event->attribute( 'id' );
				if( $http->hasPostVariable( $var ) ) {
					$this->setAttribute( 'current_class_id', $http->postVariable( $var ) );
				} else {
					eZDebug::writeError( 'No class selected' );
				}
				break;
			}
			case 'new_class_attribute': {
				$var = 'WorkflowEvent_event_nxcfacebookpublish_class_attribute_' . $event->attribute( 'id' );
				if( $http->hasPostVariable( $var ) ) {
					$selectedClassAttributes[] = (int) $http->postVariable( $var );
					$selectedClassAttributes   = array_unique( $selectedClassAttributes );
					$event->setAttribute( self::$eventParams['classAttributes'], implode( ',', $selectedClassAttributes ) );
				} else {
					eZDebug::writeError( 'No class attribute selected' );
				}
				break;
			}
			case 'remove_class_attribute': {
				$var = 'WorkflowEvent_event_nxcfacebookpublish_remove_class_attribute_' . $event->attribute( 'id' );
				if( $http->hasPostVariable( $var ) ) {
					$removeAttributeIDs = (array) $http->postVariable( $var );
					foreach( $selectedClassAttributes as $index => $classAttributeID ) {
						if( in_array( $classAttributeID, $removeAttributeIDs ) ) {
							unset( $selectedClassAttributes[ $index ] );
						}
					}
					$event->setAttribute( self::$eventParams['classAttributes'], implode( ',', $selectedClassAttributes ) );
				} else {
					eZDebug::writeError( 'No class attributes selected' );
				}
				break;
			}
			default:
				eZDebug::writeError( 'Unknown custom HTTP action: ' . $action );
		}
	}

	public function fetchHTTPInput( $http, $base, $event ) {
		if( $http->hasPostVariable( 'StoreButton' ) ) {
			$targetID = $base . '_data_nxcfacebookpublish_target_id_' . $event->attribute( 'id' );
			if( $http->hasPostVariable( $targetID ) ) {
				$event->setAttribute( self::$eventParams['targetID'], $http->postVariable( $targetID ) );
			}

			$publishOnlyOnCreate = $base . '_data_nxcfacebookpublish_publish_only_on_create_' . $event->attribute( 'id' );
			$event->setAttribute( self::$eventParams['publishOnlyOnCreate'], (int) $http->hasPostVariable( $publishOnlyOnCreate ) );

			$includeURL = $base . '_data_nxcfacebookpublish_include_url_' . $event->attribute( 'id' );
			$event->setAttribute( self::$eventParams['includeURL'], (int) $http->hasPostVariable( $includeURL ) );
		}
	}
}

eZWorkflowEventType::registerEventType( nxcFacebookPublishType::TYPE_ID, 'nxcFacebookPublishType' );
?>