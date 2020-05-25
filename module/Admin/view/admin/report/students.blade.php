@extends('admin.report.report')

@section('content')


    <div>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab"><?=__('Report')?></a></li>
            <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><?=__('Totals')?></a></li>
            <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab"><?=__('Classes')?></a></li>
            <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab"><?=__('Tests')?></a></li>
            <li role="presentation"><a href="#homework" aria-controls="homework" role="tab" data-toggle="tab"><?=__('Homework')?></a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home">
                <div class="table-responsive">
                <table class="table table-striped datatable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?=__('student-name')?></th>
                        <th><?=__('enrolled-on')?></th>
                        <th><?=__('classes-attended')?></th>
                        <th><?=__('progress')?></th>
                        <th><?=__('tests-taken')?></th>
                        <th><?=__('average-test-score')?></th>
                        <th><?=__('test-grade')?></th>
                        <th><?=__('homework-submitted')?></th>
                        <th><?=__('average-homework-score')?></th>
                        <th><?=__('homework-grade')?></th>
                        <th><?=__('instructor-chats')?></th>
                        <th><?=__('forum-topics')?></th>
                        <th><?=__('forum-posts')?></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($rowset as $row)
                        <?php $student = \Application\Entity\Student::find($row->id) ?>
                        @if($student)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $student->first_name.' '.$student->last_name }}</td>
                                <td>
                                    @php
                                    $enrollment = $student->studentSessions()->where('session_id',$id)->first();
                                    @endphp
                                    @if($enrollment)
                                        {{ showDate('d/M/Y',$enrollment->enrolled_on) }}
                                    @endif
                                </td>
                                <?php
                                $attendance = $student->attendance()->where('session_id',$id)->count();
                                ?>
                                <td>{{ $attendance }}</td>

                                <td>

                                    <?php
                                    echo round(($attendance/$totalSessionLessons)*100)
                                    ?>%
                                </td>
                                <?php
                                $testStats = $controller->getStudentTestsStats($row->id);
                                ?>
                                <td>
                                    {{ $testStats['testsTaken'] }}
                                </td>
                                <td>
                                    {{ $testStats['average'] }}
                                </td>
                                <td>
                                    {{ $testGradeTable->getGrade($testStats['average']) }}
                                </td>
                                <?php
                                $homeworkStats = $controller->getStudentAssignmentStats($row->id);
                                ?>
                                <td>
                                    {{ $homeworkStats['submissions'] }}
                                </td>
                                <td>
                                    {{ $homeworkStats['average'] }}
                                </td>
                                <td>
                                    {{ $testGradeTable->getGrade($homeworkStats['average']) }}
                                </td>

                                <td>
                                    {{ $student->discussions()->where('session_id',$id)->count() }}
                                </td>
                                <td>
                                    {{ $student->forumTopics()->where('session_id',$id)->where('topic_owner_type','s')->count() }}
                                </td>
                                <td>
                                    {{ $controller->getStudentTotalPosts($row->id) }}
                                </td>


                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="profile">
                <table class="table table-striped">
                    <tr>
                        <td style="width: 30%"><?=__('enrolled-students')?>:</td>
                        <td>{{ $session->studentSessions()->count() }}</td>
                    </tr>
                    <tr>
                        <td><?=__('total-classes')?>:</td>
                        <td>{{ $session->sessionLessons()->count() }}</td>
                    </tr>
                    <tr>
                        <td><?=__('total-students-attended')?>:</td>
                        <td>{{ $attendanceTable->getTotalStudentsForSession($id) }}</td>
                    </tr>
                    <tr>
                        <td><?=__('total-tests')?>:</td>
                        <td>{{ count($allTests) }}</td>
                    </tr>
                    <tr>
                        <td><?=__('total-homework')?>:</td>
                        <td>{{ $session->assignments()->count() }}</td>
                    </tr>
                </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="messages">
                <table class="table table-striped datatable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?=__('class')?></th>
                        @if($session->session_type=='c')
                            <th><?=__('lectures')?></th>
                        @endif
                        <th><?=__('students-completed')?></th>
                        <th><?=__('completion-percentage')?></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($session->sessionLessons()->orderBy('sort_order')->orderBy('lesson_date')->get() as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row->lesson->lesson_name }}</td>
                            @if($session->session_type=='c')
                                <td>{{ $row->lesson->lectures()->count() }}</td>
                            @endif
                            @php
                            $totalAttended = $attendanceTable->getTotalStudentsForSessionAndLesson($session->session_id,$row->lesson_id);
                            @endphp
                            <td>{{ $totalAttended }}</td>
                            @php
                            $total = $session->studentSessions()->count();
                            if(empty($total)){
                            $total=1;
                            }
                            @endphp
                            <td>{{ ($totalAttended/$total)*100 }}%</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>



            </div>
            <div role="tabpanel" class="tab-pane" id="settings">

                <table class="table table-striped datatable">
                    <thead>
                    <tr>
                        <th><?=__('test')?></th>
                        <th><?=__('questions')?></th>
                        <th><?=__('passmark')?></th>
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
                                        </tr>
                                    @endif
                                @endforeach
                    </tbody>
                </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="homework">


                <table class="table table-striped datatable">
                    <thead>
                    <tr>
                        <th><?=__('homework')?></th>
                        <th><?=__('created-on')?></th>
                        <th><?=__('due-date')?></th>
                        <th><?=__('created-by')?></th>
                        <th><?=__('passmark')?></th>
                        <th><?=__('submissions')?></th>
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
                            </tr>

                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>










@endsection