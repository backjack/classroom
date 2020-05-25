<div class="box">


<table class="table table-striped">
    <thead>
    <tr>
        <th>#</th>
        <th><?=__('course-session')?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    @foreach($sessions as $row)
        <tr>
            <td>{{ $loop->iteration }}</td>
             <td>{{ $row->session->session_name }}</td>
            <td><a class="btn btn-primary" href="{{ $_url('application/default',['controller'=>'test','action'=>'reportcard','id'=>$row->session_id]) }}"><i class="fa fa-download"></i> <?=__('download-statement')?></a></td>
        </tr>
    @endforeach
    </tbody>


</table>
</div>
{!! $sessions->links() !!}