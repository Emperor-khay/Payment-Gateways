<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payment</title>
</head>
<body>
        @if(session('success'))
            <p>{{ session('success') }}</p>
        @endif
        
        @if(session('error'))
            <p>{{ session('error') }}</p>
        @endif

        <h1>Payment</h1>
        <form action="{{ route('process_payment') }}" method="POST">
            @csrf
            <input type="text" name="name" placeholder="Enter Name" reqquired>
            <input type="text" name="email" placeholder="Enter Email" reqquired>
            <input type="number" name="amount" placeholder="Enter Amount" reqquired>
            <button type="submit">Pay</button>

        </form>

</body>
</html>
