<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="game_id" content="{{ $game_id }}">
    <meta name="color" content="{{ $colour }}">
    <meta name="users" content="{{ implode(",", $users) }}">


    <title>Risk</title>
    <link rel="stylesheet" href="{{ mix('css/app.css')}}">
    
</head>
<body>
 
    <div id="root" ></div>
 
    <script src="{{ mix('js/app.js') }}"></script>


 
</body>
</html>