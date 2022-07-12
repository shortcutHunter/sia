// var isByPass = false;

$( document ).ready(function() {

	let jqueryEvent = "input keydown keyup mousedown mouseup select contextmenu drop focusout";

	function validateForm (required_fields) {

		let is_valid = true;

		// if (isByPass) {
		// 	return true;
		// }

		required_fields.each(function(i, v){
			if (['text', 'textarea', 'select-one'].includes(v.type)) {
				if (v.value && !$(v).hasClass('error-validation')) {
					$(v).removeClass('invalid');
				}else{
					is_valid = false;
					if (!$(v).hasClass('invalid')) {
						$(v).addClass('invalid');
					}
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
	    	if (v.files && v.files.length > 0 && !$(v).hasClass('error-validation')) {
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

	$('.custom-file-input').attr('accept', 'image/jpeg,image/gif,image/png,application/pdf');

	$('.custom-file-input').on('change', function() {
		var self = this;
		var is_valid = true;
		$.each(this.files, function(i, v){

			if (!v.type.includes('image') && !v.type.includes('pdf')) {
				$(self).val('');
				addTextError('Maaf, file anda harus image atau pdf', self);
				if (!$(self).parent().hasClass('invalid')) {
					$(self).parent().addClass('invalid');
				}
				is_valid = false;
			}

			if (v.size > 512000) {
				$(self).val('');
				addTextError('Maaf, file anda terlalu besar', self);
				if (!$(self).parent().hasClass('invalid')) {
					$(self).parent().addClass('invalid');
				}
				is_valid = false;
			}
		});

		if (is_valid) {
			$(self).parent().removeClass('invalid');
			removeTextError(self);
		}
	});

	$('.datepicker').datepicker({
		format: 'dd/mm/yyyy',
		autoclose: true
	});

	$("[number]").inputFilter(function(value) {
		return /^\d*$/.test(value);
	});

	$(".nik").on('drop focusout', function(e) {
		let self = this;
		let max_length = $(self).attr('length');
		if (self.value.length != max_length) {
			$(self).addClass('invalid');
			$(self).addClass('error-validation');
			addTextError("NIK harus memiliki panjang " + max_length, self);
		} else {
			$.post('/register/check/nik', {'nik': self.value}, function(response){ 
				if (response) {
					$(self).removeClass('invalid');
					$(self).removeClass('error-validation');
					removeTextError(self);
				} else {
					$(self).addClass('invalid');
					$(self).addClass('error-validation');
					addTextError("NIK telah terdaftar harap hubungi admin", self);
				}
			});
		}
	});

	$(".no_hp").on('drop focusout', function(e) {
		let self = this;
		let min_length = $(self).attr('min');
		if (self.value.length < min_length) {
			$(self).addClass('invalid');
			$(self).addClass('error-validation');
			addTextError("No. HP minimal panjang " + min_length, self);
		} else {
			$(self).removeClass('invalid');
			$(self).removeClass('error-validation');
			removeTextError(self);
		}
	});

	$(".uppercase").on(jqueryEvent, function(e) {
		this.value = this.value.toUpperCase();
	});

	$(".email").on('drop focusout', function(e) {
		let self = this;
		let regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if (!regex.test(self.value)) {
			$(self).addClass('invalid');
			$(self).addClass('error-validation');
			addTextError("Email tidak valid", self);
		} else {
			$(self).removeClass('invalid');
			$(self).removeClass('error-validation');
			removeTextError(self);
		}
	});

	$(".currency").on(jqueryEvent, function(e) {
		$(this).val(function(index, value) {
            let endResult = value
              // Keep only digits, decimal points, and dashes at the start of the string:
              .replace(/[^\d,-]|(?!^)-/g, "")
              // Remove duplicated decimal points, if they exist:
              .replace(/^([^,]*\,)(.*$)/, (_, g1, g2) => g1 + g2.replace(/\,/g, ''))
              // Keep only two digits past the decimal point:
              .replace(/\,(\d{2})\d+/, '.$1')
              .replace(".", ",")
              // Add thousands separators:
              .replace(/\B(?=(\d{3})+(?!\d))/g, ".");

            return endResult;
        });
	});


	function addTextError(errMsg, el) {
		if (!$(self).hasClass('error-validation')) {
			$(self).addClass('error-validation');
		}

		if ($(el).nextAll('.error-text').length > 0) {
			$(el).nextAll('.error-text').html(errMsg);
		} else {
			$(el).after('<div class="error-text">'+ errMsg +'</div>');
		}
	}

	function removeTextError(el) {
		if ($(self).hasClass('error-validation')) {
			$(self).removeClass('error-validation');
		}

		if ($(el).nextAll('.error-text').length > 0) {
			$(el).nextAll('.error-text').remove();
		}
	}

});


// function bypassValidation () {
// 	isByPass = true;
// 	$('li.disabled').removeClass('disabled');
// }