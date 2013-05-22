{extends file=$schbasSettingsParent}{block name=content}
<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
<script type="text/javascript"
		src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js">
</script>
<script type="text/javascript">

$(function () {

	$('#gradeValueSelection').on('click', function(event) {
		var templateId = $(this).children('option:selected').val();
		var textIdOne = 'textOne';
		var textIdTwo = 'textTwo';
		var textIdThree = 'textThree';
		$.ajax({
			type: "POST",
			url: 'index.php?section=Schbas|SchbasSettings&action=fetchTextsAjax',
			data: {
				'templateId': templateId,
				'textId': textIdOne
			},
			success: function(data) {
				if(data == 'errorFetchTemplate') {
					alert('Konnte das Template nicht abrufen!');
				}
				templateData = $.parseJSON(data);
				CKEDITOR.instances['messagetext'].setData(templateData.text);
				$('#messagetitle').val(templateData.title);
			},
			error: function(data) {
				alert('Konnte das Template nicht abrufen!');
			}
		});
		
		
		$.ajax({
			type: "POST",
			url: 'index.php?section=Schbas|SchbasSettings&action=fetchTextsAjax',
			data: {
				'templateId': templateId,
				'textId': textIdTwo
			},
			success: function(data) {
				if(data == 'errorFetchTemplate') {
					alert('Konnte das Template nicht abrufen!');
				}
				templateData = $.parseJSON(data);
				CKEDITOR.instances['messagetext2'].setData(templateData.text);
				$('#messagetitle2').val(templateData.title);
			},
			error: function(data) {
				alert('Konnte das Template nicht abrufen!');
			}
		});
		
		$.ajax({
			type: "POST",
			url: 'index.php?section=Schbas|SchbasSettings&action=fetchTextsAjax',
			data: {
				'templateId': templateId,
				'textId': textIdThree
			},
			success: function(data) {
				if(data == 'errorFetchTemplate') {
					alert('Konnte das Template nicht abrufen!');
				}
				templateData = $.parseJSON(data);
				CKEDITOR.instances['messagetext3'].setData(templateData.text);
				$('#messagetitle3').val(templateData.title);
			},
			error: function(data) {
				alert('Konnte das Template nicht abrufen!');
			}
		});
		
	});
});
</script>

<form action="index.php?section=Schbas|SchbasSettings&action=9"	method="post">
	
	
	
		<label>Bitte Jahrgang ausw&auml;hlen:
			<select id="gradeValueSelection" name="template">			
					<option value="5">5</option>
					<option value="6">6</option>
					<option value="7">7</option>
					<option value="8">8</option>
					<option value="9">9</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
			</select>
		</label>
	
	<br />
	<label>Titel erster Text:<input id="messagetitle" type="text" name="messagetitle" value="" /></label><br />
	<label>Text 1:<textarea class="ckeditor" name="messagetext"></textarea></label><br /><br />
	<label>Titel zweiter Text:<input id="messagetitle2" type="text" name="messagetitle" value="" /></label><br />
	<label>Text 2:<textarea class="ckeditor" name="messagetext2"></textarea></label><br /><br />
	<label>Titel dritter Text:<input id="messagetitle3" type="text" name="messagetitle" value="" /></label><br />
	<label>Text 3:<textarea class="ckeditor" name="messagetext3"></textarea></label><br /><br />
	
	<input id="submit"type="submit" value="Submit" />
</form>

{/block}