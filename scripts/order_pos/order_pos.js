var HOME = BASE_URL + 'pos/order_pos/';

function goToPOS(id) {
	window.location.href = HOME + 'pos/'+id;
}


$('#barcode-box').keyup(function(e){
	if(e.keyCode === 13) {
		var barcode = $.trim($(this).val());

		if(barcode.length) {
			$(this).val('');
			$.ajax({
				url:HOME + 'get_product_by_barcode',
				type:'GET',
				cache:false,
				data:{
					'barcode' : barcode
				},
				success:function(rs) {
					if(isJson(rs)) {
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
