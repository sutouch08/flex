var HOME = BASE_URL + 'pos/order_pos/';

function goToPOS(id) {
	window.location.href = HOME + 'main/'+id;
}


function goAdd(id) {
	window.location.href = HOME + 'add/'+id;
}

function viewDeail(code) {
	window.location.href = HOME + 'pos/view_detail/'+code;
}

function removeItem(id) {
	$('#row-'+id).remove();
	recalTotal();
}




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
		var payment_code = $('#payBy').val();
		var customer_code = $('#customer').val();

		if(barcode.length) {
			$(this).val('');
			$.ajax({
				url:HOME + 'get_product_by_code',
				type:'GET',
				cache:false,
				data:{
					'code' : barcode,
					'customer_code' : customer_code,
					'payment_code' : payment_code
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


$('#barcode-box').autocomplete({
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
	$('#bank_account').attr('disabled', 'disabled');

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
		$('#bank_account').removeAttr('disabled');
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


$('#receiveAmount').keyup(function(e) {
	if(e.keyCode == 13) {
		submitPayment();
	}
	else {
		var amount = parseDefault(parseFloat($('#payableAmount').val()), 0);
		var receive = parseDefault(parseFloat($(this).val()), 0);
		calChange();
		if(receive >= amount) {
			$('#btn-submit').removeAttr('disabled');
		}
		else {
			$('#btn-submit').attr('disabled', 'disabled');
		}
	}

})

function calChange() {
	var amount = parseDefault(parseFloat($('#payableAmount').val()), 0);
	var receive = parseDefault(parseFloat($('#receiveAmount').val()), 0);
	var change = receive - amount;
	$('#changeAmount').val(change.toFixed(2));
}


function submitPayment() {
	var customer_code = $('#customer').val();
	var customer_name = $('#customer option:selected').text();
	var channels_code = $('#channels_code').val();
	var payment_code = $('#payBy').val();
	var acc_no = $('#bank_account').val();
	var payment_role = $('#payBy option:selected').data('role');
	var zone_code = $('#zone_code').val();
	var warehouse_code = $('#warehouse_code').val();
	var pos_code = $('#pos_code').val();
	var prefix = $('#prefix').val();
	var shop_id = $('#shop_id').val();

	var amount = parseDefault(parseFloat($('#payableAmount').val()), 0);
	var receive_amount = parseDefault(parseFloat($('#receiveAmount').val()), 0);
	var change = receive_amount - amount;

	if(payment_role == 2 || payment_role == 3 || payment_role == 5) {
		if(amount > receive_amount) {
			swal("ยอดเงินไม่ครบ");
			return false;
		}
	}



	if(customer_code.length == 0) {
		swal('กรุณาระบุลูกค้า');
		return false;
	}

	if(channels_code.length == 0) {
		swal('Missing configuration : POS_CHANNELS');
		return false;
	}

	if(payment_code == '') {
		swal('กรุณาระบุช่องทางการชำระเงิน');
		return false;
	}

	if(payment_role == 3 && acc_no.length == 0) {
		swal('กรุณาระบุเลขที่บัญชี');
		return false;
	}

	var items = [];

	$('.sell-item').each(function() {
		var id = $(this).data('id');
		var code = $(this).val();
		var name = $('#pdName-'+id).val();
		var item_type = $('#itemType-'+id).val();
		var unit_code = $('#unitCode-'+id).val();
		var std_price = parseDefault(parseFloat($('#stdPrice-'+id).val()),0); //--- standard price
		var price = parseDefault(parseFloat($('#price-'+id).val()),0); //---- input price
		var sell_price = parseDefault(parseFloat($('#sellPrice-'+id).val()), 0); //--- price after discount
		var discount_label = $('#disc-'+id).val();
		var discount_amount = parseDefault(parseFloat($('#discAmount-'+id).val()),0);
		var qty = parseDefault(parseFloat($('#qty-'+id).val()), 1);
		var total_amount = sell_price * qty;
		var tax_rate = $('#taxRate-'+id).val();
		var tax_amount = $('#taxAmount-'+id).val();

		var item = {
			'code' : code,
			'name' : name,
			'item_type' : item_type,
			'unit_code' : unit_code,
			'std_price' : std_price,
			'price' : price,
			'sell_price' : sell_price,
			'discount_label' : discount_label,
			'discount_amount' : discount_amount,
			'qty' : qty,
			'total_amount' : total_amount,
			'vat_rate' : tax_rate,
			'vat_amount' : tax_amount,
			'zone_code' : zone_code
		}

		items.push(item);
	})

	if(items.length > 0) {
		var order = {
			'prefix' : prefix,
			'customer_code' : customer_code,
			'customer_name' : customer_name,
			'channels_code' : channels_code,
			'payment_code' : payment_code,
			'acc_no' : acc_no,
			'payment_role' : payment_role,
			'shop_id' : shop_id,
			'warehouse_code' : warehouse_code,
			'pos_code' : pos_code,
			'amount' : amount,
			'received' : receive_amount,
			'changed' : change.toFixed(2),
			'details' : items
		}

		$.ajax({
			url:HOME + 'add',
			type:'POST',
			dataType:'json',
			contentType:'application:json',
			processData:false,
			data: JSON.stringify(order),
			complete: function(data) {
				var rs = data.responseText;
				if(isJson(rs)) {
					var ds = $.parseJSON(rs);
					viewDeail(ds.order_code);
				}
				else {
					swal({
						title:'Error!',
						text: rs,
						type:'error'
					})
				}
			},
			error:function(xhr, status, error) {
				swal({
					title:'Error!',
					text: xhr.responseText,
					type:'error'
				})
			}

		})
	}

}
