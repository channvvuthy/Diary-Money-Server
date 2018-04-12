<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>

<div>
    Hi {{ $email }},
    <br>
    Thank you for creating an account with us. Don't forget to complete your registration!
    <br>
    Please copy code below and paste it into your verify form
    <br>
    <p>Here is your verify code: </p>
    {{$verification_code}}
    <br/>
</div>

</body>
</html>

