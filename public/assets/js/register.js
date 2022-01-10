$( document ).ready(function() {

	function validateForm (required_fields) {

		is_valid = true;

		required_fields.each(function(i, v){
	          if (['text', 'textarea', 'select-one'].includes(v.type)) {
	            if (v.value) {
	              $(v).removeClass('invalid');
	            }else{
	              is_valid = false;
	              $(v).addClass('invalid');
	            }
	          }else if (['checkbox', 'radio'].includes(v.type)) {
	            // if checkbox/radio have name
	            if (v.name) {
	              var el = $('[name="'+v.name+'"]');
	              var is_checked = $('[name="'+v.name+'"]:checked');
	              if (is_checked.length > 0) {
	                el.removeClass('invalid');
	              }else{
	                is_valid = false;
	                el.addClass('invalid');
	              }
	            // checkbox/radio have no name meaning single checkbox/radio
	            }else{
	              if (v.checked) {
	                $(v).removeClass('invalid');
	              }else{
	                is_valid = false;
	                $(v).addClass('invalid');
	              }
	            }
	          }else if (v.type == 'file') {
	            if (v.files && v.files.length > 0) {
	              $(v).parent().removeClass('invalid');
	            }else{
	              is_valid = false;
	              $(v).parent().addClass('invalid');
	            }
	          }
	        });

		return is_valid;
	}

	$("#wizard").steps({
		onFinished: function (event, currentIndex) {
			let container = $('div .body.current');
			let required_fields = container.find('[required]');
			let is_valid = validateForm(required_fields);			

	        if (is_valid) {
	        	$("form").submit();
	        }
		},
		onStepChanging: function (e, currentIndex, priorIndex) {
			let container = $('div .body.current');
			let required_fields = container.find('[required]');
			let is_valid = validateForm(required_fields);

	        return is_valid;
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