<?php 
/** Fenom template 'addtask.tpl' compiled at 2018-09-04 16:58:43 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>
		
	<?php
/* addtask.tpl:4: {$title} */
 echo (isset($var["title"]) ? $var["title"] : null); ?> / <?php
/* addtask.tpl:4: {parent} */
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
				
    <div id="addtask-wrapper">
        <?php
/* addtask.tpl:9: {parent} */
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
				
        <form role="form" class="form-vertical" action="/task/" method="post">
            <div class="form-group">
                <label for="name" class="control-label col-xs-3">Название:</label>
                <input type="text" name="name" class="form-control" id="FormControlTextareaName" required="required"></textarea>
            </div>
            <div class="form-group">
                <label for="name" class="control-label col-xs-3">Описание:</label>
                <textarea name="description" class="form-control" id="FormControlTextareaDescription" required="required" rows="6"></textarea>
            </div>
            <div id="add_task_button" class="text-right">
	            <button type="submit" class="btn btn-default" id="btn1">Добавить задачу</button>
		    </div>
        </form>
    </div>

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
	'name' => 'addtask.tpl',
	'base_name' => 'addtask.tpl',
	'time' => 1536069429,
	'depends' => array (
  0 => 
  array (
    '_base.tpl' => 1535627034,
    'addtask.tpl' => 1536069429,
  ),
),
	'macros' => array(),

        ));
