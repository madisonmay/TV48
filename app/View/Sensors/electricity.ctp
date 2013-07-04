<script>
  $(document).ready(function() {
    $("select").selectpicker();

    $('#duration').bind('keypress', function (event) {
        var regex = new RegExp("[0-9\.]+");
        var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
        if (!regex.test(key)) {
           event.preventDefault();
           return false;
        }
    });
  });

</script>

<div id='chart'>
    <div id="inner_chart", style="text-align: center">
    </div>
</div>


<!-- simple gui for user interaction -- change time plotted or dataset -->
<!-- eventually should also allow for datasets that do not contain the current value -->

<div id="edit">
  <div style='text-align: center; display: block; margin-left: auto; margin-right: auto; margin-top: 20px;'>
    <select id='feed'>
    </select>
    <input id='duration' type='text' pattern='[0-9\.]*' class='number-input' style='height: 42px; padding-right: 10px;'>
    <select id='units'>
      <option value="seconds">seconds</option>
      <option value="minutes">minutes</option>
      <option value="hours">hours</option>
      <option value="days">days</option>
      <option value="weeks">weeks</option>
      <option value="months">months</option>
      <!-- Option years currently causes problems -- too large of date range -->
      <!-- <option value="years">years</option> -->
    </select>
  </div>
  <div class='text-center'>
    <img id='loading' class='centered' src="/img/load.gif" style='width: 30px; display: none;'>
  </div>

  <div class='text-center'>
    <img id='settings' href="#myModal" role='button' data-toggle='modal' class='centered' src='/img/settings.png' style='width: 30px;'>
  </div>

  <!-- Modal -->

  <!-- Switch around this interface - all that is needed is a mapping betweeen users and rooms, and a way to assign rooms to be public -->
  <div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 id="myModalLabel">Graph Settings</h3>
    </div>
    <div class="modal-body" style='height: 700px;'>
      <h4>Units of Measurement</h4>
      <select id='graph-units'>
        <option value='Watts'>Watts</option>
        <option value='Euro cents per Hour'>Euros cents per Hour</option>
        <option value='Euros per Month'>Euros per Month</option>
        <option value='Euros per Year'>Euros per Year</option>
        <option value='Grams of CO2 per Hour'>Grams of CO2 per Hour</option>
        <option value='Kg of CO2 per Day'>Kg of CO2 per Day</option>
        <option value='Kg of CO2 per Month'>Kg of CO2 per Month</option>
        <option value='Kg of CO2 per Year'>Kg of CO2 per Year</option>
      </select>
    </div>
    <div class="modal-footer">
      <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
      <button class="btn btn-success unit-change">Save changes</button>
    </div>
  </div>
</div>

<script src='/js/electricity.js'></script>

