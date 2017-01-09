{strip}
<results>
{if $errorMsg != ''}
	<error_code>{$errorCode|escape}</error_code>
	<message>{$errorMsg|escape}</message>
{else}
	<records>{$results|sizeof}</records>
	{foreach $results as $result}
	<result id="{$result['orderid']}" description="{$result['description']|escape}" customer="{$result['customer']|escape}">
		{$result['xmldata']}
	</result>
	{/foreach}
{/if}
</results>
{/strip}