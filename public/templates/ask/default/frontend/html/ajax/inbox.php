<div class="bg-white" >
    <div class="mb-1 uk-overflow-auto bg-white" id="inbox-dialog-container" style="height: 300px">

    </div>
    <form method="post" action="{:url('member/inbox//send')}" class="pb-3 pt-2">
        <input type="hidden" name="recipient_uid" value="{$user_name}">
        <div class="form-group overflow-hidden px-3">
            <textarea type="text" name="message" rows="4" class="form-control float-left border-0 bg-light" placeholder="私信内容"></textarea>
        </div>

        <div class="px-3 overflow-hidden">
            <button class="btn btn-primary px-3 btn-sm d-block uk-ajax-submit float-right" type="button">发送私信</button>
        </div>
    </form>
</div>
