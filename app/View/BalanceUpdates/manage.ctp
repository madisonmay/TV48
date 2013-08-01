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

    $('body').on('click', '.btn-delete', function() {
      var id = $(this).parent().parent().attr('id');
      window.table_row = $(this).parent().parent();
      $.post('/balance_updates/remove', {'id': id}, function(response) {
        console.log(response);
        if (response == 1) {
          $(window.table_row).remove();
        }
      })
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
    height: 20px;
    max-height: 20px;
    text-align: center;
    white-space: nowrap; 
    overflow: hidden;

  }

  .btn-delete:hover, .btn-delete {
    color: red !important;
  }

</style>

<div class='table-container'>
  <div id='invisible-wrapper'>
    <table class="table table-hover sortable" style='margin-top: 15px; table-layout: fixed;'>
      <thead>
        <tr>
          <th id='name_header' class='sortBy no-select sorttable_alpha'>Name</th>
          <th class='sortBy no-select sorttable_alpha' id='description'>Description</th>
          <th class='sortBy no-select sorttable_numeric' id='cost'>Cost</th>
          <th class='no-select sorttable_ddmm' id='date'>Date</th>
          <th class='delete'>Delete</th>
        </tr>
      </thead>
      <tbody class='table-body'>
      <?php 
        foreach ($updates as $update) {
          echo '<tr id=' . $update['BalanceUpdate']['id'] . '>';

          //begin adding tds
          echo '<td class="name" id="' . $update['User']['id'] . '">' . $update['User']['full_name'] . '</td>';
          echo '<td>' . $update['BalanceUpdate']['text'] . '</td>';
          echo '<td>'  . '€' .  number_format($update['BalanceUpdate']['delta'], 2) . '</td>';
          echo '<td>';
          date_default_timezone_set('europe/brussels');
          $date = strftime("%B %d, %Y", $update['BalanceUpdate']['delta']);
          echo $date;
          echo '</td>';
          echo '<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="btn-delete">x</a></td>';
          echo '</tr>';
        }
      ?>
      </tbody>
    </table>
  </div>
  <div id='table_paginate'>
  </div>
</div>
