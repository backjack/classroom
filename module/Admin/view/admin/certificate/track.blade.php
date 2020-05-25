<div>

    <form id="filterform"  role="form"  method="get" action="{{ $_url('admin/default',['controller'=>'certificate','action'=>'track']) }}">

<div class="row">
    <div class="form-group col-md-5">
        <input placeholder="<?=__('tracking-no-name-email')?>" class="form-control" type="text" name="query" id="query" value="{{ @$_GET['query'] }}">
    </div>

    <div class="col-md-6">
        <button type="submit" class="btn btn-primary"> <i class="fa fa-search"></i> <?=__('search')?></button>
        <button type="button" onclick="$('#filterform input, #filterform select').val(''); $('#filterform').submit();" class="btn btn-inverse"><?=__('clear')?></button>

    </div>
</div>


    </form>
</div>
@if($paginator)


<div>
    <table class="table table-striped">
        <thead>
        <tr>
            <th><?=__('student')?></th>
            <th><?=__('certificate')?></th>
            <th><?=__('course-session')?></th>
            <th><?=__('tracking-number')?></th>
            <th><?=__('downloaded-on')?></th>
        </tr>
        </thead>

        <tbody>

        @foreach($paginator as $student)
@php
$certificate = \Application\Entity\Certificate::find($student->certificate_id);
@endphp
            <tr>
                <td><a class="viewbutton" style="text-decoration: underline"   data-id="<?php echo $student->student_id; ?>" data-toggle="modal" data-target="#simpleModal" href="">{{ $student->first_name }} {{ $student->last_name }}</a></td>
                <td>{{$certificate->certificate_name}}</td>
                <td>{{$certificate->session->session_name}}</td>
                <td>{{ $student->tracking_number }}</td>
                <td>{{ showDate('d/M/Y',$student->created_on) }}</td>
            </tr>

        @endforeach

        </tbody>

    </table>

</div>
<div>
    <?php
    // add at the end of the file after the table
    echo $viewHelper->get('paginationControl')(
    // the paginator object
        $paginator,
        // the scrolling style
        'sliding',
        // the partial to use to render the control
        array('partial/paginator.phtml', 'Admin'),
        // the route to link to when a user clicks a control link
        array(
            'route' => 'admin/default',
            'controller'=>'certificate',
            'action'=>'track'
        )
    );
    ?>
</div>
@endif
<!-- START SIMPLE MODAL MARKUP -->
<div class="modal fade" id="simpleModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="simpleModalLabel">Student Details</h4>
            </div>
            <div class="modal-body" id="info">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- END SIMPLE MODAL MARKUP -->

<script type="text/javascript">
    $(function(){
        $('.viewbutton').click(function(){
            $('#info').text('Loading...');
            var id = $(this).attr('data-id');
            $('#info').load('{{ $_url('admin/default',array('controller'=>'student','action'=>'view')) }}'+'/'+id);
        });
    });
</script>