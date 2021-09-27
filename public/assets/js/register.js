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

});