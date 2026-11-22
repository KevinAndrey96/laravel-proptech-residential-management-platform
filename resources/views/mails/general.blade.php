<html lang="en">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <img src="{{App\Models\Setting::find(1)?->logo ? asset(App\Models\Setting::find(1)->logo) : asset('assets/images/logo.png')}}" width="100px">
        <p><h3 style="color:#8A0829;">{!! $text !!} </h3></p>
    </body>
    <footer><img src="{{asset('assets/images/red-footer.png')}}" width="100%" height="250px"></footer>
</html>
