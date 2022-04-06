<script src="{$template_path}ssh_user_key/ssh_user_key.js"></script>
<h2>{$lang.ssh_user_key}</h2>


<div class="wrap">
	{if $g_userinfo.group_properties.root=="y" || $g_userinfo.users_management=="y"}
		<div class="un-user">
		<h3>{$lang.common_seluserplease}</h3>
		<label>{$lang.common_user_current}:</label>
		<b class="un-current">{if $uncurrent|strlen>0}<input type="hidden" value="1" id="userselval">{$uncurrent}{else}{$lang.common_notselect}{/if}</b>
		<div class="un-list" id="select_user_setting">
			<div class="un-list-header un-noclick"><input placeholder="{$lang.common_putname_label}" class="un-noclick" type="text"></div>
			<div id="get_user_filter" class="un-list-body">
				{foreach from=$users item=user}
					<div>{$user}</div>
				{/foreach}
			</div>
		</div>

		<div id="caption_wait" style="display:inline-block;"></div>
	{else}
		<div class="un-user">
	{/if}

	<br/>
	<br/>
	<br/>
	<div id="user_key">
		{if $g_userinfo.group_properties.root!="y" && $g_userinfo.users_management!="y"}
			{include file="{$template_path_sm}ssh_user_key/key.tpl"}
		{/if}
	</div>


</div>
