
function save() {
	if($('.row-qty').length > 0) {
		var error = 0;
		var message = "";
		var code = $('#code').val();
		var bDiscText = $('#billDiscPercent').val();
		var bDiscAmount = removeCommas($('#billDiscAmount').val());

		var items = [];

		$('.row-qty').each(function(){
			var id = $(this).data('id');
			var product_code = $(this).data('item');
			var price = parseDefault(parseFloat($('#price-'+id).val()), 0);
			var qty = parseDefault(parseFloat($('#qty-'+id).val()), 0);
			var disc = $('#disc-'+id).val();
			var discount = parseDiscount(disc, price);
			var total_price = price * qty;
			var total_disc = qty * discount.discountAmount;
			var amount = total_price - total_disc;

			if(isNaN(amount)){
				amount = 0;
			}

			if(amount < 0 ){
				$('#price-'+id).addClass('has-error');
				$('#qty-'+id).addClass('has-error');
				$('#disc-'+id).addClass('has-error');
				$('#amount-'+id).addClass('red');
				error++;
				message = "พบข้อผิดพลาด";
			} else {
				$('#price-'+id).removeClass('has-error');
				$('#qty-'+id).removeClass('has-error');
				$('#disc-'+id).removeClass('has-error');
				$('#amount-'+id).removeClass('red');
			}

			if(qty > 0) {
				let item = {
					"product_code" : product_code,
					"qty" : qty,
					"price" : price,
					"discount_label" : disc
				}

				items.push(item);
			}

		});

		if(items.length == 0){
	    swal('กรุณาระบุจำนวนอย่างน้อย 1 ชิ้น');
	    return false;
	  }

		if(error > 0){
			swal({
				title:"Error!",
				text:"กรุณาแก้ไขข้อผิดพลาด",
				type:"error"
			});

			return false;
		}

	  var data = JSON.stringify(items);

		load_in();

		$.ajax({
	    url:HOME + 'save',
			type:'POST',
			cache:false,
			data:{
				'code' : code,
				'bDiscText' : bDiscText,
				'bDiscAmount' : bDiscAmount,
				'data' : data
			},
			success:function(rs){
				load_out();
				if(rs == 'success'){
					swal({
						title:'Success',
						text:'บันทึกเอกสารเรียบร้อยแล้ว',
						type:'success',
						timer:1000
					});

					setTimeout(function(){
						goDetail(code);
					},1500);
				}else{
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					});
				}
			}
	  });



	} else {
		return false;
	}
}



function get_edit(){
  $('.edit').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}

function update(){
  let code = $('#code').val();
  let date = $('#date_add').val();
  let customer_code = $('#customerCode').val();
  let contact = $('#contact').val();
  let is_term = $('#is_term').val();
  let credit_term = $('#credit_term').val();
	let valid_days = $('#valid_days').val();
	let title = $('#title').val();
  let remark = $('#remark').val();

  if(!isDate(date)){
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  if(customer_code.length == 0){
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
      'date_add' : date,
      'customer_code' : customer_code,
      'contact' : contact,
      'is_term' : is_term,
      'credit_term' : credit_term,
			'valid_days' : valid_days,
			'title' : title,
      'remark' : remark
    },
    success:function(rs){
      load_out();
      rs = $.trim(rs);
      if(rs === 'success'){
        $('.edit').attr('disabled', 'disabled');
        $('#btn-update').addClass('hide');
        $('#btn-edit').removeClass('hide');

        swal({
          title:'Updated',
          type:'success',
          timer: 1000
        });
      }else{
        swal({
          title:'Error!',
          text: rs,
          type:'error'
        });
      }
    }

  })
}



function getOrderGrid(pdCode) {
	if( pdCode.length > 0  ){
		load_in();
		$.ajax({
			url: BASE_URL + 'orders/orders/get_product_grid',
			type:"GET",
			cache:"false",
			data:{
				"style_code" : pdCode
			},
			success: function(rs){
				load_out();
				var rs = rs.split(' | ');
				if( rs.length == 4 ){
					var grid = rs[0];
					var width = rs[1];
					var pdCode = rs[2];
					var style = rs[3];
					$("#modal-content").css("width", width +"px");
					$("#modalTitle").html(pdCode);
					$("#id_style").val(style);
					$("#modalBody").html(grid);
					$("#orderGrid").modal('show');
				}else{
					swal(rs[0]);
				}
			}
		});
	}
}


function getProductGrid(){
	var pdCode = $('#pd-box').val();
	if( pdCode.length > 0  ){
		load_in();
		$.ajax({
			url: BASE_URL + 'orders/orders/get_product_grid',
			type:"GET",
			cache:"false",
			data:{
				"style_code" : pdCode
			},
			success: function(rs){
				load_out();
				var rs = rs.split(' | ');
				if( rs.length == 4 ){
					var grid = rs[0];
					var width = rs[1];
					var pdCode = rs[2];
					var style = rs[3];
					$("#modal").css("width", width +"px");
					$("#modal-content").css("width", width +"px");
					$("#modalTitle").html(pdCode);
					$("#id_style").val(style);
					$("#modalBody").html(grid);
					$("#orderGrid").modal('show');
				}else{
					swal(rs[0]);
				}
			}
		});
	}
}


function valid_qty(){
  return true;
}


function insert_item()
{
	$('#orderGrid').modal('hide');

	var disc = $('#discountLabel').val();


  $('.input-qty').each(function(){
		let no = parseInt($('#no').val()) + 1;
    let item_code = $(this).data('pdcode');
		let item_name = $(this).data('pdname');
		let price = parseDefault(parseFloat($(this).data('price')), 0);
    let qty = parseDefault(parseFloat($(this).val()), 0);

    if(qty > 0){
			if($('[data-item="'+item_code+'"]').length > 0) {
				var rs = $('[data-item="'+item_code+'"]');
				var id = rs.data('id');
				var c_qty = parseDefault(parseFloat($('#qty-'+id).val()), 0);
				var new_qty = c_qty + qty;

				//---- update row
				$('#price-'+id).val(price);
				$('#qty-'+id).val(new_qty);
				$('#disc-'+id).val(disc);

			} else {
				var data = {
					"id" : no,
					"product_code" : item_code,
					"product_name" : item_name,
					"price" : price,
					"qty" : qty,
					"discount_label" : disc,
					"amount" : 0
				}

				var source = $('#row-template').html();
				var output = $('#detail-table');

				render_append(source, data, output);
				$('#no').val(no);
			}
    }
  });

	init();

	recal();
	reIndex();
	clearFields();
	$('#item-code').focus();
}



function addItem(){
	var code = $('#code').val();
	var item_code = $('#item-code').val();
	var item_name = $('#item-name').val();
	var price = parseFloat($('#price').val());
	var disc = $('#disc').val();
	var qty = parseFloat($('#qty').val());
	var no = parseDefault(parseInt($('#no').val()), 0) + 1;

	if(item_code.length === 0){
		$('#item-code').addClass('has-error');
		return false;
	} else {
		$('#item-code').removeClass('has-error');
	}

	if(isNaN(price)){
		$('#price').addClass('has-error');
		return false;
	} else {
		$('#price').removeClass('has-error');
	}

	if(isNaN(qty)){
		$('#qty').addClass('has-error');
		return false;
	} else {
		$('#qty').removeClass('has-error');
	}

	if($('[data-item="'+item_code+'"]').length > 0) {
		var rs = $('[data-item="'+item_code+'"]');
		var id = rs.data('id');
		var c_qty = parseDefault(parseFloat($('#qty-'+id).val()), 0);
		var new_qty = c_qty + qty;

		//---- update row
		$('#price-'+id).val(price);
		$('#qty-'+id).val(new_qty);
		$('#disc-'+id).val(disc);
	} else {
		var data = {
			"id" : no,
			"product_code" : item_code,
			"product_name" : item_name,
			"price" : price,
			"qty" : qty,
			"discount_label" : disc,
			"amount" : 0
		}

		var source = $('#row-template').html();
		var output = $('#detail-table');

		render_append(source, data, output);
		$('#no').val(no);
	}

	init();

	recal();
	reIndex();
	clearFields();
	$('#item-code').focus();
}



function removeRow(no) {
	$('#row-'+no).remove();
	reIndex();
	recal();
}


function clearFields() {
	$('#pd-box').val('');
	$('#item-code').val('');
	$('#item-name').val('');
	$('#price').val('');
	$('#disc').val('');
	$('#qty').val('');
}

function active_bdisc() {
	$('#billDiscPercent').removeAttr('disabled');
	$('#billDiscAmount').removeAttr('disabled');
	$('#btn-edit-bdisc').addClass('hide');
	$('#btn-save-bdisc').removeClass('hide');
}


function updateBillDisc() {
	var billDisAmount = parseDefault(parseFloat(removeCommas($('#billDiscAmount').val())), 0);
	var billDisPercent = parseDefault(parseFloat($('#billDiscPercent').val()), 0);

	var code = $('#code').val();
	load_in();

	$.ajax({
		url:HOME + 'update_bill_discount',
		type:'POST',
		cache:false,
		data:{
			"code" : code,
			"bDiscText" : billDisPercent,
			"bDiscAmount" : billDisAmount
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if(rs === 'success') {
				$('#billDiscPercent').attr('disabled', 'disabled');
				$('#billDiscAmount').attr('disabled', 'disabled');
				$('#btn-save-bdisc').addClass('hide');
				$('#btn-edit-bdisc').removeClass('hide');
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				});
			}
		}
	})

}

//----- คำนวนส่วนลดท้ายบิลย้อนกลับไปเป็น % ในชอง disc billDiscPercent
$('#billDiscAmount').keyup(function() {
	var totalAfDisc = parseDefault(parseFloat($('#totalAfDisc').val()), 0);
	var percent = 0.00;
	var billDisAmount = parseDefault(parseFloat(removeCommas($(this).val())), 0);

	if(totalAfDisc > 0) {
		if(billDisAmount >= 0 && billDisAmount <= totalAfDisc) {
			percent = (billDisAmount/totalAfDisc) * 100;
		}
		else {
			$(this).val(0.00);
			percent = 0.00;
		}
	}
	else {
		$(this).val(0.00);
		percent = 0.00;
	}

	$('#billDiscPercent').val(percent.toFixed(2));

	// recal();
})

$('#billDiscAmount').focusout(function(){
	var totalAfDisc = parseDefault(parseFloat($('#totalAfDisc').val()), 0);
	var percent = 0.00;
	var billDisAmount = parseDefault(parseFloat(removeCommas($(this).val())), 0);

	if(totalAfDisc > 0) {
		if(billDisAmount >= 0 && billDisAmount <= totalAfDisc) {
			percent = (billDisAmount/totalAfDisc) * 100;
			$(this).val(billDisAmount.toFixed(2))
		}
		else {
			$(this).val(0.00);
			percent = 0.00;
		}
	}
	else {
		$(this).val(0.00);
		percent = 0.00;
	}

	$('#billDiscPercent').val(percent.toFixed(2));
	recal();
});


//------ คำนวนส่วนลดท้ายบิล แล้ว update ช่อง มูลค่าส่วนลดท้ายบิล (discAmount)
$('#billDiscPercent').keyup(function(){
	var totalAfDisc = parseDefault(parseFloat($('#totalAfDisc').val()), 0);
	var percent = parseDefault(parseFloat($(this).val()), 0);
	var billDisAmount = 0.00;

	if(totalAfDisc > 0) {
		if(percent >= 0 && percent <= 100) {
			billDisAmount = totalAfDisc * (percent * 0.01);
		}
		else {
			$(this).val(0.00);
			billDisAmount = 0.00;
		}
	}
	else {
		$(this).val(0.00);
		billDiscAmount = 0.00;
	}

	$('#billDiscAmount').val(addCommas(billDisAmount.toFixed(2)));

});

$('#billDiscPercent').focusout(function() {
	var totalAfDisc = parseDefault(parseFloat($('#totalAfDisc').val()), 0);
	var percent = parseDefault(parseFloat($(this).val()), 0);
	var billDisAmount = 0.00;

	if(totalAfDisc > 0) {
		if(percent >= 0 && percent <= 100) {
			billDisAmount = totalAfDisc * (percent * 0.01);
			$(this).val(percent.toFixed(2))
		}
		else {
			$(this).val(0.00);
			billDisAmount = 0.00;
		}
	}
	else {
		$(this).val(0.00);
		billDiscAmount = 0.00;
	}

	$('#billDiscAmount').val(addCommas(billDisAmount.toFixed(2)));

	recal();
})

function recal(){
	var total_amount = 0.00;
	var total_qty = 0.00;
	var total_discount = 0.00;
	var net_amount = 0.00;
	var billDisAmount = parseDefault(parseFloat(removeCommas($('#billDiscAmount').val())), 0);

	$('.row-qty').each(function(){
		var id = $(this).data('id');
		var price = parseDefault(parseFloat($('#price-'+id).val()), 0);
		var qty = parseDefault(parseFloat($('#qty-'+id).val()), 0);
		var disc = $('#disc-'+id).val();
		var discount = parseDiscount(disc, price);
		var total_price = price * qty;
		var total_disc = qty * discount.discountAmount;
		var amount = total_price - total_disc;

		total_qty += qty;
		total_discount += total_disc;
		total_amount += total_price;
		net_amount += amount;

		if(isNaN(amount)){
			amount = 0;
		}

		if(amount < 0 ){
			$('#price-'+id).addClass('has-error');
			$('#qty-'+id).addClass('has-error');
			$('#disc-'+id).addClass('has-error');
			$('#amount-'+id).addClass('red');
		} else {
			$('#price-'+id).removeClass('has-error');
			$('#qty-'+id).removeClass('has-error');
			$('#disc-'+id).removeClass('has-error');
			$('#amount-'+id).removeClass('red');
		}

		var amount = addCommas(amount.toFixed(2));

		$('#amount-'+id).text(amount);
	});

	$('#totalAfDisc').val(net_amount);

	total_discount += billDisAmount;
	net_amount -= billDisAmount;

	$('#total-qty').text(addCommas(total_qty.toFixed(2)));
	$('#total-amount').text(addCommas(total_amount.toFixed(2)));
	$('#total-discount').text(addCommas(total_discount.toFixed(2)));
	$('#net-amount').text(addCommas(net_amount.toFixed(2)));

	$('#btn-save').removeClass('hidden');
	$('#btn-back').addClass('hidden');
	$('#btn-leave').removeClass('hidden');
}



function get_item(){
	var code = $('#item-code').val();
	if(code.length > 0) {
		$.ajax({
			url:HOME + 'get_item',
			type:'GET',
			cache:false,
			data:{
				'item_code' : code
			},
			success:function(rs){
				var rs = $.trim(rs);
				if(isJson(rs)){
					var ds = $.parseJSON(rs);
					$('#item-name').val(ds.product_name);
					$('#price').val(ds.price);
					$('#price').focus();
				} else {
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					});
				}
			}
		})
	}
}




$("#customer").autocomplete({
	source: BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#customerCode").val(code);
			$("#customer").val(code);
			$('#customerName').val(name);
			$('#contact').focus();
		}else{
			$("#customerCode").val('');
			$(this).val('');
			$('#customerName').val('');
		}
	}
});


$('#is_term').change(function(){
  if($(this).val() == 1){
    $('#credit_term').removeAttr('readonly').focus();
  }else{
    $('#credit_term').val(0).attr('readonly', 'readonly');
  }
})



$('#date_add').datepicker({
  dateFormat:'dd-mm-yy'
})

$("#pd-box").autocomplete({
	source: BASE_URL + 'auto_complete/get_style_code',
	autoFocus: true
});




$('#pd-box').keyup(function(event) {
	if(event.keyCode == 13){
		var code = $(this).val();
		if(code.length > 0){
			setTimeout(function(){
				getProductGrid();
			}, 300);

		}
	}

});



$('#item-code').autocomplete({
	source:BASE_URL + 'auto_complete/get_product_code',
	//minLength: 2,
	autoFocus:true,
	close:function(){
		var rs = $(this).val();
		if(rs == "no item found" || rs == "*") {
			$(this).val('');
		}
	}
});



$('#item-code').keyup(function(e){
	if(e.keyCode == 13){
		var code = $(this).val();
		if(code.length > 4){
			setTimeout(function(){
				get_item();
			}, 200);
		}
	}
});





function init(){
	$('.edit-row').keyup(function(e){
		recal();
	});
}


$(document).ready(function(){
	init();
})


$('#price').keyup(function(e){
	if(e.keyCode === 13){
		$('#disc').focus();
	}
})


$('#disc').keyup(function(e){
	if(e.keyCode === 13){
		$('#qty').focus();
	}
});


$('#qty').keyup(function(e){
	if(e.keyCode === 13){
		addItem();
	}
})
