<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PaperAirPlane 管理后台</title>
    <meta name="user" content="{{$user}}" />
    <link rel="stylesheet" media="all" href={{mix('css/admin.css')}}>
</head>

<body class="antialiased">
    <div id="root">
    </div>

    <script src={{mix('js/admin.js')}}></script>
</body>

</html>
