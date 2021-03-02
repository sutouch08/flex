var HOME = BASE_URL + 'masters/customers/';

function addNew(){
  window.location.href = HOME + 'add_new';
}



function goBack(){
  window.location.href = HOME;
}


function getEdit(code){
  window.location.href = HOME + 'edit/'+code;
}


function viewDetail(code){
  window.location.href = HOME + 'view_detail/'+code;
}


function changeURL(code, tab)
{

	var url = HOME + 'edit/' + code + '/' + tab;
	var stObj = { stage: 'stage' };
	window.history.pushState(stObj, 'customers', url);
}


function changeView(code, tab)
{

	var url = HOME + 'view_detail/' + code + '/' + tab;
	var stObj = { stage: 'stage' };
	window.history.pushState(stObj, 'customers', url);
}



function saveAdd() {
	var code = $('#code').val();
	var name = $('#name').val();
	var tax_id = $('#Tax_id').val();
	var group = $('#group').val();
	var kind = $('#kind').val();
	var type = $('#type').val();
	var grade = $('#class').val();
	var area = $('#area').val();
	var sale = $('#sale').val();
	var credit_term = $('#credit_term').val();
	var credit_amount = $('#CreditLine').val();
	var note = $('#note').text();

	if(code.length == 0) {
		set_error($('#code'), $('#code-error'), 'กรุณาระบุรหัสลูกค้า');
		return false;
	}
	else {
		clear_error($('#code'), $('#code-error'));
	}


	if(name.length == 0) {
		set_error($('#name'), $('#name-error'), 'กรุณาระบุชื่อลูกค้า');
		return false;
	}
	else {
		clear_error($('#name'), $('#name-error'));
	}

	load_in();
	$.ajax({
		url: HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'name' : name,
			'Tax_id' : tax_id,
			'group' : group,
			'kind' : kind,
			'type' : type,
			'class' : grade,
			'area' : area,
			'sale' : sale,
			'credit_term' : credit_term,
			'CreditLine' : credit_amount,
			'note' : note
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs)
			if(rs === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer: 1000
				});

				setTimeout(function() {
					addNew();
				}, 1200);
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
			load_out();
			var errorMessage = xhr.status + ': '+xhr.statusText;
			swal({
				title:'Error!',
				text:"Error-" + errorMessage,
				type:'error'
			})
		}
	})
}



function update() {
	var code = $('#code').val();
	var name = $('#name').val();
	var old_name = $('#old_name').val();
	var tax_id = $('#Tax_id').val();
	var group = $('#group').val();
	var kind = $('#kind').val();
	var type = $('#type').val();
	var grade = $('#class').val();
	var area = $('#area').val();
	var sale = $('#sale').val();
	var credit_term = $('#credit_term').val();
	var credit_amount = $('#CreditLine').val();
	var note = $('#note').text();

	if(name.length == 0) {
		set_error($('#name'), $('#name-error'), 'กรุณาระบุชื่อลูกค้า');
		return false;
	}
	else {
		clear_error($('#name'), $('#name-error'));
	}

	load_in();
	$.ajax({
		url: HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'name' : name,
			'old_name' : old_name,
			'Tax_id' : tax_id,
			'group' : group,
			'kind' : kind,
			'type' : type,
			'class' : grade,
			'area' : area,
			'sale' : sale,
			'credit_term' : credit_term,
			'CreditLine' : credit_amount,
			'note' : note
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs)
			if(rs === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer: 1000
				});
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
			load_out();
			var errorMessage = xhr.status + ': '+xhr.statusText;
			swal({
				title:'Error!',
				text:"Error-" + errorMessage,
				type:'error'
			})
		}
	})
}



function clearFilter() {
  $.get(HOME + 'clear_filter', function(rs){
    goBack();
  });
}


function getDelete(code, name){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + name + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    window.location.href = HOME + 'delete/' + code;
  })
}



$('.filter').change(function(){
  getSearch();
});



function getSearch(){
  $('#searchForm').submit();
}


function get_template() {
	var token	= new Date().getTime();
	get_download(token);
	window.location.href = HOME + 'download_template/'+token;
}


$('#credit_term').focus(function(){
	$(this).select();
})

$('#CreditLine').focus(function(){
	$(this).select();
})

$('#name').focus(function(){
	$(this).select();
});

$('#Tax_id').focus(function() {
	$(this).select();
})
