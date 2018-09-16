<?php 
/** Fenom template 'home.tpl' compiled at 2018-09-13 10:34:28 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>
		Конвертация zip-архивов
	</title>
	
		<link rel="stylesheet" href="/assets/css/bootstrap.min.css">
	
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-md-10">
				
	<div class="jumbotron">
		<?php
/* home.tpl:5: {parent} */
 ?>
					<?php
/* _base.tpl:18: {if $longtitle != ''} */
 if((isset($var["longtitle"]) ? $var["longtitle"] : null) != '') { ?>
						<h3><?php
/* _base.tpl:19: {$longtitle} */
 echo (isset($var["longtitle"]) ? $var["longtitle"] : null); ?></h3>
					<?php
/* _base.tpl:20: {elseif $pagetitle != ''} */
 } elseif((isset($var["pagetitle"]) ? $var["pagetitle"] : null) != '') { ?>
						<h3><?php
/* _base.tpl:21: {$pagetitle} */
 echo (isset($var["pagetitle"]) ? $var["pagetitle"] : null); ?></h3>
					<?php
/* _base.tpl:22: {/if} */
 } ?>
					<?php
/* _base.tpl:23: {$content} */
 echo (isset($var["content"]) ? $var["content"] : null); ?>
				
	</div>
	<form action="upload_file.php" method="post" enctype="multipart/form-data" role="form">
      <div class="form-group col-sm-6" style="padding-right: 10px;">
        <input type="file" class="input-group-sm" name="file" id="file" accept="application/zip|application/x-zip-compressed" class="form-control" data-filename-placement="inside" title="Выбрать файл" />
      </div>
      <div class="form-group col-sm-4" style="padding-left: 10px;">
        <button type="submit" class="btn" name="submit">Конвертировать</button>
      </div>
    </form>

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
	'name' => 'home.tpl',
	'base_name' => 'home.tpl',
	'time' => 1536767426,
	'depends' => array (
  0 => 
  array (
    '_base.tpl' => 1536747574,
    'home.tpl' => 1536767426,
  ),
),
	'macros' => array(),

        ));
