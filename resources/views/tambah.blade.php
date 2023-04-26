
<style type="text/css">
	input, select, textarea {
		text-transform: UPPERCASE;
		padding: 5px;
	}
    .select2-container {
  z-index: 1051 !important;
}

.modal {
  z-index: 1052 !important;
}
</style>
<meta name="csrf-token" content="{{ csrf_token() }}" />
<form id="customerForm" method="POST">
@csrf
    <table width="100%" cellspacing="0" id="addData">
        <tr>
            <td>
                <label>No.Invoice</label>
            </td>
          
            <td>
                <input type="text" id="no_invoice1" name="no_invoice" class="FormElement ui-widget-content ui-corner-all autofocus" autofocus autocomplete="off" disabled>
            </td>
         
        </tr>
      
        <tr>
            <td>
                <label>Tanggal Pembelian</label>
            </td>
            <td>
                <input type="text" id="tgl_pembelian1" name="tgl_pembelian" class="FormElement ui-widget-content ui-corner-all setDate " required autocomplete="off" maxlength="10" required>
            </td>
        </tr>
		<tr>
            <td>
                <label>Nama Pelanggan</label>
            </td>
            <td>
                <input type="text" id="nama_pelanggan1" name="nama_pelanggan" class="FormElement ui-widget-content ui-corner-all" required autocomplete="off">
            </td>
        </tr>
        <tr>
            <td>
                <label>Jenis Kelamin</label>
            </td>
            <td>
                <select id="jenis_kelamin1"  class="jenis_kelamin" name="jenis_kelamin" required style="overflow:hidden;">
       
                    <option value="LAKI-LAKI">LAKI-LAKI</option>
                    <option value="PEREMPUAN">PEREMPUAN</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <label>Saldo</label>
            </td>
            <td>
                <input type="text" id="saldo1" name="saldo" class="FormElement ui-widget-content ui-corner-all im-currency" required autocomplete="off">
            </td>
        </tr>
    </table>
  
    
    <br>
    <table width="100%" class="table ui-state-default detail-row" cellpading="5" cellspacing="0" id="detailData">
        <thead>
			<tr>
				<th class="ui-th-div">Nama Barang</th>
				<th class="ui-th-div">Qty</th>
				<th class="ui-th-div">Harga</th>
				<th class="ui-th-div">Action</th>
			</tr>
		</thead>
        <tbody>
            <tr>
                <td>
                    <input type="text" name="nama_barang[]" id="nama_barang" class="FormElement ui-widget-content ui-corner-all" required autocomplete="off">
                </td>
                <td>
                    <input type="text" name="qty[]" id="qty" class="FormElement ui-widget-content ui-corner-all im-currency" required autocomplete="off">
                </td>
                <td>
                    <input type="text" name="harga[]" id="harga" class="FormElement ui-widget-content ui-corner-all im-currency" required autocomplete="off">
                </td>
                <td>
					<a href="javascript:">
						<span class="ui-icon ui-icon-trash" onclick="$(this).parent().parent().parent().remove()"></span>
					</a>
				</td>
            </tr>
            <tr>
				<td colspan="3"></td>
				<td>
					<a href="javascript:" onclick="addRow(); setNumericFormat(); formBindKeys();">
						<span class="ui-icon ui-icon-plus" ></span>
					</a>
				</td>
			</tr>
        </tbody>
    </table>
    </form>
<script>
    $(document).ready(function() {
		let index = 0

		setDateFormat()
		setNumericFormat()
        setSelect2()
        formBindKeys()
        autoFocus()
	})

    function autoFocus()
    {
        $('.autofocus').focus();
    }

    function addRow()
    {
        console.log($('#detailData tbody tr'));
        $('#detailData tbody tr').last().before(`
            <tr>
                <td>
                    <input type="text" name="nama_barang[]" id="nama_barang" class="FormElement ui-widget-content ui-corner-all" required autocomplete="off">
                </td>
                <td>
					<input type="text" name="qty[]" id="qty" class="FormElement ui-widget-content ui-corner-all im-currency" required autocomplete="off">
				</td>
				<td>
					<input type="text" name="harga[]" id="harga" class="FormElement ui-widget-content ui-corner-all im-currency" required autocomplete="off">
				</td>
                <td>
					<a href="javascript:">
						<span class="ui-icon ui-icon-trash" onclick="$(this).parent().parent().parent().remove()"></span>
					</a>
				</td>
                
            </tr>
            
        `)
    }

    function setDateFormat() 
    {
        $('.setDate').datepicker({
			dateFormat: 'dd-mm-yy',
		}).inputmask({
			alias: "datetime",
            mask: "1-2-y",
            separator: "-"
		})
    }

    function setSelect2() {
        // console.log('set select2 on add');
  $('.jenis_kelamin').select2({
    dropdownParent: $('.jenis_kelamin').parent(),
  });
}

    function setNumericFormat() {
        $('.im-numeric').keypress(function(e){
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) 
            {
                return false;
            }
        })

        $('.im-currency').inputmask('integer', {
            alias: 'numeric',
            groupSeparator: '.',
            autoGroup: true,
			digitsOptional: false,
			allowMinus: false,
			placeholder: '',
        })
    }

    function formBindKeys() {
		let inputs = $('#customerForm [name]:not(:hidden)')
		let element
		let position

		inputs.each(function(i, el) {
			$(el).attr('data-input-index', i)
		})

		$(inputs[0]).focus()

		inputs.focus(function() {
			$(this).data('input-index')
		})

		inputs.keydown(function(e) {
			let operator
			switch(e.keyCode) {
				case 38:
					element = $(inputs[$(this).data('input-index') - 1])
					if (element.is(':not(select)') && element.attr('type') !== 'email') {
						position = element.val().length
						element[0].setSelectionRange(position, position)
					}
					element.hasClass('hasDatePicker')
						? $('.ui-datepicker').show()
						: $('.ui-datepicker').hide()
					element.focus()
					break
				case 40:
					element = $(inputs[$(this).data('input-index') + 1])
					if (element.is(':not(select)') && element.attr('type') !== 'email') {
						position = element.val().length
						element[0].setSelectionRange(position, position)
					}
					element.hasClass('hasDatePicker')
						? $('.ui-datepicker').show()
						: $('.ui-datepicker').hide()
					element.focus()
					break
			}
		})
	}

   
</script>