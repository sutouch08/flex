var HOME = BASE_URL + 'masters/products/';

function addNew(){
  window.location.href = BASE_URL + 'masters/products/add_new';
}



function goBack(){
  window.location.href = BASE_URL + 'masters/products';
}


function getEdit(code){
  window.location.href = BASE_URL + 'masters/products/edit/'+code;
}


function changeURL(style, tab)
{

	var url = BASE_URL + 'masters/products/edit/' + style + '/' + tab;
	var stObj = { stage: 'stage' };
	window.history.pushState(stObj, 'products', url);
}




function newItems(){
  var style = $('#style').val();
  window.location.href = BASE_URL + 'masters/products/item_gen/' + style;
}




function clearFilter(){
  var url = BASE_URL + 'masters/products/clear_filter';
  var page = BASE_URL + 'masters/products';
  $.get(url, function(rs){
    window.location.href = page;
  });
}


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
      url: BASE_URL + 'masters/products/delete_style/' + code,
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

function checkAdd() {
	var code = $('#code').val();
	var name = $('#name').val();

	if(code.length == 0) {
		set_error($('#code'), $('#code-error'), 'Required');
		return false;
	}
	else {
		clear_error($('#code'), $('#code-error'));
	}

	if(name.length == 0) {
		set_error($('#name'), $('#name-error'), 'Required');
		return false;
	}
	else {
		clear_error($('#name'), $('#name-error'));
	}

	$.ajax({
		url:HOME + 'is_style_exists/'+code,
		type:'POST',
		cache:false,
		success:function(rs) {
			var rs = $.trim(rs);
			if(rs === 'exists') {
				set_error($('#code'), $('#code-error'), 'รหัสซ้ำ');
				return false;
			}
			else {
				$('#addForm').submit();
			}
		}
	})


}



function getSearch(){
  $('#searchForm').submit();
}


$('#cost').focus(function() {
	$(this).select();
})

$('#price').focus(function() {
	$(this).select();
})


function doExport(code){
  load_in();
  $.ajax({
    url:BASE_URL + 'masters/products/export_products/'+code,
    type:'POST',
    cache:false,
    success:function(rs){
      load_out();
      if(rs === 'success'){
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });
      }else{
        swal({
          title:'Error',
          text:rs,
          type:'error'
        });
      }
    }
  })
}
