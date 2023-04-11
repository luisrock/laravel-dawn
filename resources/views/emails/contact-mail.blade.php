<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Contact Email') }}</title>
</head>
<body>
    <h3>Contact Form Submission</h3>
    <p><strong>{{ __('Name:') }}</strong> {{ $name }}</p>
    <p><strong>{{ __('Email:') }}</strong> {{ $email }}</p>
    <p><strong>{{ __('Subject:') }}</strong> {{ $subject }}</p>
    <p><strong>{{ __('Message:') }}</strong></p>
    <p>{{ $content }}</p>
</body>
</html>