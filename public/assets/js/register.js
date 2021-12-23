$( document ).ready(function() {

	$("#wizard").steps({
		onFinished: function (event, currentIndex) {
		  $("form").submit();
		}
	});

	$('[type=file]').fileselect();

	$('.auto-update').on('change', function(){
		$("#"+this.name+"_text").val(this.value);
	});

	$('.custom-file-input').on('change', function() {
		var self = this;
		$.each(this.files, function(i, v){
			if (v.size > 100000) {
				$(self).val('');
				$(self).next().html("Choose file...");
				alert('Maaf, file anda terlalu besar');
			}
		});
	});

	$('.datepicker').datepicker({format: 'dd/mm/yyyy'});

});