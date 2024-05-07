<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PMS Invoice</title>
</head>
<body>
    <h1>Payment recived successfully</h1>
    <p>This is an example email sent from <span style="color: blue;"> PMS </span></p>
    <p>Your payment date is {{ $data['payment_date'] }}</p>
    <p>Your amount is <b>{{ $data['amount'] }}</b></p>
</body>
</html>