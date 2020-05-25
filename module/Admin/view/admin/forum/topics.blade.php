@if($lecture)
<div>
    <p class="lead">
        <?=__('lecture')?>: {{$lecture->lecture_title}}
    </p>
</div>
@endif
<!--breadcrumb-section ends-->
<!--container starts-->
<div class="container_" style="background-color: white; min-height: 400px;   padding-bottom:50px; margin-bottom: 10px;   " >
    <!--primary starts-->

    <div class="box-body">
        <div>
            <a target="{{ @$target }}" class="btn btn-primary" href="{{ $_url('admin/default',['controller'=>'forum','action'=>'addtopic','id'=>$id])}}"><i class="fa fa-plus"> Add Topic</i></a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th><?=__('topic')?></th>
                    <th><?=__('created-by')?></th>
                    <th><?=__('added-on')?></th>
                    <th ><?=__('replies')?></th>
                    <th><?=__('last-reply')?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($topics as $row): ?>

                <tr>
                    <td><?php echo $row->topic_title; ?></td>
                    <td>
                        <?php echo forumUser($row->topic_owner,$row->topic_owner_type)['name'] ?>
                    </td>
                    <td><?=date('d/M/Y',$row->created_on)?></td>
                    <td><?=($row->forumPosts->count()-1) ?></td>
                    <td><?php if($row->forumPosts->count()-1 > 0):?>
                        <?=date('D, d M Y g:i a',$row->forumPosts()->orderBy('forum_post_id','desc')->first()->post_created_on); ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-right">
                        <a  target="{{ @$target }}"  class="btn btn-primary" href="<?=$_url('application/default',['controller'=>'forum','action'=>'topic','id'=>$row->forum_topic_id])?>"><?=__('view')?></a>

                        @if($student->student_id==$row->topic_owner && $row->topic_owner_type=='s')
                            <a onclick="return confirm('<?=__('topic-delete-confirm')?>')" class="btn btn-danger" href="{{ $_url('application/default',['controller'=>'forum','action'=>'deletetopic','id'=>$row->forum_topic_id]) }}"><?=__('delete-topic')?></a>
                        @endif
                    </td>
                </tr>

                <?php endforeach; ?>

                </tbody>
            </table>
        </div>
        <?php
        // add at the end of the file after the table
        echo $topics->links();
        ?>
    </div>


</div>

<!--container ends-->
