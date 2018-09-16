<?php 
/** Fenom template 'task.tpl' compiled at 2018-09-03 20:30:03 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>
		
	<?php
/* task.tpl:4: {$title} */
 echo (isset($var["title"]) ? $var["title"] : null); ?> / <?php
/* task.tpl:4: {parent} */
 ?>Система контроля задач

	</title>
	
		<link rel="stylesheet" href="/assets/css/bootstrap.min.css">
	
</head>
<body>
	
		<?php
/* _base.tpl:15: {include '_navbar.tpl'} */
 $tpl->getStorage()->getTemplate('_navbar.tpl')->display($var); ?>
	
	<div class="container">
		<div class="row">
			<div class="col-md-10">
				
	<?php
/* task.tpl:8: {if $items} */
 if((isset($var["items"]) ? $var["items"] : null)) { ?>
		<div id="task-wrapper">
		    <div id="add_task_button" class="text-right">
			    <button class="btn btn-default" id="btn1">Добавить задачу</button>
			</div>
			<div id="task-items">
				<?php
/* task.tpl:14: {insert '_task.tpl'} */
 ?><?php $t55433705_1 = (isset($var["items"]) ? $var["items"] : null); if(is_array($t55433705_1) && count($t55433705_1) || ($t55433705_1 instanceof \Traversable)) {
  foreach($t55433705_1 as $var["item"]) { ?>
	<div class="task">
		<h3 style="background-color:#337ab7;"><a href="/task/<?php
/* _task.tpl:3: {$item.alias} */
 echo (isset($var["item"]["alias"]) ? $var["item"]["alias"] : null); ?>" style="color:white;"><?php
/* _task.tpl:3: {$item.name} */
 echo (isset($var["item"]["name"]) ? $var["item"]["name"] : null); ?></a></h3>
		<div class="text-right">
		    <?php
/* _task.tpl:5: {if $item.status == 'working'} */
 if((isset($var["item"]["status"]) ? $var["item"]["status"] : null) == 'working') { ?>
		    <button id="complete_task_<?php
/* _task.tpl:6: {$item.id} */
 echo (isset($var["item"]["id"]) ? $var["item"]["id"] : null); ?>" class="btn btn-default btn-xs complete-task-button">Завершить</button>
			<?php
/* _task.tpl:7: {/if} */
 } ?>
			<button id="edit_task_<?php
/* _task.tpl:8: {$item.id} */
 echo (isset($var["item"]["id"]) ? $var["item"]["id"] : null); ?>" class="btn btn-default btn-xs edit-task-button">Редактировать</button>
			<button id="delete_task_<?php
/* _task.tpl:9: {$item.id} */
 echo (isset($var["item"]["id"]) ? $var["item"]["id"] : null); ?>" class="btn btn-default btn-xs delete-task-button">Удалить</button>
		</div>
		<div>
		<p>Дата создания: <?php
/* _task.tpl:12: {$item.creation_date} */
 echo (isset($var["item"]["creation_date"]) ? $var["item"]["creation_date"] : null); ?></p>
		<p>Статус: <?php
/* _task.tpl:13: {$item.status_text} */
 echo (isset($var["item"]["status_text"]) ? $var["item"]["status_text"] : null); ?></p>
		<?php
/* _task.tpl:14: {if $item.status == 'completed'} */
 if((isset($var["item"]["status"]) ? $var["item"]["status"] : null) == 'completed') { ?>
		<p>Дата завершения: <?php
/* _task.tpl:15: {$item.completed_date} */
 echo (isset($var["item"]["completed_date"]) ? $var["item"]["completed_date"] : null); ?>
		<?php
/* _task.tpl:16: {/if} */
 } ?>
		<p>Приоритет: <?php
/* _task.tpl:17: {$item.priority_text} */
 echo (isset($var["item"]["priority_text"]) ? $var["item"]["priority_text"] : null); ?></p>
		<p>Описание:</p>
		<p><?php
/* _task.tpl:19: {$item.description} */
 echo (isset($var["item"]["description"]) ? $var["item"]["description"] : null); ?></p>
		<?php
/* _task.tpl:20: {if $item.cut} */
 if((isset($var["item"]["cut"]) ? $var["item"]["cut"] : null)) { ?>
			<a href="/task/<?php
/* _task.tpl:21: {$item.alias} */
 echo (isset($var["item"]["alias"]) ? $var["item"]["alias"] : null); ?>" class="btn btn-default">Читать далее &rarr;</a>
		<?php
/* _task.tpl:22: {/if} */
 } ?>
		</div>
	</div>
<?php
/* _task.tpl:25: {/foreach} */
   } } ?><?php ?>
			</div>
			<div id="task-pagination">
				<?php
/* task.tpl:17: {if $pagination} */
 if((isset($var["pagination"]) ? $var["pagination"] : null)) { ?>
					<?php
/* task.tpl:18: {insert '_pagination.tpl'} */
 ?><nav>
	<ul class="pagination">
		<?php $t5617b633_1 = (isset($var["pagination"]) ? $var["pagination"] : null); if(is_array($t5617b633_1) && count($t5617b633_1) || ($t5617b633_1 instanceof \Traversable)) {
  foreach($t5617b633_1 as $var["page"] => $var["type"]) { ?>
			<?php
/* _pagination.tpl:14: {/switch} */
 $t5617b633_2 = strval((isset($var["type"]) ? $var["type"] : null));
if($t5617b633_2 == 'first') {
?>
				<li><a href="/task/">&laquo;</a></li>
			<?php
} elseif($t5617b633_2 == 'last') {
?>
				<li><a href="/task/<?php
/* _pagination.tpl:8: {$page} */
 echo (isset($var["page"]) ? $var["page"] : null); ?>/">&raquo;</a></li>
			<?php
} elseif($t5617b633_2 == 'less') {
?>
			<?php
} elseif($t5617b633_2 == 'more') {
?>
			<?php
} elseif($t5617b633_2 == 'current') {
?>
				<li class="active"><a href="/task/<?php
/* _pagination.tpl:11: {$page} */
 echo (isset($var["page"]) ? $var["page"] : null); ?>/"><?php
/* _pagination.tpl:11: {$page} */
 echo (isset($var["page"]) ? $var["page"] : null); ?></a></li>
			<?php
} else {
?>
				<li><a href="/task/<?php
/* _pagination.tpl:13: {$page} */
 echo (isset($var["page"]) ? $var["page"] : null); ?>/"><?php
/* _pagination.tpl:13: {$page} */
 echo (isset($var["page"]) ? $var["page"] : null); ?></a></li>
			<?php
}
unset($t5617b633_2) ?>
		<?php
/* _pagination.tpl:15: {/foreach} */
   } } ?>
	</ul>
</nav><?php ?>
				<?php
/* task.tpl:19: {/if} */
 } ?>
			</div>
		</div>
	<?php
/* task.tpl:22: {else} */
 } else { ?>
		<a href="/task/">&larr; Назад</a>
		<?php
/* task.tpl:24: {parent} */
 ?>
					<?php
/* _base.tpl:21: {if $longtitle != ''} */
 if((isset($var["longtitle"]) ? $var["longtitle"] : null) != '') { ?>
						<h3><?php
/* _base.tpl:22: {$longtitle} */
 echo (isset($var["longtitle"]) ? $var["longtitle"] : null); ?></h3>
					<?php
/* _base.tpl:23: {elseif $pagetitle != ''} */
 } elseif((isset($var["pagetitle"]) ? $var["pagetitle"] : null) != '') { ?>
						<h3><?php
/* _base.tpl:24: {$pagetitle} */
 echo (isset($var["pagetitle"]) ? $var["pagetitle"] : null); ?></h3>
					<?php
/* _base.tpl:25: {/if} */
 } ?>
					<?php
/* _base.tpl:26: {$content} */
 echo (isset($var["content"]) ? $var["content"] : null); ?>
				
	<?php
/* task.tpl:25: {/if} */
 } ?>

			</div>
			<div class="col-md-2">
				
					Сайдбар
				
			</div>
		</div>
	</div>
</body>
<footer>
	
		<script src="/assets/js/jquery-2.1.4.min.js"></script>
		<script src="/assets/js/bootstrap.min.js"></script>
		<script src="/assets/js/main.js"></script>
	
</footer>
</html><?php
}, array(
	'options' => 2176,
	'provider' => false,
	'name' => 'task.tpl',
	'base_name' => 'task.tpl',
	'time' => 1535995799,
	'depends' => array (
  0 => 
  array (
    '_task.tpl' => 1535754530,
    '_pagination.tpl' => 1535744423,
    '_base.tpl' => 1535627034,
    'task.tpl' => 1535995799,
  ),
),
	'macros' => array(),

        ));
