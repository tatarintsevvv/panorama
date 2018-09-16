{extends '_base.tpl'}

{block 'content'}
	<div class="jumbotron">
		{parent}
	</div>
	<form action="upload_file.php" method="post" enctype="multipart/form-data" role="form">
      <div class="form-group col-sm-6" style="padding-right: 10px;">
        <input type="file" class="input-group-sm" name="file" id="file" accept="application/zip|application/x-zip-compressed" class="form-control" data-filename-placement="inside" title="Выбрать файл" />
      </div>
      <div class="form-group col-sm-4" style="padding-left: 10px;">
        <button type="submit" class="btn" name="submit">Конвертировать</button>
      </div>
    </form>
{/block}

