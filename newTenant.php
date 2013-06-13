<!DOCTYPE html>
<html lang="en">
<head>

    <title>TV48 - Registration</title>
    <title>TV48</title>
    <meta charset="utf-8">
    <? include('base.php'); ?>

    <script>
        function checkPassword(input) {
            if (input.value != document.getElementById('password').value) {
                input.setCustomValidity('Passwords must match');
            } else {
                input.setCustomValidity('');
            }
        }
    </script>

    <!--[if lt IE 9]>
        <style>

            .hide-this {
                display: none;
            }

        </style>
    <![endif]-->

</head>
<body>
    <? include('header.php'); ?>
    Add Tenant
    <? include('header2.php'); ?>
    <form style='text-align: center;' action='tenantRegister.php' method='POST'>
        <input type='email' class='centered' style='display: block;' id='email' name='email' placeholder='Confirm Email...' required>
        <input type='password' class='centered' style='display: block;' id='password' name='password' placeholder='Password...' required>
        <input type='password' class='centered' style='display: block;' id='password2' name='confirm_password' placeholder='Confirm Password...' oninput="checkPassword(this)" required>
        <input type='text' class='centered' style='display: block;' id='address' name='address' placeholder='Address...'>
        <input type='tel' class='centered' style='display: block;' id='phone_number' name='phone_number' placeholder='Phone number...'>
        <input type="hidden" name="session" value="<? session_start(); echo session_id() ?>" />
        <input type='submit' value='Submit' class='btn btn-success'>
    </form>
    </div>
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker();
        });
    </script>
</body>
</html>