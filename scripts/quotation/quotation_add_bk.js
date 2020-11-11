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




function getOrderGrid(pdCode){
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
	var code = $('#code').val();
	var discLabel = $('#discountLabel').val();

	var items = [];

  $('.input-qty').each(function(){
    let pdCode = $(this).attr('id');
    var qty = parseDefault(parseInt($(this).val()), 0);

    if(qty > 0){
      var item = {
        'product_code' : pdCode,
        'qty' : qty
      }

      items.push(item);
    }
  });

  if(items.length == 0){
    swal('กรุณาระบุจำนวนอย่างน้อย 1 รายการ');
    return false;
  }

  var data = JSON.stringify(items);

	load_in();

  $.ajax({
    url:HOME + 'add_details',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'data' : data,
			'discountLabel' : discLabel
		},
		success:function(rs){
			load_out();
			if(rs == 'success'){
				swal({
					title:'Success',
					text:'เพิ่ม '+items.length+' รายการ เรียบร้อยแล้ว',
					type:'success',
					timer:1000
				});

				setTimeout(function(){
					updateDetailTable(); //--- update list of order detail
				},1500);
			}else{
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				}, function(){
					$('#orderGrid').modal('show');
				});
			}
		}
  });
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

	load_in();

	$.ajax({
		url:HOME + 'add_detail',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'product_code' : item_code,
			'price' : price,
			'discountLabel' : disc,
			'qty' : qty
		},
		success:function(rs){
			load_out();
			var rs = $.trim(rs);
			if(rs === 'success'){
				updateDetailTable();
				setTimeout(function(){
					$('#item-code').val('');
					$('#item-name').val('');
					$('#price').val('');
					$('#disc').val('');
					$('#qty').val('');
					$('#item-code').focus();
				},200);

			} else {
				swal({
					title: 'Error!',
					text: rs,
					type:'error'
				});
			}
		}
	})
}

function removeRow(id){
	$.ajax({
		url:HOME + "delete_detail",
		type:"POST",
		cache:false,
		data:{
			"id" : id
		},
		success:function(rs){
			rs = $.trim(rs);
			if(rs === "success"){
				updateDetailTable();
			}
			else
			{
				swal({
					title:"Error!",
					text:rs,
					type:"error"
				});
			}
		}
	})
}


function updateDetailTable()
{
	const code = $('#code').val();
	$.ajax({
		url:HOME + "get_details_table",
		type:"GET",
		cache:false,
		data:{
			"code" : code
		},
		success:function(rs){
			if(isJson(rs)){
				let data = JSON.parse(rs);
				let source = $("#detail-template").html();
				let output = $("#detail-table");

				render(source, data, output);
			}
			else
			{
				swal({
					title:'Load Item failed !',
					text:rs,
					type:'error'
				})
			}
		}
	})
}



function recal(){
	var total_amount = 0.00;
	var total_qty = 0.00;
	var total_discount = 0.00;
	var net_amount = 0.00;

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

	$('#total-qty').text(addCommas(total_qty.toFixed(2)));
	$('#total-amount').text(addCommas(total_amount.toFixed(2)));
	$('#total-discount').text(addCommas(total_discount.toFixed(2)));
	$('#net-amount').text(addCommas(net_amount.toFixed(2)));
}



//---- update row data
function update_row(id, price, qty, disc){
	$.ajax({
		url:HOME + 'update_row',
		type:'POST',
		cache:false,
		data:{
			"id" : id,
			"price" : price,
			"qty" : qty,
			"discountLabel" : disc
		},
		success:function(rs){
			var rs = $.trim(rs);
			if(rs === 'success') {
				$('#price-'+id).data('old', price);
				$('#qty-'+id).data('old', qty);
				$('#disc-'+id).data('old', disc);
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






$('.edit-row').keyup(function(e){
	recal();
});


$('.edit-row').focusout(function(){
	var value = $(this).val();
	var old_value = $(this).data('old');
	console.log(value, "-", old_value);
	if(value == old_value){
		return false;
	}

	var id = $(this).data('id');
	var price = $('#price-'+id);
	var old_price = $('#old-price-'+id).val();
	var qty = $('#qty-'+id);
	var old_qty = $('#old-qty-'+id).val();
	var disc = $('#disc-'+id);
	var old_disc = $('#old-disc-'+id).val();


	if(isNaN(parseFloat(price.val())))	{
		price.addClass('has-error');
		return false;
	}	else	{
		price.removeClass('has-error');
	}


	if(isNaN(parseInt(qty.val())) || qty.val() == '0')	{
		qty.addClass('has-error');
		return false;
	}	else	{
		qty.removeClass('has-error');
	}


	update_row(id, price.val(), qty.val(), disc.val());
});


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
