{switch name="type"}
	{case value="readonly"}
		<input type="text" class="form-control" name="{$field}" id="{$field}" value="{$value}" autocomplete="false" readonly>
	{/case}
	{case value="number"}
		<input type="text" style="width: auto;" class="form-control" name="{$field}" id="{$field}" autocomplete="false" value="{$value}">
	{/case}
	{case value="text"}
		<input type="text" class="form-control" name="{$field}" id="{$field}" autocomplete="false" value="{$value}">
	{/case}
	{case value="password"}
		<input type="password" class="form-control" name="{$field}" id="{$field}" autocomplete="false" value="{$value}">
	{/case}
	{case value="textarea"}
	<textarea class="form-control" name="{$field}" id="{$field}">{$value}</textarea>
	{/case}
	{case value="select"}
	<select class="form-control" name="{$field}" id="{$field}" style="width:auto;">
		{volist name="option" id="item"}
		<option value="{$key}" {if condition="$key eq $value"}selected{/if}>{$item}</option>
		{/volist}
	</select>
	{/case}
	{case value="checkbox"}
		{php}$value = isset($value) && is_array($value) ? $value : array();{/php}
		{volist name="option" id="item"}
        <label for="{$field}-{$key}" class="d-inline-block mr-2">
            <input type="checkbox" class="uk-checkbox" name="{$field}[]" id="{$field}-{$key}" value="{$key}" {if in_array($key, $value)}checked{/if}/>
            {$item}
        </label>
		{/volist}
	{/case}
	{case value="radio"}
		{php}$value = isset($value) ? $value : 1;{/php}
		{volist name="option" id="item"}
        <label for="{$field}-{$key}" class="d-inline-block mr-2">
            <input type="radio"  name="{$field}" id="{$field}-{$key}" value="{$key}" {if condition="$key eq $value"}checked{/if}/>
            {$item}
        </label>
		{/volist}
	{/case}
{/switch}