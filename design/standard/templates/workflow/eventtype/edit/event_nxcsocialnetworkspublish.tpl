{ezcss_require( array( 'nxc_social_networks.css' ) )}
{ezscript_require( array( 'nxc.social.networks.js' ) )}

<div class="handlers-wrapper">

	<input type="hidden" name="WorkflowEvent_event_nxcsocialnetworkspublish_remove_handler_{$event.id}" value="" class="remove-handler-id" />
	<input type="submit" name="CustomActionButton[{$event.id}_remove_handler]" value="{'Remove'|i18n( 'extension/nxc_social_networks' )}" style="display: none;" class="do-remove-handler" />

	<input type="hidden" name="WorkflowEvent_event_nxcsocialnetworkspublish_remove_handler_class_attribute_{$event.id}" value="" class="remove-handler-class-attribute-id" />
	<input type="submit" name="CustomActionButton[{$event.id}_remove_handler_class_attribute]" value="{'Remove'|i18n( 'extension/nxc_social_networks' )}" style="display: none;" class="do-remove-handler-class-attribute" />

	{def
		$content_class   = false()
		$class_attribute = false()
	}
	{foreach $event.handlers as $handler}
		<div class="block">
			<table class="list" cellspacing="0">
				<tr>
					<th colspan="2">
						{$handler.name}
						<div class="button-right">
							<a href="#" rel="{$handler.id}" class="remove-publish-handler"><img src="{'button-delete.gif'|ezimage( 'no' )}" height="16" width="16" alt="{'Remove'|i18n( 'extension/nxc_social_networks' )}" title="{'Remove'|i18n( 'extension/nxc_social_networks' )}" /></a>
						</div>
					</th>
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
										<td>
											{$content_class.name|wash} / {$class_attribute.name|wash} ({$class_attribute.data_type_string|wash})
											<div class="button-right">
												<a href="#" rel="{$handler.id}|{$class_attribute_id}" class="remove-publish-handler-class-attribute"><img src="{'button-delete.gif'|ezimage( 'no' )}" height="16" width="16" alt="{'Remove'|i18n( 'extension/nxc_social_networks' )}" title="{'Remove'|i18n( 'extension/nxc_social_networks' )}" /></a>
											</div>
										</td>
									</tr>
								{/if}
							{/foreach}
						</table>
						{/if}
						<div class="controlbar nxc-social-network-attribute-controlbar">
							<select name="WorkflowEvent_event_nxcsocialnetworkspublish_handler_new_class_attributes{$event.id}[{$handler.id}]">
							{foreach $event.contentclass_attribute_list as $class_attribute}
								{if $handler.classattribute_ids|contains( $class_attribute.id )}
									{continue}
								{/if}

								{set $content_class = fetch(
									'content',
									'class',
									hash(
										'class_id',
										$class_attribute.contentclass_id
									)
								)}
								<option value="{$class_attribute.id}">{$content_class.name|wash} / {$class_attribute.name|wash}</option>
							{/foreach}
						    </select>
						    <input class="button" type="submit" name="CustomActionButton[{$event.id}_add_handler_class_attribute_{$handler.id}]" value="{'Add attribute'|i18n( 'extension/nxc_social_networks' )}" />
						</div>
					</td>
					<td class="nxc-social-network-td-container">
						<label class="nxc-social-network-attribute-header">{'Options'|i18n( 'extension/nxc_social_networks' )}:</label>
						<label class="nxc-social-network-attribute-checkbox"><input type="checkbox" name="WorkflowEvent_event_nxcsocialnetworkspublish_handler_options[publish_only_on_create][{$handler.id}]" value="1" {if $handler.options.publish_only_on_create}checked="checked"{/if}/>
						{'Publish only on object`s creation'|i18n( 'extension/nxc_social_networks' )}</label>

						<label class="nxc-social-network-attribute-checkbox"><input type="checkbox" name="WorkflowEvent_event_nxcsocialnetworkspublish_handler_options[include_url][{$handler.id}]" value="1" {if $handler.options.include_url}checked="checked"{/if}/>
						{'Include node`s URL'|i18n( 'extension/nxc_social_networks' )}</label>

						{if $handler.has_extra_options}
							{include
								uri=concat( 'design:', $handler.extra_options_edit_template )
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

<div class="block">
    <label>{'Publish to'|i18n( 'extension/nxc_social_networks' )}:</label>
    <select name="WorkflowEvent_event_nxcsocialnetworkspublish_new_handler_{$event.id}">
	{foreach $event.available_handler_names as $type => $name}
		<option value="{$type}">{$name}</option>
	{/foreach}
    </select>
    <input class="button" type="submit" name="CustomActionButton[{$event.id}_new_handler]" value="{'Add'|i18n( 'extension/nxc_social_networks' )}" />
</div>
