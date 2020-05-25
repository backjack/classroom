<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th><?=__('taken-on')?></th>
            <th><?=__('Score')?></th>
            <th><?=__('Grade')?></th>
            <th><?=__('Status')?></th>
        </tr>
    </thead>
    <tbody>
        @foreach($rowset as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ showDate('d/M/Y',$row->created_on) }}</td>
                <td>{{ round($row->score) }}</td>
                <td>{{ $gradeTable->getGrade($row->score) }}</td>
                <td>@if($row->score >= $test->passmark)
                <span style="color: green"><?=__('Passed')?></span>
                @else
                        <span style="color: red"><?=__('Failed')?></span>
                    @endif
                </td>

            </tr>

            @endforeach
    </tbody>


</table>

{!! $rowset->links() !!}