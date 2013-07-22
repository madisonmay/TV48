<script src='/js/sorttable.js'></script>

<script>
  function table_prep() {
    $('#name_header').click().click();
    var length = $('.sortable tbody').find('tr').length;
    var num = Math.ceil(length/10);
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
      console.log(n);
      $('tbody').find('tr').show();
      $('tbody').find('tr:lt(' + n*10 + ')').hide();
      $('tbody').find('tr:gt(' + (n+1)*10 + ')').hide();
    });

    //
    $('.sortBy').click(function() {
      if (window.lastClicked != $(this).attr('id')) {
        window.lastClicked = $(this).attr('id');
        $(this).click();
      }
      var first_page = $($('#table_paginate').children(".round")[0]);
      setTimeout(function() {first_page.click();}, 10);
    })
  })
</script>

<style>

  thead tr th {
    cursor: pointer !important;
  }

  .name {
    color: #46a564;
    cursor: pointer;
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

</style>

<div style='height: 525px; background-color: white; border: 2px solid #333; border-radius: 10px !important; padding-left: 10px; padding-right: 10px; padding-bottom: 5px; padding-top: 5px;'>
<table class="table table-hover sortable" style='margin-top: 15px;'>
  <thead>
    <tr>
      <th id='name_header' class='sortBy'>Name</th>
      <th class='sortBy' id='room'>Room</th>
      <th class='sortBy' id='contract_start'>Contract start</th>
      <th class='sortBy' id='contract_end'>Contract end</th>
      <th class='sortBy' id='balance'>Balance</th>
      <th class='sortBy' id='energy_use'>Energy use</th>
      <th class='sortBy' id='funds_added'>Funds Added</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class='name'>Madison May</td>
      <td>Dorm 1</td>
      <td>01-01-2012</td>
      <td>01-01-2014</td>
      <td>€100</td>
      <td>250 kWh</td>
      <td>€150</td>
    </tr>
    <tr>
      <td class='name'>Jacob Kingery</td>
      <td>Dorm 2</td>
      <td>01-01-2012</td>
      <td>01-01-2014</td>
      <td>€200</td>
      <td>250 kWh</td>
      <td>€250</td>
    </tr>
    <tr class='deactivated'>
      <td class='name'>Marge de Galle</td>
      <td>Studio 1</td>
      <td>08-01-2013</td>
      <td>01-01-2014</td>
      <td>€300</td>
      <td>250 kWh</td>
      <td>€350</td>
    </tr>
    <tr>
      <td class='name'>Madison May</td>
      <td>Dorm 1</td>
      <td>01-01-2012</td>
      <td>01-01-2014</td>
      <td>€100</td>
      <td>250 kWh</td>
      <td>€150</td>
    </tr>
    <tr>
      <td class='name'>Jacob Kingery</td>
      <td>Dorm 2</td>
      <td>01-01-2012</td>
      <td>01-01-2014</td>
      <td>€200</td>
      <td>250 kWh</td>
      <td>€250</td>
    </tr>
    <tr class='deactivated'>
      <td class='name'>Charles de Galle</td>
      <td>Studio 1</td>
      <td>08-01-2013</td>
      <td>01-01-2014</td>
      <td>€300</td>
      <td>250 kWh</td>
      <td>€350</td>
    </tr>
    <tr>
      <td class='name'>Madison May</td>
      <td>Dorm 1</td>
      <td>01-01-2012</td>
      <td>01-01-2014</td>
      <td>€100</td>
      <td>250 kWh</td>
      <td>€150</td>
    </tr>
    <tr>
      <td class='name'>Jacob Kingery</td>
      <td>Dorm 2</td>
      <td>01-01-2012</td>
      <td>01-01-2014</td>
      <td>€200</td>
      <td>250 kWh</td>
      <td>€250</td>
    </tr>
    <tr class='deactivated'>
      <td class='name'>Charles de Galle</td>
      <td>Studio 1</td>
      <td>08-01-2013</td>
      <td>01-01-2014</td>
      <td>€300</td>
      <td>250 kWh</td>
      <td>€350</td>
    </tr>
    <tr>
      <td class='name'>Madison May</td>
      <td>Dorm 1</td>
      <td>01-01-2012</td>
      <td>01-01-2014</td>
      <td>€100</td>
      <td>250 kWh</td>
      <td>€150</td>
    </tr>
    <tr>
      <td class='name'>Jacob Kingery</td>
      <td>Dorm 2</td>
      <td>01-01-2012</td>
      <td>01-01-2014</td>
      <td>€200</td>
      <td>250 kWh</td>
      <td>€250</td>
    </tr>
    <tr class='deactivated'>
      <td class='name'>Charles de Galle</td>
      <td>Studio 1</td>
      <td>08-01-2013</td>
      <td>01-01-2014</td>
      <td>€300</td>
      <td>250 kWh</td>
      <td>€350</td>
    </tr>
    <tr>
      <td class='name'>Madison May</td>
      <td>Dorm 1</td>
      <td>01-01-2012</td>
      <td>01-01-2014</td>
      <td>€100</td>
      <td>250 kWh</td>
      <td>€150</td>
    </tr>
    <tr>
      <td class='name'>Jacob Kingery</td>
      <td>Dorm 2</td>
      <td>01-01-2012</td>
      <td>01-01-2014</td>
      <td>€200</td>
      <td>250 kWh</td>
      <td>€250</td>
    </tr>
    <tr class='deactivated'>
      <td class='name'>Charles de Galle</td>
      <td>Studio 1</td>
      <td>08-01-2013</td>
      <td>01-01-2014</td>
      <td>€300</td>
      <td>250 kWh</td>
      <td>€350</td>
    </tr>
    <tr>
      <td class='name'>Madison May</td>
      <td>Dorm 1</td>
      <td>01-01-2012</td>
      <td>01-01-2014</td>
      <td>€100</td>
      <td>250 kWh</td>
      <td>€150</td>
    </tr>
    <tr>
      <td class='name'>Jacob Kingery</td>
      <td>Dorm 2</td>
      <td>01-01-2012</td>
      <td>01-01-2014</td>
      <td>€200</td>
      <td>250 kWh</td>
      <td>€250</td>
    </tr>
    <tr class='deactivated'>
      <td class='name'>Charles de Galle</td>
      <td>Studio 1</td>
      <td>08-01-2013</td>
      <td>01-01-2014</td>
      <td>€300</td>
      <td>250 kWh</td>
      <td>€350</td>
    </tr>
    <tr>
      <td class='name'>Madison May</td>
      <td>Dorm 1</td>
      <td>01-01-2012</td>
      <td>01-01-2014</td>
      <td>€100</td>
      <td>250 kWh</td>
      <td>€150</td>
    </tr>
    <tr>
      <td class='name'>Jacob Kingery</td>
      <td>Dorm 2</td>
      <td>01-01-2012</td>
      <td>01-01-2014</td>
      <td>€200</td>
      <td>250 kWh</td>
      <td>€250</td>
    </tr>
    <tr class='deactivated'>
      <td class='name'>Charles de Galle</td>
      <td>Studio 1</td>
      <td>08-01-2013</td>
      <td>01-01-2014</td>
      <td>€300</td>
      <td>250 kWh</td>
      <td>€350</td>
    </tr>
    <tr>
      <td class='name'>Madison May</td>
      <td>Dorm 1</td>
      <td>01-01-2012</td>
      <td>01-01-2014</td>
      <td>€100</td>
      <td>250 kWh</td>
      <td>€150</td>
    </tr>
    <tr>
      <td class='name'>Jacob Kingery</td>
      <td>Dorm 2</td>
      <td>01-01-2012</td>
      <td>01-01-2014</td>
      <td>€200</td>
      <td>250 kWh</td>
      <td>€250</td>
    </tr>
    <tr class='deactivated'>
      <td class='name'>Charles de Galle</td>
      <td>Studio 1</td>
      <td>08-01-2013</td>
      <td>01-01-2014</td>
      <td>€300</td>
      <td>250 kWh</td>
      <td>€350</td>
    </tr>
    <tr>
      <td class='name'>Madison May</td>
      <td>Dorm 1</td>
      <td>01-01-2012</td>
      <td>01-01-2014</td>
      <td>€100</td>
      <td>250 kWh</td>
      <td>€150</td>
    </tr>
    <tr>
      <td class='name'>Jacob Kingery</td>
      <td>Dorm 2</td>
      <td>01-01-2012</td>
      <td>01-01-2014</td>
      <td>€200</td>
      <td>250 kWh</td>
      <td>€250</td>
    </tr>
    <tr class='deactivated'>
      <td class='name'>Charles de Galle</td>
      <td>Studio 1</td>
      <td>08-01-2013</td>
      <td>01-01-2014</td>
      <td>€300</td>
      <td>250 kWh</td>
      <td>€350</td>
    </tr>
    <tr>
      <td class='name'>Madison May</td>
      <td>Dorm 1</td>
      <td>01-01-2012</td>
      <td>01-01-2014</td>
      <td>€100</td>
      <td>250 kWh</td>
      <td>€150</td>
    </tr>
    <tr>
      <td class='name'>Jacob Kingery</td>
      <td>Dorm 2</td>
      <td>01-01-2012</td>
      <td>01-01-2014</td>
      <td>€200</td>
      <td>250 kWh</td>
      <td>€250</td>
    </tr>
    <tr class='deactivated'>
      <td class='name'>Charles de Galle</td>
      <td>Studio 1</td>
      <td>08-01-2013</td>
      <td>01-01-2014</td>
      <td>€300</td>
      <td>250 kWh</td>
      <td>€350</td>
    </tr>
  </tbody>
</table>
<div id='table_paginate' style='margin-left: auto; margin-right: auto; width: 100%; text-align: center;'>
  <div class="round selected-page">
  </div>
</div>
</div>
