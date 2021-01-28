var HOME = BASE_URL + 'masters/items/';

function addNew(){
  window.location.href = HOME + 'add_new';
}



function goBack(){
  window.location.href = HOME;
}


function getEdit(code){
  window.location.href = HOME + 'edit/'+code;
}


function duplicate(code){
  window.location.href = HOME + 'duplicate/'+code;
}



$('#style').autocomplete({
  source: BASE_URL + 'auto_complete/get_style_code_and_name',
  autoFocus:true,
	close:function(){
		var style = $(this).val();
		var arr = style.split(' | ');
		if(arr.length === 2) {
			$(this).val(arr[0]);
		}
		else {
			$(this).val('');
		}
	}
});



$('#color').autocomplete({
  source: BASE_URL + 'auto_complete/get_color_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var err = rs.split(' | ');
    if(err.length == 2){
      $(this).val(err[0]);
    }else{
      $(this).val('');
    }
  }
});


$('#size').autocomplete({
  source:BASE_URL + 'auto_complete/get_size_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var err = rs.split(' | ');
    if(err.length == 2){
      $(this).val(err[0]);
    }else{
      $(this).val('');
    }
  }
});


function checkAdd(){
	var code = $('#code').val();
	var name = $('#name').val();

	if(code.length === 0) {
		set_error($('#code'), $('#code-error'), 'Required');
		return false;
	}
	else {
		clear_error($('#code'), $('#code-error'));
	}

	if(name.length === 0) {
		set_error($('#name'), $('#name-error'), 'Required');
		return false;
	}
	else {
		clear_error($('#name'), $('#name-error'));
	}


  if(code.length > 0){
    $.ajax({
      url:HOME + 'is_exists_code/'+code,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs != 'ok'){
          set_error($('#code'), $('#code-error'), rs);
          return false;
        }else{
          clear_error($('#code'), $('#code-error'));
          $('#addForm').submit();
        }
      }
    })
  }
}


function checkEdit(){
	var name = $('#name').val();

	if(name.length === 0) {
		set_error($('#name'), $('#name-error'), 'Required');
		return false;
	}
	else {
		clear_error($('#name'), $('#name-error'));
	}

	$('#addForm').submit();
	
}


function clearFilter(){
  var url = HOME + 'clear_filter';
  var page = BASE_URL + 'masters/products';
  $.get(url, function(){
    goBack();
  });
}


$('#price').focus(function(){
	$(this).select();
})

$('#cost').focus(function(){
	$(this).select();
})

function getDelete(code){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + code + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
      url: BASE_URL + 'masters/items/delete_item/' + code,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs === 'success'){
          swal({
            title:'Deleted',
            text:'ลบรุ่นสินค้าเรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });

          $('#row-'+code).remove();
        }else{
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
        }
      }
    })

  })
}

function getTemplate(){
  var token	= new Date().getTime();
	get_download(token);
	window.location.href = BASE_URL + 'masters/items/download_template/'+token;
}

$('#style').keyup(function(e){
	if(e.keyCode === 13) {
		$('#color').focus();
	}
})

$('#color').keyup(function(e){
	if(e.keyCode === 13) {
		$('#size').focus();
	}
})

$('#size').keyup(function(e){
	if(e.keyCode === 13) {
		$('#barcode').focus();
	}
})

$('#barcode').keyup(function(e){
	if(e.keyCode === 13) {
		$('#cost').focus();
	}
})


$('#cost').keyup(function(e){
	if(e.keyCode === 13) {
		$('#price').focus();
	}
})

$('#price').keyup(function(e){
	if(e.keyCode === 13) {
		$('#unit_code').focus();
	}
})

function getSearch(){
  $('#searchForm').submit();
}
