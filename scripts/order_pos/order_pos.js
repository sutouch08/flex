var HOME = BASE_URL + 'pos/order_pos/';

function goToPOS(id) {
	window.location.href = HOME + 'pos/'+id;
}

function removeItem(id) {
	$('#row-'+id).remove();
	recalTotal();
}

$('#pd-box').autocomplete({
	source:BASE_URL + 'auto_complete/get_item_code_and_name',
	autoFocus:true,
	close:function() {
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if(arr.length == 2) {
			$(this).val(arr[0]);
		}
		else {
			$(this).val('');
		}
	}
})


$('#pd-box').keyup(function(e){
	if(e.keyCode === 13) {
		var code = $(this).val();
		if(code.length) {
			$(this).val('');
			get_product_by_code(code);
		}
	}
})

$('#barcode-box').keyup(function(e){
	if(e.keyCode === 13) {
		var barcode = $.trim($(this).val());

		if(barcode.length) {
			$(this).val('');
			$.ajax({
				url:HOME + 'get_product_by_code',
				type:'GET',
				cache:false,
				data:{
					'code' : barcode
				},
				success:function(rs) {
					if(isJson(rs)) {

						addToOrder(rs);
					}
					else {
						swal({
							title:'Error',
							text:rs,
							type:'error'
						});
					}
				},
				error:function(xhr, status, error) {
					swal({
						title:'Error!',
						text:'Error-'+xhr.status+': '+xhr.statusText,
						type:'error'
					});
				}
			})
		}
	}
})


function get_product_by_code(code)
{
	var payment_code =  $('#payBy').val();
	var customer_code = $('#customer').val();
	if(code.length > 0) {
		$.ajax({
			url:HOME + 'get_product_by_code',
			type:'GET',
			cache:false,
			data:{
				'code' : code,
				'payment_code' : payment_code,
				'customer_code' : customer_code
			},
			success:function(rs) {
				if(isJson(rs)) {

					addToOrder(rs);
				}
				else {
					swal({
						title:'Error',
						text:rs,
						type:'error'
					});
				}
			},
			error:function(xhr, status, error) {
				swal({
					title:'Error!',
					text:'Error-'+xhr.status+': '+xhr.statusText,
					type:'error'
				});
			}
		})
	}
}

function addToOrder(rs) {
	var ds = $.parseJSON(rs);
	var id = ds.id;
	if($('#qty-'+id).length) {

		var c_qty =  $('#qty-'+id).val();
		var n_qty = ds.qty;


		if(isInteger(c_qty)) {
			c_qty = parseInt(c_qty);
		}
		else {
			c_qty = parseDefault(parseFloat(c_qty), 0);
		}

		if(isInteger(n_qty)) {
			n_qty = parseInt(n_qty);
		}
		else {
			n_qty = parseDefault(parseFloat(n_qty), 0);
		}

		qty = c_qty + n_qty;

		$('#qty-'+id).val(qty);

		recalItem(id);
	}
	else {
		var source = $('#row-template').html();
		var output = $('#item-table');

		render_append(source, ds, output);
		percent_init();
		recalItem(id);
	}
}


function recalItem(id) {
	var price = parseDefault(parseFloat($('#price-'+id).val()), 0);
	var qty = $('#qty-'+id).val();
	var qty = isInteger(qty) ? parseDefault(parseInt(qty), 0) : parseDefault(parseFloat(qty), 0);
	var disc = parseDiscountAmount($('#disc-'+id).val(), price);
	var sell_price = price - disc;
	var tax_rate = parseDefault(parseFloat($('#taxRate-'+id).val()), 0.00) * 0.01;
	var total = qty * sell_price;
	var tax_amount = total * tax_rate;
	var discount_amount = qty * disc;


	$('#total-'+id).text(addCommas(total.toFixed(2)));
	$('#taxAmount-'+id).val(tax_amount);
	$('#sellPrice-'+id).val(sell_price);
	$('#discAmount-'+id).val(discount_amount);

	recalTotal();
}



function recalTotal() {
	var total_qty = 0;
	var total_tax = 0;
	var total_disc = 0;
	var total_amount = 0;

	$('.input-qty').each(function() {
		let id = $(this).data('id');
		let qty = parseDefault(parseFloat($('#qty-'+id).val()), 0);
		let discAmount = parseDefault(parseFloat($('#discAmount-'+id).val()), 0);
		let tax = parseDefault(parseFloat($('#taxAmount-'+id).val()), 0);
		let total = parseDefault(parseFloat(removeCommas($('#total-'+id).text())), 0);

		total_qty += qty;
		total_tax += tax;
		total_disc += discAmount;
		total_amount += total;
	});

	$('#total_item').text(addCommas(total_qty.toFixed(2)));
	$('#total_amount').text(addCommas(total_amount.toFixed(2)));
	$('#total_discount').text(addCommas(total_disc.toFixed(2)));
	$('#total_tax').text(addCommas(total_tax.toFixed(2)));
	$('#net_amount').text(addCommas(total_amount.toFixed(2)));
}



function percent_init() {
	$('.input-disc').keyup(function(e) {
		if(e.keyCode === 32) {
			//-- press space bar
			var value = $.trim($(this).val());
			if(value.length) {
				var last = value.slice(-1);
				if(isNaN(last)) {
					//--- ถ้าตัวสุดท้ายไม่ใช่ตัวเลข เอาออก
					value = value.slice(0, -1);
				}
				value = value +"%";
				$(this).val(value);
			}
			else {
				$(this).val('');
			}

			recalItem($(this).data('id'));
		}
	})
}


function showPayment() {
	var amountText = $('#net_amount').text();
	var amount = parseDefault(parseFloat(removeCommas(amountText)), 0.00);

	if(amount > 0) {
		$('#payableAmount').val(amount);
		$('#payAmountLabel').text(amountText);

		$('#paymentModal').modal('show');
	}

}


$('#paymentModal').on('shown.bs.modal', function() {
	$('#receiveAmount').focus();
})



function changePayment() {
	//--- role = 1 ==> เครดิต
	//--- role = 2 ==> เงินสด
	//--- role = 3 ==> โอนเงิน
	//--- role = 4 ==> COD
	//--- role = 5 ==> บัตรเครดิต
	var payment = $('#payBy').val();
	var role = $('#payBy option:selected').data('role');
	//--- reset field
	$('#receiveAmount').val('');
	$('#changeAmount').val('');
	$('#btn-submit').attr('disabled','disabled');
	$('#bank_role').addClass('hide');

	if(role == 1) {
		//---- credit
		$('#receiveAmount').attr('disabled', 'disabled');
		$('#btn-submit').removeAttr('disabled');
		$('#btn-submit').focus();
	}
	else if(role == 2) {
		//--- cash
		$('#receiveAmount').removeAttr('disabled');
		$('#receiveAmount').focus();
	}
	else if(role == 3) {
		//--- bank transfer
		$('#bank_role').removeClass('hide');
		$('#receiveAmount').removeAttr('disabled');
		$('#receiveAmount').focus();
	}
	else if(role == 4) {
		//--- cod
		$('#receiveAmount').attr('disabled', 'disabled');
		$('#btn-submit').removeAttr('disabled');
		$('#btn-submit').focus();
	}
	else if(role == 5) {
		//--- Credit card
		var amount = parseDefault(parseFloat($('#payableAmount').val()), 0);
		if(amount > 0) {
			$('#receiveAmount').removeAttr('disabled');
			$('#changeAmount').val('');
			$('#receiveAmount').val(amount);
			$('#btn-submit').removeAttr('disabled');
			$('#btn-submit').focus();
		}
	}
}




function justBalance() {
	var amount = parseDefault(parseFloat($('#payableAmount').val()), 0);
	if(amount > 0) {
		var role = $('#payBy option:selected').data('role');
		if(role == 2 || role == 3 || role == 5) {
			$('#receiveAmount').val(amount);
			calChange();
			$('#btn-submit').removeAttr('disabled');
		}
	}
}


$('#receiveAmount').keyup(function() {
	var amount = parseDefault(parseFloat($('#payableAmount').val()), 0);
	var receive = parseDefault(parseFloat($(this).val()), 0);
	calChange();
	if(receive >= amount) {
		$('#btn-submit').removeAttr('disabled');
	}
	else {
		$('#btn-submit').attr('disabled', 'disabled');
	}
})

function calChange() {
	var amount = parseDefault(parseFloat($('#payableAmount').val()), 0);
	var receive = parseDefault(parseFloat($('#receiveAmount').val()), 0);
	var change = receive - amount;
	$('#changeAmount').val(change);
}


function submitPayment() {
	var customer = $('#customer').val();
	if(customer.length == 0) {
		swal('กรุณาระบุลูกค้า');
		return false;
	}



	$('.sell-item').each(function() {
		var id = $(this).data('id');
		var code = $(this).val();
	})

}
