{* Class/attribute list *}
{def $selected_class_attributes = $event.data_text1|explode( ',' )}
{if eq( $selected_class_attributes[0], '' )}
	{set $selected_class_attributes = $selected_class_attributes|remove( 0 )}
{/if}
<div class="block">
	<label>{'Class/attribute combinations (%count)'|i18n( 'extension/nxc_social_networks', , hash( '%count', $selected_class_attributes|count ) )}:</label>
	{if gt( $selected_class_attributes|count(), 0 )}
	<table class="list" cellspacing="0">
		<tr>
			<th>{'Class'|i18n( 'extension/nxc_social_networks' )}</th>
			<th>{'Attribute'|i18n( 'extension/nxc_social_networks' )}</th>
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
					<td>{$content_class.name|wash}</td>
					<td>{$class_attribute.name|wash} ({$class_attribute.data_type_string|wash})</td>
				</tr>
			{/if}
		{/foreach}
		{undef $class_attribute $content_class}
	</table>
	{else}
	<p>{'There are no combinations'|i18n( 'extension/nxc_social_networks' )}</p>
	{/if}
</div>
{undef $selected_class_attributes}



{* Target ID *}
{if $event.data_text2}
<div class="block">
	<label>{'Target ID'|i18n( 'extension/nxc_social_networks' )}:</label>
	{$event.data_text2|wash}
</div>
{/if}



{* Publish message only on object`s creation *}
<div class="block">
	<label>{'Publish message only on object`s creation'|i18n( 'extension/nxc_social_networks' )}:</label>
	{if $event.data_int1}{'Yes'|i18n( 'extension/nxc_social_networks' )}{else}{'No'|i18n( 'extension/nxc_social_networks' )}{/if}
</div>



{* Include node`s URL *}
<div class="block">
	<label>{'Include node`s URL to the message'|i18n( 'extension/nxc_social_networks' )}:</label>
	{if $event.data_int2}{'Yes'|i18n( 'extension/nxc_social_networks' )}{else}{'No'|i18n( 'extension/nxc_social_networks' )}{/if}
</div>