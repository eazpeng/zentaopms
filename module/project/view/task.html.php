<?php
/**
 * The task view file of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     project
 * @version     $Id: task.html.php 4894 2013-06-25 01:28:39Z wyd621@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
include '../../common/view/header.html.php';
include '../../common/view/chart.html.php';
include '../../common/view/datepicker.html.php';
include '../../common/view/datatable.fix.html.php';
include './taskheader.html.php';
js::set('moduleID', $moduleID);
js::set('productID', $productID);
js::set('projectID', $projectID);
js::set('browseType', $browseType);
?>
<div class='side' id='taskTree'>
  <a class='side-handle' data-id='projectTree'><i class='icon-caret-left'></i></a>
  <div class='side-body'>
    <div class='panel panel-sm'>
      <div class='panel-heading nobr'>
        <?php echo html::icon($lang->icons['project']);?> <strong><?php echo $project->name;?></strong>
      </div>
      <div class='panel-body'>
        <?php echo $moduleTree;?>
        <div class='text-right'>
          <?php common::printLink('project', 'edit',    "projectID=$projectID", $lang->edit);?>
          <?php common::printLink('project', 'delete',  "projectID=$projectID&confirm=no", $lang->delete, 'hiddenwin');?>
          <?php common::printLink('tree', 'browsetask', "rootID=$projectID&productID=0", $lang->tree->manage);?>
        </div>
      </div>
    </div>
  </div>
</div>
<div class='main'>
  <script>setTreeBox();</script>
  <form method='post' id='projectTaskForm'>
    <?php
    $datatableId  = $this->moduleName . ucfirst($this->methodName);
    $useDatatable = (isset($this->config->datatable->$datatableId->mode) and $this->config->datatable->$datatableId->mode == 'datatable');
    $vars         = "projectID=$project->id&status=$status&parma=$param&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage";

    if($useDatatable) include '../../common/view/datatable.html.php';
    $setting = $this->datatable->getSetting('project');
    $widths  = $this->datatable->setFixedFieldWidth($setting);
    $columns = 0;
    ?>
    <table class='table table-condensed table-hover table-striped tablesorter table-fixed <?php echo $useDatatable ? 'datatable' : ''?>' id='taskList' data-checkable='true' data-fixed-left-width='<?php echo $widths['leftWidth']?>' data-fixed-right-width='<?php echo $widths['rightWidth']?>' data-custom-menu='true' data-checkbox-name='taskIDList[]'>
      <thead>
        <tr><?php
        foreach ($setting as $key => $value)
        {
            if($value->show)
            {
                $this->datatable->printHead($value, $orderBy, $vars);
                $columns++;
            }
        }
        ?></tr>
      </thead>
      <tbody>
        <?php foreach($tasks as $task):?>
        <tr class='text-center' data-id='<?php echo $task->id?>'>
          <?php foreach ($setting as $key => $value) $this->task->printCell($value, $task, $users, $browseType, $branchGroups, $modulePairs, $useDatatable ? 'datatable' : 'table');?>
        </tr>
          <?php
            if(!empty($task->children))
            {
              $childrenNum = count($task->children);
              foreach($task->children as $key=>$child)
              {
          ?>
            <tr class='text-center table-children<?php if($key==0) echo ' table-child-top';?><?php if(($key+1) == $childrenNum) echo ' table-child-bottom';?> parent-<?php echo $task->id;?>' data-id='<?php echo $child->id?>'>
              <?php foreach ($setting as $key => $value) $this->task->printCell($value, $child, $users, $browseType, $branchGroups, $modulePairs, $useDatatable ? 'datatable' : 'table', true);?>
            </tr>
            <?php }?>
          <?php }?>
        <?php endforeach;?>
      </tbody>
      <tfoot>
        <tr>
          <?php if(!isset($columns)) $columns = ($this->cookie->windowWidth > $this->config->wideSize ? 15 : 13) - ($project->type == 'sprint' ? 0 : 1);?>
          <td colspan='<?php echo $columns;?>'>
            <div class='table-actions clearfix'>
            <?php 
            $canBatchEdit         = common::hasPriv('task', 'batchEdit', !empty($task) ? $task : null);
            $canBatchClose        = (common::hasPriv('task', 'batchClose', !empty($task) ? $task : null) && strtolower($browseType) != 'closedBy');
            $canBatchCancel       = common::hasPriv('task', 'batchCancel', !empty($task) ? $task : null);
            $canBatchChangeModule = common::hasPriv('task', 'batchChangeModule', !empty($task) ? $task : null);
            $canBatchAssignTo     = common::hasPriv('task', 'batchAssignTo', !empty($task) ? $task : null);
            if(count($tasks))
            {
                echo html::selectButton();

                $actionLink = $this->createLink('task', 'batchEdit', "projectID=$projectID");
                $misc       = $canBatchEdit ? "onclick=\"setFormAction('$actionLink', '', '#projectTaskForm')\"" : "disabled='disabled'";

                echo "<div class='btn-group dropup'>";
                echo html::commonButton($lang->edit, $misc);
                echo "<button id='moreAction' type='button' class='btn dropdown-toggle' data-toggle='dropdown'><span class='caret'></span></button>";
                echo "<ul class='dropdown-menu' id='moreActionMenu'>";

                $actionLink = $this->createLink('task', 'batchClose');
                $misc = $canBatchClose ? "onclick=\"setFormAction('$actionLink', 'hiddenwin', '#moreAction')\"" : "class='disabled'";
                echo "<li>" . html::a('#', $lang->close, '', $misc) . "</li>";

                $actionLink = $this->createLink('task', 'batchCancel');
                $misc = $canBatchCancel ? "onclick=\"setFormAction('$actionLink', 'hiddenwin', '#moreAction')\"" : "class='disabled'";
                echo "<li>" . html::a('#', $lang->task->cancel, '', $misc) . "</li>";

                if($canBatchChangeModule)
                {
                    $withSearch = count($modules) > 10;
                    echo "<li class='dropdown-submenu'>";
                    echo html::a('javascript:;', $lang->task->moduleAB, '', "id='moduleItem'");
                    echo "<div class='dropdown-menu" . ($withSearch ? ' with-search':'') . "'>";
                    echo '<ul class="dropdown-list">';
                    foreach($modules as $moduleId => $module)
                    {
                        $actionLink = $this->createLink('task', 'batchChangeModule', "moduleID=$moduleId");
                        echo "<li class='option' data-key='$moduleID'>" . html::a('#', $module, '', "onclick=\"setFormAction('$actionLink', 'hiddenwin', '#moreAction')\"") . "</li>";
                    }
                    echo '</ul>';
                    if($withSearch) echo "<div class='menu-search'><div class='input-group input-group-sm'><input type='text' class='form-control' placeholder=''><span class='input-group-addon'><i class='icon-search'></i></span></div></div>";
                    echo '</div></li>';
                }
                else
                {
                    echo '<li>' . html::a('javascript:;', $lang->task->moduleAB, '', $misc) . '</li>';
                }

                /* Batch assign. */
                if($canBatchAssignTo)
                {
                    $withSearch = count($memberPairs) > 10;
                    $actionLink = $this->createLink('task', 'batchAssignTo', "projectID=$projectID");
                    echo html::select('assignedTo', $memberPairs, '', 'class="hidden"');
                    echo "<li class='dropdown-submenu'>";
                    echo html::a('javascript::', $lang->task->assignedTo, 'id="assignItem"');
                    echo "<div class='dropdown-menu" . ($withSearch ? ' with-search':'') . "'>";
                    echo '<ul class="dropdown-list">';
                    foreach ($memberPairs as $key => $value)
                    {
                        if(empty($key)) continue;
                        echo "<li class='option' data-key='$key'>" . html::a("javascript:$(\".table-actions #assignedTo\").val(\"$key\");setFormAction(\"$actionLink\", \"hiddenwin\", \"#moreAction\")", $value, '', '') . '</li>';
                    }
                    echo "</ul>";
                    if($withSearch) echo "<div class='menu-search'><div class='input-group input-group-sm'><input type='text' class='form-control' placeholder=''><span class='input-group-addon'><i class='icon-search'></i></span></div></div>";
                    echo "</div></li>";
                }
                echo "</ul></div>";
            }
            echo "<div class='text'>" . $summary . "</div>";
            ?>
            </div>
            <?php $pager->show();?>
          </td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<?php js::set('replaceID', 'taskList')?>
<script>
$('#project<?php echo $projectID;?>').addClass('active')
$('#listTab').addClass('active')
$('#<?php echo ($browseType == 'bymodule' and $this->session->taskBrowseType == 'bysearch') ? 'all' : $this->session->taskBrowseType;?>Tab').addClass('active');
<?php if($browseType == 'bysearch'):?>
$shortcut = $('#QUERY<?php echo (int)$param;?>Tab');
if($shortcut.size() > 0)
{
    $shortcut.addClass('active');
    $('#bysearchTab').removeClass('active');
    $('#querybox').removeClass('show');
}
<?php endif;?>
statusActive = '<?php echo isset($lang->project->statusSelects[$this->session->taskBrowseType]);?>';
if(statusActive) $('#statusTab').addClass('active')
<?php if(isset($this->config->project->homepage) and $this->config->project->homepage != 'browse'):?>
$('#modulemenu .nav li.right:last').after("<li class='right'><a style='font-size:12px' href='javascript:setHomepage(\"project\", \"browse\")'><i class='icon icon-cog'></i> <?php echo $lang->homepage?></a></li>")
<?php endif;?>
</script>
<?php include '../../common/view/footer.html.php';?>
