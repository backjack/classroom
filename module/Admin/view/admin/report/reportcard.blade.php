<!DOCTYPE html><html  <?=langMeta()?>>

<head>
    <title><?=__('report-card')?></title>
    <!-- BEGIN META -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="your,keywords">
    <meta name="description" content="Short explanation about this website">
    <!-- END META -->

    <style>
        * { font-family: DejaVu Sans, sans-serif; }
    </style>

    <!-- END STYLESHEETS -->

    <style>
        .fadedtext{
            font-size: 8px;
            color: #d9d9d9;
        }
        .table {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        .table td, .table th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .table tr:nth-child(even){background-color: #f2f2f2;}

        .table tr:hover {background-color: #ddd;}

        .table th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #4CAF50;
            color: white;
        }
    </style>




</head>


<body>
<div class="container">
    <div style="text-align: center">
    <?php $logo = $viewHelper->get('getSetting')('image_logo'); if(!empty($logo)):?>
    <img style="max-height: 100px" class="img-responsive" src="public/<?php echo $logo; ?>">
    <?php endif; ?>
    </div>
    <h1 style="text-align: center;">{{ $viewHelper->get('getSetting')('general_site_name') }}</h1>

    <h2 style="text-align: center"><?=__('student')?> <?=__('report-card')?></h2>
    <table class="table table-striped">
        <tr>
            <td><?=__('student')?></td>
            <td>{{ $student->first_name }} {{ $student->last_name }}</td>
        </tr>
        <tr>
            <td>
                <?=__('session-course')?>:
            </td>
            <td>
                {{ $session->session_name }}
            </td>
        </tr>
    </table>

    <h4><?=strtoupper(__('results'))?></h4>
    <table class="table table-striped">
        <thead>
        <tr>
            <th><?=__('test')?></th>
            <th><?=__('passmark')?></th>
            <th><?=__('score')?></th>
            <th><?=__('grade')?></th>
            <th><?=__('status')?></th>
        </tr>
        </thead>
        @foreach($tests as $test)
            <tr>
                <td>{{ $test->name }}</td>
                <td>{{ $test->passmark }}%</td>
                <td>     @php $result =$test->studentTests()->where('student_id',$student->student_id)->orderBy('score','desc')->first() @endphp
                    @if($result)
                        {{ round($result->score,1) }}%
                    @endif
                </td>
                <td>
                    @if($result)
                    {{ $testGradeTable->getGrade($result->score) }}
                    @endif

                </td>
                <td>
                    @if($result && $result->score >= $test->passmark)
                        <span style="color: green"><?=__('passed')?></span>
                        @else
                        <span style="color: red"><?=__('failed')?></span>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>

    <h4>Total</h4>
    <table class="table table-striped">
        @php $stats = $controller->getStudentTestsStats($student->student_id); @endphp
        <tr>
            <td style="width: 30%;"><?=__('average-score')?>:</td>
            <td>{{ round($stats['average'],1) }}%</td>
        </tr>
        <tr>
            <td><?=__('average-grade')?>:</td>
            <td>{{ $testGradeTable->getGrade($stats['average']) }}</td>
        </tr>
    </table>

</div>

</body>

</html>