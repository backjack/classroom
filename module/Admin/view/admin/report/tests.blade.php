@extends('admin.report.report')

@section('content')



    <div>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab"><?=__('overview')?></a></li>
            <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><?=__('student-scores')?></a></li>
            <li role="presentation"><a href="#cards" aria-controls="cards" role="tab" data-toggle="tab"><?=__('report-cards')?></a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home">
                <table class="table table-striped datatable">
                    <thead>
                    <tr>
                        <th><?=__('test')?></th>
                        <th><?=__('questions')?></th>
                        <th><?=__('passmark')?></th>
                        <th><?=__('attempts')?></th>
                        <th><?=__('created-by')?></th>
                        <th><?=__('average-score')?></th>
                        <th><?=__('average-grade')?></th>
                        <th><?=__('total-passed')?></th>
                        <th><?=__('total-failed')?></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($allTests as $testId)
                        <?php $test = \Application\Entity\Test::find($testId); ?>
                        @if($test)
                            <tr>
                                <td>{{ $test->name }}</td>
                                <td>{{ $test->testQuestions()->count() }}</td>
                                <td>{{ $test->passmark }}%</td>
                                <td>{{ $test->studentTests()->count() }}</td>
                                <td>{{ $viewHelper->get('adminName')($test->account_id) }}</td>
                                <td>{{ round($test->studentTests()->avg('score'),1) }}</td>
                                <td>{{ $testGradeTable->getGrade($test->studentTests()->avg('score')) }}</td>
                                <td>{{ $test->studentTests()->where('score','>=',$test->passmark)->count() }}</td>
                                <td>{{ $test->studentTests()->where('score','<',$test->passmark)->count() }}</td>
                            </tr>
                        @endif
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
                        @foreach($tests as $test)
                            <th>
                                {{ limitLength($test->name,30) }}
                            </th>

                            @endforeach

                    </tr>
                    </thead>
                    <tbody>
                        @foreach($rowset as $row)
                            <?php $student = \Application\Entity\Student::find($row->id) ?>
                            @if($student)
                                <tr>
                                @php $stats = $controller->getStudentTestsStats($row->id); @endphp
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td>{{ round($stats['average'],1) }}%</td>
                                <td>{{ $testGradeTable->getGrade($stats['average']) }}</td>
                                @foreach($tests as $test)
                                   <td>
                                       @php $result =$test->studentTests()->where('student_id',$student->student_id)->orderBy('score','desc')->first() @endphp
                                       @if($result)
                                           {{ round($result->score,1) }}% ({{ $testGradeTable->getGrade($result->score) }})
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
            <div role="tabpanel" class="tab-pane" id="cards">
                
                <table class="table-stripped table" id="reportcards">
                    <thead>
                    <tr>
                        <th><?=__('student')?></th>
                        <th><?=__('average-score')?></th>
                        <th><?=__('average-grade')?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($rowset as $row)
                        <?php $student = \Application\Entity\Student::find($row->id) ?>
                        @if($student)
                            @php $stats = $controller->getStudentTestsStats($row->id); @endphp
                            <tr>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td>{{ round($stats['average'],1) }}%</td>
                                <td>{{ $testGradeTable->getGrade($stats['average']) }}</td>
                                <td><a class="btn btn-primary" href="{{ $_url('admin/default',['controller'=>'report','action'=>'reportcard','id'=>$student->student_id]) }}?sessionId={{ $session->session_id }}"><i class="fa fa-download"></i> Download</a></td>
                            </tr>
                        
                        @endif
                        
                     @endforeach   
                        
                    </tbody>
                    
                </table>
                <script type="text/javascript">
                    $(function(){
                       $('#reportcards').DataTable(); 
                    });
                </script>
                </div>
        </div>

    </div>






@endsection