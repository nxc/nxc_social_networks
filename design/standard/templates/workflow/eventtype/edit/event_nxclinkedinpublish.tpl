{* Class *}
<div class="block">
	<label>{'Class'|i18n( 'extension/nxc_socail_networks' )}:</label>
	<select name="WorkflowEvent_event_nxclinkedinpublish_class_{$event.id}">
	{foreach $event.workflow_type.contentclass_list as $class}
		<option value="{$class.id}" {if eq( $event.workflow_type.current_class_id, $class.id )}selected="selected"{/if}>{$class.name|wash}</option>
	{/foreach}
	</select>
	<input class="button" type="submit" name="CustomActionButton[{$event.id}_load_class_attribute_list]" value="{'Update attributes'|i18n( 'extension/nxc_socail_networks' )}" />
</div>



{* Attributes *}
<div class="block">
	<label>{'Attribute'|i18n( 'extension/nxc_socail_networks' )}:</label>
	<select name="WorkflowEvent_event_nxclinkedinpublish_class_attribute_{$event.id}">
	{foreach $event.workflow_type.contentclass_attribute_list as $possible_attribute}
		<option value="{$possible_attribute.id}">{$possible_attribute.name|wash}</option>
	{/foreach}
	</select>
	<input class="button" type="submit" name="CustomActionButton[{$event.id}_new_class_attribute]" value="{'Select attribute'|i18n( 'extension/nxc_socail_networks' )}" />
</div>



{* Class/attribute list *}
{def $selected_class_attributes = $event.data_text1|explode( ',' )}
{if eq( $selected_class_attributes[0], '' )}
	{set $selected_class_attributes = $selected_class_attributes|remove( 0 )}
{/if}
<div class="block">
	<label>{'Class/attribute combinations (%count)'|i18n( 'extension/nxc_socail_networks', , hash( '%count', $selected_class_attributes|count ) )}:</label>
	{if gt( $selected_class_attributes|count(), 0 )}
	<table class="list" cellspacing="0">
		<tr>
			<th class="tight">&nbsp;</th>
			<th>{'Class'|i18n( 'extension/nxc_socail_networks' )}</th>
			<th>{'Attribute'|i18n( 'extension/nxc_socail_networks' )}</th>
		</tr>

		{def
			$class_attribute = false()
			$content_class = false()
		}
		{foreach $selected_class_attributes as $class_attribute_id sequence array( 'bgdark', 'bglight' ) as $row_class}
			{set $class_attribute = fetch(
				'content',
				'class_attribute',
				hash(
					'attribute_id',
					$class_attribute_id
				)
			)}
			{if $class_attribute}
				{set $content_class = fetch(
					'content',
					'class',
					hash(
						'class_id',
						$class_attribute.contentclass_id
					)
				)}
				<tr class="{$row_class}">
					<td><input type="checkbox" name="WorkflowEvent_event_nxclinkedinpublish_remove_class_attribute_{$event.id}[]" value="{$class_attribute.id}" /></td>
					<td>{$content_class.name|wash}</td>
					<td>{$class_attribute.name|wash} ({$class_attribute.data_type_string|wash})</td>
				</tr>
			{/if}
		{/foreach}
		{undef $class_attribute $content_class}
	</table>
	{else}
	<p>{'There are no combinations'|i18n( 'extension/nxc_socail_networks' )}</p>
	{/if}

	<div class="controlbar">
		<input class="button" type="submit" name="CustomActionButton[{$event.id}_remove_class_attribute]" value="{'Remove selected'|i18n( 'extension/nxc_socail_networks' )}" {if eq( $selected_class_attributes|count(), 0 )}disabled="disabled"{/if} />
	</div>
</div>
{undef $selected_class_attributes}



{* Publish message only on object`s creation *}
<div class="block">
	<label>{'Publish message only on object`s creation'|i18n( 'extension/nxc_socail_networks' )}:</label>
	<input type="checkbox" name="WorkflowEvent_data_nxclinkedinpublish_publish_only_on_create_{$event.id}" value="1" {if $event.data_int1}checked="checked"{/if} />
</div>



{* Include node`s URL *}
<div class="block">
	<label>{'Include node`s URL to the message'|i18n( 'extension/nxc_socail_networks' )}:</label>
	<input type="checkbox" name="WorkflowEvent_data_nxclinkedinpublish_include_url_{$event.id}" value="1" {if $event.data_int2}checked="checked"{/if} />
</div>