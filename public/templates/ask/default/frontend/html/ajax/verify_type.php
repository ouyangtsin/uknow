{if isset($keyList) /}
{volist name="keyList" id="field"}
<div class="form-group">
    <label class="uk-form-label">{$field['title']|htmlspecialchars} </label>
    <div class="uk-form-controls">
        <label>
            {:widget('form/show',array($field,$info))}
        </label>
    </div>
</div>
{/volist}
{/if}
<script>
    let isVerify = parseInt("{:isset($verify_info['status']) ? 1 : 0}");
    let verify = parseInt("{:isset($verify_info['status']) ? $verify_info['status'] : 0}");
    if(verify<2 && isVerify)
    {
        $('#uk-center-main input').attr('readonly','readonly').attr('disabled','disabled');
        $('#uk-center-main textarea').attr('readonly','readonly').attr('disabled','disabled');
        $('#uk-center-main select').attr('readonly','readonly').attr('disabled','disabled');
    }
</script>