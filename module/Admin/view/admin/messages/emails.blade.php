

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
                <td>{{ $template->email_template_id }}</td>
                <td><?=__('e-template-name-'.$template->email_template_id)?></td>
                <td><?=__('e-template-desc-'.$template->email_template_id)?></td>
                <td><a class="btn btn-primary" href="{{ $_url('admin/default',['controller'=>'messages','action'=>'editemail','id'=>$template->email_template_id]) }}"> <i class="fa fa-pencil"></i> <?=__('edit')?></a></td>
            </tr>
        @endforeach
    </tbody>
</table>

{{ $templates->links() }}