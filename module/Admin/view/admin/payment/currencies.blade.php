<p class="well">
    <?=__('currencies-help')?>
    <a href="{{ $_url('admin/default',['controller'=>'setting','action'=>'currencies']) }}" style="text-decoration: underline" target="_blank"><?=__('currency-setup')?></a> <?=strtolower(__('page'))?>
</p>
<form id="currencyform" class="form" method="post" action="{{ selfURL() }}">
    <div class="form-group" >
        <label for="currency"><?=__('select-currency')?></label>
        <select class="form-control select2" required="required" name="currency" id="currency">
            <option value=""></option>
            @foreach($currencies as $currency)
                <option value="{{ $currency->currency_id }}">{{ $currency->country->currency_name }} - {{ $currency->country->currency_code }}</option>
                @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> <?=__('add-currency')?></button>
</form>

<table class="table table-striped">
    <thead>
        <tr>
            <th><?=__('currency')?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    @foreach($rowset as $row)
        <tr>
            <td>{{ $row->currency->country->currency_name }} - {{ $row->currency->country->currency_code }}</td>
            <td><a class="btn btn-primary delete" href="{{ $_url('admin/default',['controller'=>'payment','action'=>'deletecurrency','id'=>$row->payment_method_currency_id]) }}"><i class="fa fa-trash"></i> <?=__('remove')?></a></td>
        </tr>
        @endforeach
    </tbody>
</table>