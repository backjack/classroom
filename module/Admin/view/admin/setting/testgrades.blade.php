<a class="btn btn-primary" href="{{ $_url('admin/default',['controller'=>'setting','action'=>'addtestgrade']) }}"><i class="fa fa-plus"></i> <?=__('Add Grade')?></a>
<table class="table table-striped">
    <thead>
    <tr>
        <th><?=__('Grade')?></th>
        <th><?=__('Minimum')?></th>
        <th><?=__('Maximum')?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
        @foreach($grades as $grade)
            <tr>
            <td>{{ $grade->grade }}</td>
            <td>{{ $grade->min }}</td>
            <td>{{ $grade->max }}</td>
            <td>
                <a class="btn btn-primary" href="{{ $_url('admin/default',['controller'=>'setting','action'=>'edittestgrade','id'=>$grade->test_grade_id]) }}"><i class="fa fa-pencil"></i> <?=__('Edit')?></a>
                <a class="btn btn-danger" onclick="return confirm('<?=__('delete-confirm')?>')" href="{{ $_url('admin/default',['controller'=>'setting','action'=>'deletetestgrade','id'=>$grade->test_grade_id]) }}"><i class="fa fa-trash"></i> <?=__('Delete')?></a>

            </td>
            </tr>
            @endforeach
    </tbody>
</table>