var HOME = BASE_URL + 'orders/order_invoice/';

function goBack() {
	window.location.href = HOME;
}

function addNew() {
	window.location.href = HOME + 'add_new';
}


function goEdit(code) {
	window.location.href = HOME + 'edit/'+code;
}


function view_detail(code) {
	window.location.href = HOME + 'view_detail/'+code;
}


function print_invoice() {
	//--- properties for print
	var prop 			= "width=800, height=900. left="+center+", scrollbars=yes";
	var center    = ($(document).width() - 800)/2;
	var code = $('#code').val();
	var target  = HOME + 'print_invoice/'+code;
  window.open(target, '_blank', prop);
}

function clearFilter() {
	$.get(HOME+'clear_filter', function() {
		goBack();
	});
}


$('#from_date').datepicker({
	dateFormat:'dd-mm-yy',
	onClose:function(sd) {
		$('#to_date').datepicker('option', 'minDate', sd);
	}
})

$('#to_date').datepicker({
	dateFormat:'dd-mm-yy',
	onClose:function(sd) {
		$('#from_date').datepicker('option', 'maxDate', sd);
	}
})


$('#doc_date').datepicker({
	dateFormat:'dd-mm-yy'
});


$('#customer_code').autocomplete({
	source:BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus:true,
	close:function() {
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if(arr.length == 2) {
			$('#customerCode').val(arr[0]); //--- for check with customer
			$('#customer_code').val(arr[0]);
			$('#customer_name').val(arr[1]);

			getAddressBillTo(arr[0]);
		}
		else {
			$('#customerCode').val('');
			$('#customer_code').val('');
			$('#customer_name').val('');
		}
	}
})


function getAddressBillTo(code) {
	$.ajax({
		url:BASE_URL + 'masters/address/get_customer_bill_to_address',
		type:'GET',
		cache:false,
		data:{
			'customer_code' : code
		},
		success:function(rs) {
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var ds = $.parseJSON(rs);
				$('#branch_code').val(ds.branch_code);
				$('#branch_name').val(ds.branch_name);
				$('#address').val(ds.address);
				$('#phone').val(ds.phone);
			}
		}
	})
}


function add() {
	var doc_date = $('#doc_date').val();
	var customerCode = $.trim($('#customerCode').val());
	var customer_code = $.trim($('#customer_code').val());
	var customer_name = $.trim($('#customer_name').val());
	var branch_code = $.trim($('#branch_code').val());
	var branch_name = $.trim($('#branch_name').val());
	var address = $.trim($('#address').val());
	var vat_type = $('#vat_type').val();
	var phone = $.trim($('#phone').val());
	var remark = $.trim($('#remark').val());

	if(customer_code.length === 0 || (customer_code != customerCode)) {
		swal("รหัสลูกค้าไม่ถูกต้อง");
		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'doc_date' : doc_date,
			'customer_code' : customer_code,
			'customer_name' : customer_name,
			'branch_code' : branch_code,
			'branch_name' : branch_name,
			'address' : address,
			'vat_type' : vat_type,
			'phone' : phone,
			'remark' : remark
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var ds = $.parseJSON(rs);
				if(ds.status === 'success') {
					goEdit(ds.code);
				}
				else {
					swal({
						title:'Error!',
						text:ds.message,
						type:'error'
					});
				}
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		}
	})
}


function getEdit(){
	$('.edit').removeAttr('disabled');
	$('#btn-edit').addClass('hide');
	$('#btn-update').removeClass('hide');
}

function updateHeader(){
	var code = $('#code').val();
	var doc_date = $('#doc_date').val();
	var customerCode = $.trim($('#customerCode').val());
	var customer_code = $.trim($('#customer_code').val());
	var customer_name = $.trim($('#customer_name').val());
	var branch_code = $.trim($('#branch_code').val());
	var branch_name = $.trim($('#branch_name').val());
	var address = $.trim($('#address').val());
	var phone = $.trim($('#phone').val());
	var vat_type = $('#vat_type').val();
	var remark = $.trim($('#remark').val());

	if(customer_code.length === 0 || (customer_code != customerCode)) {
		swal("รหัสลูกค้าไม่ถูกต้อง");
		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'doc_date' : doc_date,
			'customer_code' : customer_code,
			'customer_name' : customer_name,
			'branch_code' : branch_code,
			'branch_name' : branch_name,
			'address' : address,
			'vat_type' : vat_type,
			'phone' : phone,
			'remark' : remark
		},
		success:function(rs) {
			var rs = $.trim(rs);
			if(rs === 'success') {
				swal({
					title:'Updated',
					type:'success',
					timer:1000
				});

				setTimeout(function(){
					window.location.reload();
				}, 1200);
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		}
	})
}


function getSearch() {
	$('#searchForm').submit();
}
