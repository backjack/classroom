@extends('admin.report.report')

@section('content')



    <div>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab"><?=__('overview')?></a></li>
            <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><?=__('student-scores')?></a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">


            <div role="tabpanel" class="tab-pane active" id="home">

                <table class="table table-striped datatable">
                    <thead>
                    <tr>
                        <th><?=__('homework')?></th>
                        <th><?=__('created-on')?></th>
                        <th><?=__('due-date')?></th>
                        <th><?=__('created-by')?></th>
                        <th><?=__('passmark')?></th>
                        <th><?=__('submissions')?></th>
                        <th><?=__('average-score')?></th>
                        <th><?=__('average-grade')?></th>
                        <th><?=__('total-passed')?></th>
                        <th><?=__('total-failed')?></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($session->assignments as $assignment)

                        <tr>
                            <td>{{ $assignment->title }}</td>
                            <td>{{ showDate('d/M/Y',$assignment->created_on) }}</td>
                            <td>{{ showDate('d/M/Y',$assignment->due_date) }}</td>
                            <td>{{ $assignment->account->first_name }} {{ $assignment->account->last_name }}</td>
                            <td>{{ $assignment->passmark }}%</td>
                            <td>{{ $assignment->assignmentSubmissions()->count() }}</td>
                            <td>{{ round($assignment->assignmentSubmissions()->avg('grade'),1) }}</td>
                            <td>{{ $testGradeTable->getGrade($assignment->assignmentSubmissions()->avg('grade')) }}</td>
                            <td>{{ $assignment->assignmentSubmissions()->where('grade','>=',$assignment->passmark)->count() }}</td>
                            <td>{{ $assignment->assignmentSubmissions()->where('grade','<',$assignment->passmark)->count() }}</td>

                        </tr>

                    @endforeach
                    </tbody>
                </table>
            </div>

            <div role="tabpanel" class="tab-pane" id="profile">
                <div class="table-responsive">
                    <table class="table table-striped datatable">
                        <thead>
                        <tr>
                            <th><?=__('student')?></th>
                            <th><?=__('average-score')?></th>
                            <th><?=__('average-grade')?></th>
                            @foreach($session->assignments as $assignment)
                                <th>
                                    {{ limitLength($assignment->title,30) }}
                                </th>

                            @endforeach

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($rowset as $row)
                            <?php $student = \Application\Entity\Student::find($row->id) ?>
                            @if($student)
                                <tr>
                                    @php $stats = $controller->getStudentAssignmentStats($row->id); @endphp
                                    <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                    <td>{{ round($stats['average'],1) }}%</td>
                                    <td>{{ $testGradeTable->getGrade($stats['average']) }}</td>
                                    @foreach($session->assignments as $assignment)
                                        <td>
                                            @php $result = $assignment->assignmentSubmissions()->where('student_id',$student->student_id)->orderBy('grade','desc')->first() @endphp
                                            @if($result)
                                                {{ round($result->grade,1) }}% ({{ $testGradeTable->getGrade($result->grade) }})
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>






@endsection