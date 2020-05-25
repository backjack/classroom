

<table class="table table-striped">
    <thead>
    <tr>
        <th>#</th>
        <th><?=__('message')?></th>
        <th><?=__('description')?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    @foreach($templates as $template)
        <tr>
            <td>{{ $template->sms_template_id }}</td>
            <td><?=__('s-template-name-'.$template->sms_template_id)?></td>
            <td><?=__('s-template-desc-'.$template->sms_template_id)?></td>
            <td><a class="btn btn-primary" href="{{ $_url('admin/default',['controller'=>'messages','action'=>'editsms','id'=>$template->sms_template_id]) }}"> <i class="fa fa-pencil"></i> <?=__('edit')?></a></td>
        </tr>
    @endforeach
    </tbody>
</table>

{{ $templates->links() }}