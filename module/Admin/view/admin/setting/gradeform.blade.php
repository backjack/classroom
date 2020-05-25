<form class="form" action="{!! selfURL() !!}" method="post">

    <div class="form-group">
        {!! $_formLabel($form->get('grade')) !!}
        {!! $_formElement($form->get('grade')) !!}
    </div>
    <div class="form-group">
        {!! $_formLabel($form->get('min')) !!}
        {!! $_formElement($form->get('min')) !!}
    </div>
    <div class="form-group">
        {!! $_formLabel($form->get('max')) !!}
        {!! $_formElement($form->get('max')) !!}
    </div>

    <button class="btn btn-primary" type="submit"><?=__('submit')?></button>
</form>