let oTable;

class Class_kb_log
{
	constructor(){
		this.param = [];
		this.param.table_config = {
			    responsive: {
			        details: {
			            type: 'column'
			        }
			    },
			    processing: true,
			    serverSide: true,
			    lengthMenu: [[5], [5]],
			    iDisplayLength : 5,
			    "searching": false,
			    ajax: {
			        url: base_url_js+'dashboard/c_dashboard/get_log_kb_table',
			        type: 'post',
			        data: function (d) {
			        	let filterTable = {}
			            $('.filterSearch_log_content select[tabindex="-1"], .filterSearch_log_content input').each(function () {
			                filterTable[$(this).attr("name")] = $(this).val();
			            });
			            d.filter = filterTable;
			            return d;
			        },
			    },
			    autoWidth: false,
			    columnDefs: [{
			            orderable: false,
			            targets: 'no-sort'
			        }, {
			            className: 'text-center',
			            targets: 'text-center'
			        }, {
			            className: 'text-right',
			            targets: 'text-right'
			        }, {
			            className: 'no-padding',
			            targets: 'no-padding'
			        }, {
			            className: 'td-no-padding',
			            targets: 'td-no-padding'
			        }, {
			            className: 'th-no-padding',
			            targets: 'th-no-padding'
			        }, {
			            className: 'td-image',
			            targets: 'image'
			        }, {
			            visible: false,
			            targets: 'hide'
			        }
			    ],
			    order: [
			        [$('#tbl_kb_log th.default-sort').index(), $('#tbl_kb_log th.default-sort').attr('data-sort')]
			    ],
			    orderCellsTop: true,
			    language: {
			        paginate: {
			            'next': '<i class="icon-arrow-right15"></i>',
			            'previous': '<i class="icon-arrow-left15"></i>'
			        }
			    },
			    "initComplete": function(settings, json) {
			    	
			    },
			    
		};
	}

	load_default = () => {
		this.datepickerByChecked();
		$('.filterSearch_log_content').find('select[tabindex!="-1"]').removeClass('form-control');
		$('.filterSearch_log_content').find('select[tabindex!="-1"]').removeClass('form-control-sm');
		$('.filterSearch_log_content').find('select[tabindex!="-1"]').select2({
		    allowClear: true
		});
	}

	datepickerByChecked = () => {
		const t = $('#dateCheckedLogContent');
		if (!t.is(':checked')) {
			
			$('#filter_emp_datetimepicker1').removeClass('hide');
			$('#filter_emp_datetimepicker2').removeClass('hide');

			// $('#datetimepicker1_by_employee').val(dateNowLogContent);
			// $('#datetimepicker2_by_employee').val(dateNowLogContent);

			$('#filter_emp_datetimepicker1').datetimepicker({
			  format: 'yyyy-MM-dd',autoclose: true, minView: 2,pickTime: false,
			});

			$('#filter_emp_datetimepicker2').datetimepicker({
			  format: 'yyyy-MM-dd',autoclose: true, minView: 2,pickTime: false,
			});
		}
		else
		{
			$('#datetimepicker1_by_employee').val('');
			$('#datetimepicker2_by_employee').val('');

			$('#filter_emp_datetimepicker1').addClass('hide');
			$('#filter_emp_datetimepicker2').addClass('hide');
		}
	}

	dataTableGenerate = () => {
		var columns = [];
		$('#tbl_kb_log .column th').each(function () {
		    columns.push({
		        data: $(this).attr('data-data')
		    });
		});
		this.param.table_config.columns = columns;
		this.param.sel_table = $('#tbl_kb_log').DataTable(this.param.table_config);

	}
}

const log_content = new Class_kb_log();

$(document).ready(function(e){
	log_content.load_default();
	log_content.dataTableGenerate();

	let tbl = log_content.param.sel_table;
	oTable = tbl;

	$('body').on('keyup', '.filterSearch_log_content input', function (e) {
	    if (e.keyCode == 13) {
	        tbl.ajax.reload((e) => {
	            
	        });
	    }
	});

	$('body').on('change', '.filterSearch_log_content select', function (e) {
	    tbl.ajax.reload((e) => {
	        
	    });
	});

	$('.btnSearchLogContent').click(function(e){
		tbl.ajax.reload((e) => {
		    
		});
	})
})