<div class="row dd_input_group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l" for="{$form[type].name}">{$form[type].title|htmlspecialchars}</label>
    <div class="col-{if $form[type].tips}8{else /}7{/if} col-md-4 col-lg-4">
        <textarea class="form-control" id="{$form[type].name}" name="{$form[type].name}" rows="{$form[type].rows|default='3'}" style="display: none">{$form[type].value}</textarea>
        <table class="table table-danger array-table">
            <thead>
            <tr>
                <th>键名</th>
                <th>键值</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody class="{$form[type].name}-container">
            {volist name="form[type].options" id="option"}
            <tr>
                <td><input type="text" class="form-control" name="{$form[type].name}[key][]" value="{$key}"></td>
                <td><input type="text" class="form-control" name="{$form[type].name}[value][]" value="{$option}"></td>
                <td><a href="javascript:;" class="btn-remove"><i class="fa fa-trash"></i></a> </td>
            </tr>
            {/volist}
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3"><a href="javascript:;" class="btn btn-sm btn-success btn-append" data-name="{$form[type].name}"><i class="fa fa-plus"></i> 追加</a></td>
            </tr>
            </tfoot>
        </table>
    </div>
    <div class="col-{if $form[type].tips}12{else /}1{/if} col-md-6 col-lg-6 dd_ts">
        {notempty name="form[type].required"} *{/notempty}
        {notempty name="form[type].tips"} {$form[type].tips|raw}{/notempty}
    </div>
</div>
<script>
    $(document).on('click','.btn-append',function() {
        var name = $(this).data('name');
        var html = '<tr><td><input type="text" class="form-control" name="'+name+'[key][]"></td><td><input type="text" class="form-control" name="'+name+'[value][]"></td><td><a href="javascript:;" class="btn-remove"><i class="fa fa-trash"></i></a> </td> </tr>';
        $(this).parents('table').find('.{$form[type].name}-container').append(html);
    })

    $(document).on('click','.btn-remove',function() {
        $(this).parents('tr').remove();
    })
</script>