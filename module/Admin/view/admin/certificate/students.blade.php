

<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th><?=__('student')?></th>
                <th><?=__('tracking-number')?></th>
                <th><?=__('downloaded-on')?></th>
            </tr>
        </thead>

        <tbody>

        @foreach($students as $student)

            <tr>
                <td><a class="viewbutton" style="text-decoration: underline"   data-id="<?php echo $student->student_id; ?>" data-toggle="modal" data-target="#simpleModal" href="">{{ $student->student->first_name }} {{ $student->student->last_name }}</a></td>
                <td>{{ $student->tracking_number }}</td>
                <td>{{ showDate('d/M/Y',$student->created_on) }}</td>
            </tr>

        @endforeach

        </tbody>

    </table>

</div>
<div>
    {{ $students->links() }}
</div>

<!-- START SIMPLE MODAL MARKUP -->
<div class="modal fade" id="simpleModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="simpleModalLabel"><?=__('student-details')?></h4>
            </div>
            <div class="modal-body" id="info">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?=__('close')?></button>
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