<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TICKETS - Laravel</title>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            background-color: black;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }
        .container {
            background: black;
            padding: 2rem 3rem;
            border-radius: 8px;
            border: 3px solid #e22424ff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            max-width: 600px;
        }
        h1 {
            margin-bottom: 1rem;
            color: #e22424ff;
        }
        p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenido a TICKETS Laravel</h1>
        <p>Tu aplicación Laravel está lista para usarse.</p>
        <p>Este apartado es para el login</p>
    </div>
</body>
</html>
