@extends('mails.layout')

@section('content')
<?=__('new-topics-mail',['count'=>count($topics)])?><br/>
<table style="width:100%" class="table-layout">
    <thead>
        <tr>
            <th style="text-align: left;"><?=__('Topic')?></th>
            <th style="text-align: left;"><?=__('Created By')?></th>
        </tr>
    </thead>
    <tbody>
    @foreach($topics as $topic)
        <tr>
            <td><a style="text-decoration: underline" href="{{ $controller->url()->fromRoute($module.'/default',['controller'=>'forum','action'=>'topic','id'=>$topic->forum_topic_id]) }}">{{ $topic->topic_title }}</a></td>
            <td>{{ forumUser($topic->topic_owner,$topic->topic_owner_type)['name'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
