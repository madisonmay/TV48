<script>
	$(document).ready(function() {
		$('#delta').bind('keypress', function (event) {
		    var regex = new RegExp("[0-9\.]+");
		    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
		    if (!regex.test(key)) {
		       event.preventDefault();
		       return false;
		    }
		});
	});

	function submit() {
		var ids = [];
		var trs = $('.tr');
		for (var i = 0; i < trs.length; i++) {
			var checkbox_tds = $(trs[i]).children('.checkbox_td');
			var checkbox_td = checkbox_tds[0];
			var checkboxes = $(checkbox_td).children('input');
			var checkbox = checkboxes[0];
			if ($(checkbox).prop('checked') == true) {
				var names = $(trs[i]).children('.name');
				var name = names[0];
				var id = $(name).attr('id');
				ids.push(parseInt(id));
			}
		}
		var delta = parseFloat($('#delta').val());
		var text = $('#text').val();
		var data = {'delta': delta, 'text': text, 'ids': ids}
		$.post('/balance_updates/charge', data, function(response) {
			console.log(response);
			window.location = '/balance_updates';
		});
	}

	//functions too similar -- refactor
	function selectAll() {
		$('#all').toggleClass("btn-success");
		$('#all').toggleClass('dark-text');
		if ($('#all').hasClass('btn-success')) {
			$('#studio').addClass("btn-success");
			$('#studio').removeClass('dark-text');
			$('#dorm').addClass("btn-success");
			$('#dorm').removeClass('dark-text');
		} else {
			$('#studio').removeClass("btn-success");
			$('#studio').addClass('dark-text');
			$('#dorm').removeClass("btn-success");
			$('#dorm').addClass('dark-text');
		}

		var trs = $('.activated');
		for (var i = 0; i < trs.length; i++) {
			var checkbox_tds = $(trs[i]).children('.checkbox_td');
			var checkbox_td = checkbox_tds[0];
			var checkboxes = $(checkbox_td).children('input');
			var checkbox = checkboxes[0];
			if ($('#all').hasClass('btn-success')) {
				$(checkbox).prop('checked', false);
			} else {
				$(checkbox).prop('checked', true);
			}
		}
	}

	function selectDorm() {
		$('#dorm').toggleClass("btn-success");
		$('#dorm').toggleClass('dark-text');
		if ($('#studio').hasClass('btn-success') && $('#dorm').hasClass('btn-success')) {
			$('#all').removeClass('dark-text');
			$('#all').addClass("btn-success");
		} else if (!$('#studio').hasClass('btn-success') && !$('#dorm').hasClass('btn-success')) {
			$('#all').removeClass("btn-success");
			$('#all').addClass('dark-text');
		}
		var trs = $('.activated.dorm');
		for (var i = 0; i < trs.length; i++) {
			var checkbox_tds = $(trs[i]).children('.checkbox_td');
			var checkbox_td = checkbox_tds[0];
			var checkboxes = $(checkbox_td).children('input');
			var checkbox = checkboxes[0];
			if ($('#dorm').hasClass('btn-success')) {
				$(checkbox).prop('checked', false);
			} else {
				$(checkbox).prop('checked', true);
			}
		}
	}

	function selectStudio() {
		$('#studio').toggleClass("btn-success");
		$('#studio').toggleClass('dark-text');
		if ($('#studio').hasClass('btn-success') && $('#dorm').hasClass('btn-success')) {
			$('#all').removeClass('dark-text');
			$('#all').addClass("btn-success");
		} else if (!$('#studio').hasClass('btn-success') && !$('#dorm').hasClass('btn-success')) {
			$('#all').removeClass("btn-success");
			$('#all').addClass('dark-text');
		}
		var trs = $('.activated.studio');
		for (var i = 0; i < trs.length; i++) {
			var checkbox_tds = $(trs[i]).children('.checkbox_td');
			var checkbox_td = checkbox_tds[0];
			var checkboxes = $(checkbox_td).children('input');
			var checkbox = checkboxes[0];
			if ($('#studio').hasClass('btn-success')) {
				$(checkbox).prop('checked', false);
			} else {
				$(checkbox).prop('checked', true);
			}
		}
	}
</script>

<style>

	.label-container {
		position: absolute !important;
		top: 50px !important;
	}

	#container-left {
		width: 50%;
		float: left;
	}

	#container-right {
		width: 50%;
		float: right;
	}

	#label-left {
		float: right; 
		margin-right: 10px; 
		width: 210px; 
		text-align: center !important;
	}

	#label-right {
		float: left; 
		margin-left: 10px; 
		width: 210px; 
		text-align: center !important;
	}

	input[type=checkbox] {
		margin-top: -3px;
	}

/*	*  {
		border: 2px black solid;
	}*/
</style>

<script src='/js/date.js'></script>
<script src='/js/sorttable.js'></script>

<script>
  function table_prep() {
    $('#name_header').click().click();
    var length = $('.sortable tbody').find('tr').length;
    var num = Math.ceil(length/10);

    //make sure more than 10 entries are present before paginating
    if (num>1) {
      $('#table_paginate').append('<div class="round selected-page"></div>');
    }
    for (i = 1; i < num; i++) {
      $('#table_paginate').append('<div class="round"></div>')
    }
    $('tbody').find('tr:gt(10)').hide();
  }

  $(document).ready(function() {
    setTimeout(function() {table_prep()}, 0);

    $('body').on('click', '.round', function() {
      $('.round').removeClass('selected-page');
      $(this).addClass('selected-page');
      var n = $(this).index();
      $('tbody').find('tr').css('display', 'none');
      $('tbody').find('tr').show();
      $('tbody').find('tr:lt(' + n*10 + ')').hide();
      $('tbody').find('tr:gt(' + (n+1)*10 + ')').hide();
    });

    //
    $('body').on('click', '.sortBy', function() {
      if (window.lastClicked != $(this).attr('id')) {
        window.lastClicked = $(this).attr('id');
        $(this).click();
      }
      var first_page = $($('#table_paginate').children(".round")[0]);
      setTimeout(function() {first_page.click();}, 10);
    })

    $('body').on('click', '.name', function() {
      window.location = '/users/profile?id=' + $(this).attr('id');
    });
  })
</script>

<style>

  body {
    min-width: 800px;
  }

  thead tr th {
    cursor: pointer !important;
  }

  .name {
    color: #46a564;
    cursor: pointer;
    -webkit-user-select: none;  
    -moz-user-select: none;    
    -ms-user-select: none;      
    user-select: none;
  }

  .no-select {
    -webkit-user-select: none;  
    -moz-user-select: none;    
    -ms-user-select: none;      
    user-select: none;
  }

  .deactivated .name{
    color: #9d261d;
  }

  .round {
    display: inline-block;
    margin-left: 3px;
    margin-right: 3px;
    width: 15px;
    height: 15px;
    border-radius: 15px;
    border: 1px #333 solid;
  }

  .selected-page {
    background-color: #46a564;
  }

  td, th {
    text-align: center;
  }

</style>

<div>
<div id='label-container'>
	<span id='container-left'>
		<div id='label-left'>
			<div for='text' class='centered' style='font-size: 25px;'>Description</div>
			<input type='text' id='text' class='centered'>
		</div>
	</span>
	<span id='container-right'>
		<div id='label-right'>
			<div for='delta' class='centered' style='font-size: 25px;'>Total Cost</div>
			<input type='text' id='delta' id='cost' pattern='[0-9\.]+' class='centered'>
		</div>
	</span>
</div>
<div class='table-container' style='position: absolute; margin-left: -10px; margin-right: 15px; margin-top: 100px;'>
  <div id='invisible-wrapper'>
    <table class="table table-hover sortable" style='margin-top: 15px; table-layout: fixed;'>
      <thead>
        <tr>
          <th id='name_header' class='sortBy no-select sorttable_alpha'>Name</th>
          <th class='checkbox_th' style='width: 40px;'>Charge</th>
          <th class='sortBy no-select sorttable_alpha' id='room'>Room</th>
          <th class='sortBy no-select sorttable_alpha' id='room_type'>Room Type</th>
<!--      <th class='sortBy no-select sorttable_ddmm' id='contract_start'>Contract start</th>
          <th class='sortBy no-select sorttable_ddmm' id='contract_end'>Contract end</th> -->
          <th class='sortBy no-select sorttable_numeric' id='balance'>Balance</th>
        </tr>
      </thead>
      <tbody class='table-body'>
      <?php 
      	function addEntry($users) {
	        foreach ($users as $user) {
	          if (!$user['active']) {
	            echo '<tr class="tr deactivated ' . $user['Room']['type'] . '">';
	          } else {
	            echo '<tr class="tr activated ' . $user['Room']['type'] . '">';
	          }

	          //begin adding tds
	          echo '<td class="name" id="' . $user['User']['id'] . '">' . $user['User']['full_name'] . '</td>';
	          echo '<td class="checkbox_td"><input type="checkbox" name="charge" style="width: 40px"></td>';
	          echo '<td>' . $user['Room']['name'] . '</td>';
	          echo '<td>' . ucfirst($user['Room']['type']) . '</td>';
	          // echo '<td>' . $user['primary_contract']['start_date'] . '</td>';
	          // echo '<td>' . $user['primary_contract']['end_date'] . '</td>';
	          echo '<td>' . '€' . number_format($user['User']['balance'], 2) . '</td>';
	          echo '</tr>';
	        }
	    }
	    addEntry($users);
      ?>
      </tbody>
    </table>
  </div>
  <div id='table_paginate'>
  </div>
  <div style='text-align: center; position: absolute; top: 450px; left: 50%; margin-left: -250px;'>
  	<button type='button' id='dorm' onclick='selectDorm()' class='btn btn-success'>Current Dorm Owners</button>
  	<button type='button' id='all' onclick='selectAll()' class='btn btn-success'>Current Tenants</button>
  	<button type='button' id='studio' onclick='selectStudio()' class='btn btn-success'>Current Studio Owners</button>
  </div>
  <div style='text-align: center; position: absolute; top: 500px; left: 50%; margin-left: -45px;'>
  	<button type='button' id='submit' onclick='submit()' class='btn btn-danger'>Submit</button>
  </div>
</div>
<div>