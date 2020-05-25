<div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab"><?=__('message')?></a></li>
        <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><?=__('placeholders')?></a></li>
        <li role="presentation"><a href="#defaulttab" aria-controls="defaulttab" role="tab" data-toggle="tab"><?=__('default-message')?></a></li>

    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="home">
            <div class="well">
             <?=__('s-template-desc-'.$template->sms_template_id)?>
            </div>
            <form action="{{ selfURL() }}" method="post">            

                <div class="form-group">
                    <label for="smsmessage"><?=__('message')?></label>
                    <textarea rows="6"  required="required"  name="message" id="smsmessage" class="form-control summernote">{{ $template->message }}</textarea>
                    <p>
                        <span id="remaining">160 <?=__('characters-remaining')?></span>
                        <span id="messages">1 <?=__('messages')?></span>
                    </p>
                    <small><?=__('sms-template-help')?>.</small>
                </div>
                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> <?=__('save')?></button>
            </form>
        </div>
        <div role="tabpanel" class="tab-pane" id="profile">{!!  $template->placeholders  !!}</div>
        <div role="tabpanel" class="tab-pane" id="defaulttab">

            <div class="well">
                <strong><?=__('message')?></strong>
                <hr>
                <p>{!!  $template->default  !!}</p></div>
            <a href="{{ $_url('admin/default',['controller'=>'messages','action'=>'resetsms','id'=>$template->sms_template_id]) }}" onclick="return confirm('<?=__('restore-default-help')?>')" class="btn btn-primary"><i class="fa fa-refresh"></i> <?=__('restore-default')?></a>

        </div>
    </div>

</div>
<script>
    $(document).ready(function(){
        var $remaining = $('#remaining'),
            $messages = $remaining.next();

        $('#smsmessage').keyup(function(){
            var chars = this.value.length,
                messages = Math.ceil(chars / 160),
                remaining = messages * 160 - (chars % (messages * 160) || messages * 160);

            $remaining.text(remaining + ' <?=__('characters-remaining')?>');
            $messages.text(messages + ' <?=__('messages')?>');
        });

        $('#smsmessage').trigger('keyup');
    });

</script>