/* jQuery Action for QuicKe's Admin Section
 * Written by : Nimrod Tsabari | omniWP
 * @ 16.09.2012
 */

jQuery(function($) {
	var keyFields = $('.quickeys-record-key');
	
	keyFields.each(function() {
		var keyCode = $(this).next('.quickeys-keep-key').val();
		if ((48 <= keyCode) && (90 >= keyCode)) $(this).val(String.fromCharCode(keyCode));
		if (keyCode == 35) $(this).val('end');
		if (keyCode == 36) $(this).val('begin');
		if (keyCode == 37) $(this).val('left');
		if (keyCode == 38) $(this).val('up');
		if (keyCode == 39) $(this).val('right');
		if (keyCode == 40) $(this).val('down');
		if (keyCode == 32) $(this).val('');
	});
	
	$('.quickeys-record-key').live('blur', function() { 
		var keyCodes = $('.quickeys-keep-key');
		var uniCodes = new Array();
		var dupCode = false;
		keyCodes.each(function() {
			if ($(this).val() != '32') {
				if ($.inArray($(this).val(),uniCodes) != -1) {
					dupCode = true;
				} else {
					uniCodes.push($(this).val());
				}
			}
		});
		
		console.log(uniCodes);
		
		if (dupCode) {
			$('#quickeys_error').html('You got a duplicate key there....');
			$('#quickeys_error_tr').show();
			updateKeymap();
		} else {
			$('#quickeys_error').html('');
			$('#quickeys_error_tr').hide();
			updateKeymap();
		}
	});

	$('.quickeys-record-key').live('keyup', function(e) {
		if ((((35 <= e.which) && (90 >= e.which)) && (e.which != 45) && (e.which != 46)) || (e.which == 32)) { 
			$(this).next('.quickeys-keep-key').val(e.which);
			if ((48 <= e.which) && (90 >= e.which)) $(this).val(String.fromCharCode(e.which));
			if (e.which == 35) $(this).val('end');
			if (e.which == 36) $(this).val('begin');
			if (e.which == 37) $(this).val('left');
			if (e.which == 38) $(this).val('up');
			if (e.which == 39) $(this).val('right');
			if (e.which == 40) $(this).val('down');
			if (e.which == 32) $(this).val('');
		} else {
			$(this).val('');
		}
	});
	$('.quickeys-record-key-temp').keyup(function(e) {
		if ((((35 <= e.which) && (90 >= e.which)) && (e.which != 45) && (e.which != 46)) || (e.which == 32)) { 
			$(this).next('.quickeys-keep-key-temp').val(e.which);
			if ((48 <= e.which) && (90 >= e.which)) $(this).val(String.fromCharCode(e.which));
			if (e.which == 35) $(this).val('end');
			if (e.which == 36) $(this).val('begin');
			if (e.which == 37) $(this).val('left');
			if (e.which == 38) $(this).val('up');
			if (e.which == 39) $(this).val('right');
			if (e.which == 40) $(this).val('down');
			if (e.which == 32) $(this).val('');
		} else {
			$(this).val('');
		}
	});

	$('.remove_tr').live('click',function() {
		$(this).parents('tr').remove();
		duplicateKeys(); 
	});

	$('.quickeys-add-pid-button').click(function() {
		var idType = $(this).siblings(".quickeys_type_id").val();
		var pids = parseInt($('#quickeys_pids').val());
		
		switch(idType) {
		case 'page':
			var pkc = parseInt($('#key_page_code').val());
			var ppid = parseInt($('#key_page_id').val());
			ajaxSubmit(pids,pkc,ppid,idType); 
		break;
		case 'post':
			var pkc = parseInt($('#key_post_code').val());
			var ppid = parseInt($('#key_post_id').val());
			ajaxSubmit(pids,pkc,ppid,idType); 
		break;
		case 'cat':
			var pkc = parseInt($('#key_cat_code').val());
			var ppid = parseInt($('#key_cat_id').val());
			ajaxSubmit(pids,pkc,ppid,idType); 
		break;
		default:
		  
		}
	});
			 
	function ajaxSubmit(pids,pkc,ppid,idtype){
		$.ajax({
			type:"POST",
			url: "/wp-admin/admin-ajax.php",
			data: {
				action: 'quickeys_ajax_add_new_pid_field',
				pids: pids,
				pkc: pkc,
				ppid: ppid,
				idtype: idtype
			},
			success:function(data){
				var pids = parseInt($('#quickeys_pids').val());
				pids = pids + 1;
				$('#quickeys_pids').val(pids);
				$('#quickeys_keymap').append(data);
				updateKeymap();
				duplicateKeys();
			}
		});
	 
	return false;
	}
	
	function duplicateKeys() {
		var keyCodes = $('.quickeys-keep-key');
		var uniCodes = new Array();
		var dupCode = false;
		keyCodes.each(function() {
			if ($(this).val() != 32) {
				if ($.inArray($(this).val(),uniCodes) != -1) {
					dupCode = true;
				} else {
					uniCodes.push($(this).val());
				}
			}
		});
		
		if (dupCode) {
			$('#quickeys_error').html('You got a duplicate key there....');
			$('#quickeys_error_tr').show();
		} else {
			$('#quickeys_error').hide();
			$('#quickeys_error_tr').html('');
		}
	}

	function updateKeymap() {
		var keyFields = $('.quickeys-record-key');
		keyFields.each(function() {
			var keyCode = $(this).next('.quickeys-keep-key').val();
			if ((48 <= keyCode) && (90 >= keyCode)) $(this).val(String.fromCharCode(keyCode));
			if (keyCode == 35) $(this).val('end');
			if (keyCode == 36) $(this).val('begin');
			if (keyCode == 37) $(this).val('left');
			if (keyCode == 38) $(this).val('up');
			if (keyCode == 39) $(this).val('right');
			if (keyCode == 40) $(this).val('down');
			if (keyCode == 32) $(this).val('');
		});
	}
});
