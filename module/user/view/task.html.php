<?php
/**
 * The task view file of dashboard module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2010 QingDao Nature Easy Soft Network Technology Co,LTD (www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     dashboard
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/tablesorter.html.php';?>
<table class='table-1 tablesorter' id='tasktable'>
  <thead>
  <tr class='colhead'>
    <th class='w-id'><?php echo $lang->idAB;?></th>
    <th class='w-pri'><?php echo $lang->priAB;?></th>
    <th><?php echo $lang->task->project;?></th>
    <th><?php echo $lang->task->name;?></th>
    <th class='w-hour'><?php echo $lang->task->estimateAB;?></th>
    <th class='w-hour'><?php echo $lang->task->consumedAB;?></th>
    <th class='w-hour'><?php echo $lang->task->leftAB;?></th>
    <th class='w-date'><?php echo $lang->task->deadlineAB;?></th>
    <th class='w-status'><?php echo $lang->statusAB;?></th>
  </tr>
  </thead>   
  <tbody>
  <?php foreach($tasks as $task):?>
  <tr class='a-center'>
    <td><?php echo html::a($this->createLink('task', 'view', "taskID=$task->id"), sprintf('%03d', $task->id));?></td>
    <td><?php echo $lang->task->priList[$task->pri];?></td>
    <td class='nobr'><?php echo html::a($this->createLink('project', 'browse', "projectid=$task->projectID"), $task->projectName);?></th>
    <td class='a-left nobr'><?php echo html::a($this->createLink('task', 'view', "taskID=$task->id"), $task->name);?></td>
    <td><?php echo $task->estimate;?></td>
    <td><?php echo $task->consumed;?></td>
    <td><?php echo $task->left;?></td>
    <td class=<?php if(isset($task->delay)) echo 'delayed';?>><?php if(substr($task->deadline, 0, 4) > 0) echo $task->deadline;?></td>
    <td class='<?php echo $task->status;?>'><?php echo $lang->task->statusList[$task->status];?></td>
  <?php endforeach;?>
  </tbody>
</table> 
<?php include '../../common/view/footer.html.php';?>
