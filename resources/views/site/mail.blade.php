<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        p {
            color: #555;
        }
        .footer {
            margin-top: 20px;
            font-size: 0.9em;
            color: #aaa;
        }
    </style>
</head>
<body>
    @if ($data['dataType'] == 'contactMail')
        <div class="container">
            <h1>New Message from {{ $data['name'] }}</h1>
            <p><strong>Email:</strong> {{ $data['email'] }}</p>
            <p><strong>Phone:</strong> {{ $data['phone'] }}</p>
            <p><strong>Subject:</strong> {{ $data['subject'] }}</p>
            <p><strong>Message:</strong></p>
            <p>{{ $data['message'] }}</p>
        </div>
    @elseif ($data['dataType'] == 'joinNow')
        <div class="container">
            <h1>New Message from {{ $data['firstName'] }} {{ $data['lastName'] }}</h1>
            <p><strong>Email:</strong> {{ $data['email'] }}</p>
            <p><strong>Phone:</strong> {{ $data['phone'] }}</p>
            <p><strong>Address:</strong> {{ $data['address'] }}</p>
            <p><strong>Course:</strong> {{ $data['course'] }}</p>
            <p><strong>Queries:</strong> {{ $data['queries'] }}</p>
        </div>
    @endif
    <div class="footer">
        <p>This email was sent from your website contact form.</p>
    </div>
</body>
</html>
