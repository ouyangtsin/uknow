{switch name="type"}
	{case value="readonly"}
		<input type="text" class="uk-input" name="{$field}" id="{$field}" value="{$value}" autocomplete="false" readonly>
	{/case}
	{case value="num"}
		<input type="text" style="width: auto;" class="uk-input" name="{$field}" id="{$field}" autocomplete="false" value="{$value}">
	{/case}
	{case value="decimal"}
		<input type="text" style="width: auto;" class="uk-input" name="{$field}" id="{$field}" autocomplete="false" value="{$value}">
	{/case}
	{case value="text"}
		<input type="text" class="uk-input" name="{$field}" id="{$field}" autocomplete="false" value="{$value}">
	{/case}
	{case value="password"}
		<input type="password" class="uk-input" name="{$field}" id="{$field}" autocomplete="false" value="{$value}">
	{/case}
	{case value="textarea"}
	<textarea class="uk-textarea" name="{$field}" id="{$field}">{$value}</textarea>
	{/case}
	{case value="select"}
	<select class="uk-select" name="{$field}" id="{$field}" style="width:auto;">
		{volist name="option" id="item"}
		<option value="{$key}" {if condition="$key eq $value"}selected{/if}>{$item}</option>
		{/volist}
	</select>
	{/case}
	{case value="bool"}
	<select class="uk-select" name="{$field}" id="{$field}" style="width:auto;">
		{volist name="option" id="item"}
		<option value="{$key}" {if condition="$key eq $value"}selected{/if}>{$item}</option>
		{/volist}
	</select>
	{/case}
    {case value="array"}
        {volist name="value" id="item"}
        <dl class="uk-overflow-hidden">
            <dt class="uk-display-inline-block mr-2" style="min-width: 100px">{$key}</dt>
            <dd class="uk-display-inline-block"><input type="text" name="{$field}[{$key}]" value="{$item}" class="uk-input"></dd>
        </dl>
        {/volist}
    {/case}
	{case value="bind"}
	<select class="uk-select" name="{$field}" id="{$field}" style="width:auto;">
		{volist name="option" id="item"}
		<option value="{$key}" {if condition="$key eq $value"}selected{/if}>{$item}</option>
		{/volist}
	</select>
	{/case}
	{case value="checkbox"}
		{php}$value = isset($value) && is_array($value) ? $value : array();{/php}
		{volist name="option" id="item"}
        <label for="{$field}-{$key}" class="uk-display-inline-block mr-2">
            <input type="checkbox" class="uk-checkbox" name="{$field}[]" id="{$field}-{$key}" value="{$key}" {if in_array($key, $value)}checked{/if}/>
            {$item}
        </label>
		{/volist}
	{/case}
	{case value="radio"}
		{php}$value = isset($value) ? $value : 1;{/php}
		{volist name="option" id="item"}
        <label for="{$field}-{$key}" class="uk-display-inline-block mr-2">
            <input type="radio" class="uk-radio" name="{$field}" id="{$field}-{$key}" value="{$key}" {if condition="$key eq $value"}checked{/if}/>
            {$item}
        </label>
		{/volist}
	{/case}
{/switch}