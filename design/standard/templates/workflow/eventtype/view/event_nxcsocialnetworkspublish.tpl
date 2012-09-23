{ezcss_require( array( 'nxc_social_networks.css' ) )}

<div class="handlers-wrapper">

	{def
		$content_class   = false()
		$class_attribute = false()
	}
	{foreach $event.handlers as $handler}
		<div class="block">
			<table class="list" cellspacing="0">
				<tr>
					<th colspan="2">{$handler.name}</th>
				</tr>
				<tr>
					<td class="nxc-social-network-td-container">
						<label class="nxc-social-network-attribute-header">{'Class attributes (%count)'|i18n( 'extension/nxc_social_networks', , hash( '%count', $handler.classattribute_ids|count ) )}:</label>
						{if gt( $handler.classattribute_ids|count, 0 )}
						<table class="list nxc-social-network-attribute-list" cellspacing="0" style="width: 100%;">
							<tr>
								<th>{'Attribute'|i18n( 'extension/nxc_social_networks' )}</th>
							</tr>
							{foreach $handler.classattribute_ids as $class_attribute_id sequence array( 'bgdark', 'bglight' ) as $row_class}
								{set $class_attribute = fetch( 'content', 'class_attribute', hash( 'attribute_id', $class_attribute_id ) )}
								{if $class_attribute}
									{set $content_class = fetch( 'content', 'class', hash( 'class_id', $class_attribute.contentclass_id ) )}
									<tr class="{$row_class}">
										<td>{$content_class.name|wash} / {$class_attribute.name|wash} ({$class_attribute.data_type_string|wash})</td>
									</tr>
								{/if}
							{/foreach}
						</table>
						{/if}
					</td>
					<td class="nxc-social-network-td-container">
						<label class="nxc-social-network-attribute-header">{'Options'|i18n( 'extension/nxc_social_networks' )}:</label>
						<label class="nxc-social-network-attribute-checkbox">{'Publish only on object`s creation'|i18n( 'extension/nxc_social_networks' )}: {if $handler.options.publish_only_on_create}{'Yes'|i18n( 'extension/nxc_social_networks' )}{else}{'No'|i18n( 'extension/nxc_social_networks' )}{/if}</label>

						<label class="nxc-social-network-attribute-checkbox">{'Include node`s URL'|i18n( 'extension/nxc_social_networks' )}: {if $handler.options.include_url}{'Yes'|i18n( 'extension/nxc_social_networks' )}{else}{'No'|i18n( 'extension/nxc_social_networks' )}{/if}</label>

						{if $handler.has_extra_options}
							{include
								uri=concat( 'design:', $handler.extra_options_view_template )
								handler=$handler
							}
						{/if}
					</td>
				</tr>
			</table>
		</div>
	{/foreach}
	{undef $content_class}
</div>
