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
    min-width: 1050px;
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

<div class='table-container'>
  <div id='invisible-wrapper'>
    <table class="table table-hover sortable" style='margin-top: 15px; table-layout: fixed;'>
      <thead>
        <tr>
          <th id='name_header' class='sortBy no-select sorttable_alpha'>Name</th>
          <th class='sortBy no-select sorttable_alpha' id='room'>Room</th>
          <th class='sortBy no-select sorttable_ddmm' id='contract_start'>Contract start</th>
          <th class='sortBy no-select sorttable_ddmm' id='contract_end'>Contract end</th>
          <th class='sortBy no-select sorttable_alpha' id='energy_use'>Energy use</th>
          <th class='sortBy no-select sorttable_numeric' id='balance'>Balance</th>
          <th class='sortBy no-select sorttable_numeric' id='funds_added'>Funds Added</th>
        </tr>
      </thead>
      <tbody class='table-body'>
      <?php 
        foreach ($users as $user) {
          if (!$user['active']) {
            echo '<tr class="deactivated">';
          } else {
            echo '<tr>';
          }

          //begin adding tds
          echo '<td class="name" id="' . $user['User']['id'] . '">' . $user['User']['full_name'] . '</td>';
          echo '<td>' . $user['Room']['name'] . '</td>';
          if ($user['primary_contract']['start_date'] != "None") {
            echo '<td>' . $user['primary_contract']['start_date'] . '</td>';
          } else {
            echo '<td>None</td>';
          }
          if ($user['primary_contract']['end_date'] != "None") {
            echo '<td>' . $user['primary_contract']['end_date'] . '</td>';
          } else {
            echo '<td>None</td>';
          }
          echo '<td>' . number_format($user['User']['wh']/1000, 2) . ' kWh </td>';
          echo '<td>' . '€' . number_format($user['User']['balance'], 2) . '</td>';
           echo '<td>' . '€' . number_format($user['User']['funds_added'], 2) . '</td>';
          echo '</tr>';
        }
      ?>
      </tbody>
    </table>
  </div>
  <div id='table_paginate'>
  </div>
</div>
<div style='text-align: center; width: 100%; margin-top: 10px;'>
<?php echo '<a href="' . $email_link . '"><button class="btn btn-success">Send email</button></a>'; ?>
</div>