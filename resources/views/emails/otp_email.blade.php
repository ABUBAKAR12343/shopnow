<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Our Platform</title>
</head>
<body>
    <h1>Welcome, {{ $user->name }}!</h1>
    <p>Below's your password update link. Clink the link to update password!</p>

    <ul>
        <li>Name: {{ $user->name }}</li>
        <li>Email: {{ $user->email }}</li>
        <li>Password Update Link: {{ $url }}</li>
    </ul>

    <p>Thank you for joining us!</p>
</body>
</html>
