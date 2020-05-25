@extends('admin.report.report')
@section('content')



    <div>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab"><?=__('Report')?></a></li>
            <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><?=__('Totals')?></a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home">
                <div class="table-responsive">
                <table class="table table-striped datatable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?=__('classes')?></th>
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

                </table>
            </div>
        </div>

    </div>










@endsection