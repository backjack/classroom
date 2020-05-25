<div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab"><?=__('message')?></a></li>
        <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><?=__('placeholders')?></a></li>
        <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab"><?=__('default-message')?></a></li>

    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="home">
            <div class="well">
                <?=__('e-template-desc-'.$template->email_template_id)?>
            </div>
            <form action="{{ selfURL() }}" method="post">
                <div class="form-group">
                    <label for="subject"><?=__('subject')?></label>
                    <input required="required" type="text" class="form-control" name="subject" value="{{ $template->subject }}">
                </div>

                <div class="form-group">
                    <label for="message"><?=__('message')?></label>
                    <textarea  required="required"  name="message" id="message" class="form-control summernote">{{ $template->message }}</textarea>
                </div>
                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> <?=__('save')?></button>
            </form>
        </div>
        <div role="tabpanel" class="tab-pane" id="profile">{!!  $template->placeholders  !!}</div>
        <div role="tabpanel" class="tab-pane" id="messages">
            <p><strong><?=__('subject')?>:</strong> {{ $template->default_subject }}</p>
            <hr>

            <div class="well">
                <strong><?=__('message')?></strong>
                <hr>
                <p>{!!  $template->default  !!}</p></div>
            <a href="{{ $_url('admin/default',['controller'=>'messages','action'=>'resetemail','id'=>$template->email_template_id]) }}" onclick="return confirm('<?=__('restore-default-help')?>')" class="btn btn-primary"><i class="fa fa-refresh"></i> <?=__('restore-default')?></a>
        </div>
    </div>

</div>
<?php $_headScript()->prependFile($_basePath() . '/static/summernote/summernote.min.js')->appendFile($_basePath() . '/static/summernote-ext-emoji/src/summernote-ext-emoji.js')
?>
<?php $_headLink()->prependStylesheet($_basePath().'/static/summernote/summernote.css')->appendStylesheet($_basePath().'/static/summernote-ext-emoji/src/css-new-version.css');?>
<script>
    $(function() {
        $('.summernote').summernote({  height: 300 });
    });
</script>