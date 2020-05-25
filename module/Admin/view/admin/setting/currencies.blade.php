<p class="well">
    <?=__('currency-help',['url'=>$_url('admin/default',['controller'=>'setting','action'=>'index'])])?>
 
</p>
<a id="currencyBtn" class="btn btn-primary" href="#" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> <?=__('add-currency')?></a>


<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th><?=__('currency')?></th>
            <th style="width: 150px"><?=__('exchange-rate')?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($currencies as $currency)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $currency->country->currency_name }} - {{ $currency->country->currency_code }} @if($currentCountry == $currency->country_id) <strong>(<?=__('default')?>)</strong>  @endif</td>
                <td >
                    <p class="age{{ $currency->currency_id }}">

  <span class="editable{{ $currency->currency_id }}">
    {{ $currency->exchange_rate }}
  </span>
                        <input type="button" value="<?=__('edit')?>" class="btn btn-primary pull-right btn-sm btn_edit{{ $currency->currency_id }}"></input>
                    </p>
                </td>
                <td>
                    @if($currentCountry != $currency->country_id)
                    <a onclick="return confirm('<?=__('currency-remove-confirm')?>')" class="btn btn-primary" href="{{ $_url('admin/default',['controller'=>'setting','action'=>'deletecurrency','id'=>$currency->currency_id]) }}"><i class="fa fa-trash"></i> <?=__('remove')?></a>
                    @endif
                </td>
            </tr>
            <script>
                // edit button
                var option = {trigger : $(".age{{ $currency->currency_id }} .btn_edit{{ $currency->currency_id }}"), action : "click"};
                $(".age{{ $currency->currency_id }} .editable{{ $currency->currency_id }}").editable(option, function(e){
                    if(isNaN(e.value)){
                        $(".age{{ $currency->currency_id }} .editable{{ $currency->currency_id }}").html(e.old_value);
                        alert(e.value + " is not a valid rate");
                    }
                    else{
                        $.post('{{ $_url('admin/default',['controller'=>'setting','action'=>'updatecurrency','id'=>$currency->currency_id]) }}',{'rate': e.value});
                    }
                });


            </script>

            @endforeach
    </tbody>
</table>
{{ $currencies->links() }}
<!-- Modal -->
<div class="modal fade" id="myModal"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form class="form" method="post" action="{{ $_url('admin/default',['controller'=>'setting','action'=>'addcurrency']) }}">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?=__('add-currency')?></h4>
            </div>
            <div class="modal-body">


                <div class="form-group">
                    <label for="country"><?=__('currency')?></label>
                    <select class="form-control select2" name="country" id="country">
                        <option value=""></option>
                        @foreach($countries as $country)
                            <option value="{{ $country->country_id }}">{{ $country->currency_name }} ({{ $country->currency_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="exchange_rate"><?=__('exchange-rate')?></label>
                    <input class="form-control digit" type="text" value="" name="exchange_rate"/>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=__('close')?></button>
                <button type="submit" class="btn btn-primary"><?=__('add-currency')?></button>
            </div>
            </form>
        </div>
    </div>
</div>
<?php $_headScript()->appendFile($_basePath().'/static/jquery.editable/jquery.editable.min.js') ?>




