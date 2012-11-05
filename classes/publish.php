<?php
/**
 * @package nxcSocialNetworks
 * @class   nxcSocialNetworksPublishHanlder
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    21 Sep 2012
 **/

class nxcSocialNetworksPublishHanlder extends eZPersistentObject
{
	protected $name = null;

	public static function definition() {
		return array(
			'fields'              => array(
				'id' => array(
					'name'     => 'ID',
					'datatype' => 'integer',
					'default'  => 0,
					'required' => true
				),
				'workflow_event_id' => array(
					'name'     => 'WorkflowEventID',
					'datatype' => 'integer',
					'default'  => 0,
					'required' => true
				),
				'type' => array(
					'name'     => 'Type',
					'datatype' => 'string',
					'default'  => 0,
					'required' => true
				),
				'classattribute_ids_serialized' => array(
					'name'     => 'ClassAttributeIDsSerialized',
					'datatype' => 'string',
					'default'  => null,
					'required' => true
				),
				'options_serialized' => array(
					'name'     => 'OptionsSerialized',
					'datatype' => 'string',
					'default'  => null,
					'required' => true
				)
			),
			'function_attributes' => array(
				'name'                         => 'getName',
				'classattribute_ids'           => 'getClassAttributeIDs',
				'options'                      => 'getOptions',
				'has_extra_options'            => 'hasExtraOptions',
				'extra_options_view_template'  => 'getExtraOptionsViewTemplate',
				'extra_options_edit_template'  => 'getExtraOptionsEditTemplate'
			),
			'keys'                => array( 'id' ),
			'sort'                => array( 'id' => 'asc' ),
			'increment_key'       => 'id',
			'class_name'          => __CLASS__,
			'name'                => 'nxc_social_network_publish_handlers'
		);
	}

	public function getName() {
		return ezpI18n::tr( 'extension/nxc_social_networks', $this->name );
	}

	public static function getTypes() {
		$ini = eZINI::instance( 'nxcsocialnetworks.ini' );
		return (array) $ini->variable( 'General', 'PublishHandlers' );
	}

	public static function newInstance( $type ) {
		$types = self::getTypes();

		if( isset( $types[ $type ] ) === false ) {
			throw new Exception( '"' . $type . '" is not supported type. Please check nxcsocialnetworks.ini' );
		}

		return new $types[ $type ](
			array(
				'type'                          => $type,
				'classattribute_ids_serialized' => null,
				'options_serialized'            => null
			)
		);
	}

	public static function fetch( $id ) {
		return eZPersistentObject::fetchObject(
			self::definition(),
			null,
			array( 'id' => $id ),
			true
		);
	}

	public static function fetchList( $eventID = null ) {
		$conditions = array();
		if( $eventID !== null ) {
			$conditions['workflow_event_id'] = $eventID;
		}

		$types  = self::getTypes();
		$return = array();
		$result = eZPersistentObject::fetchObjectList(
			self::definition(),
			null,
			$conditions,
			null,
			null,
			false
		);
		foreach( $result as $item ) {
			if( isset( $types[ $item['type'] ] ) === false ) {
				continue;
			}

			$return[] = new $types[ $item['type'] ]( $item );
		}

		return $return;
	}

	public function getClassAttributeIDs() {
		if( $this->attribute( 'classattribute_ids_serialized' ) === null ) {
			return array();
		}

		return unserialize( $this->attribute( 'classattribute_ids_serialized' ) );
	}

	public function getOptions() {
		if( $this->attribute( 'options_serialized' ) === null ) {
			return array();
		}

		return unserialize( $this->attribute( 'options_serialized' ) );
	}

	public function addClassAttributeID( $id ) {
		$attributeIDs = $this->getClassAttributeIDs();
		if( in_array( $id, $attributeIDs ) === false ) {
			$attributeIDs[] = $id;
			$this->setAttribute( 'classattribute_ids_serialized', serialize( $attributeIDs ) );
		}
	}

	public function removeClassAttributeID( $id ) {
		$attributeIDs = $this->getClassAttributeIDs();
		foreach( $attributeIDs as $key => $attributeID ) {
			if( (int) $attributeID === (int) $id ) {
				unset( $attributeIDs[ $key ] );
			}
		}

		$this->setAttribute( 'classattribute_ids_serialized', serialize( $attributeIDs ) );
	}


	public function setOptions( $name, $value ) {
		$options = $this->getOptions();
		$options[ $name ] = $value;
		$this->setAttribute( 'options_serialized', serialize( $options ) );
	}

	public function hasExtraOptions() {
		return false;
	}

	public function getExtraOptionsViewTemplate() {
		return 'social_networks/publish_extra_options/view/' . $this->attribute( 'type' ) . '.tpl';
	}

	public function getExtraOptionsEditTemplate() {
		return 'social_networks/publish_extra_options/edit/' . $this->attribute( 'type' ) . '.tpl';
	}

	public function getExtraOptionNames() {
		return array();
	}

	public function publish( eZContentObject $object, $message ) {}

	protected function getAPI() {
		return false;
	}
}
?>
