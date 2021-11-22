<!DOCTYPE html>
<html lang="en" >

<head>

    @php
        $title = App\Models\GeneralSetting::find(1)->business_name;
    @endphp

  <meta charset="UTF-8">
  <title>{{ $title }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <style>
        #oopss {
            position: fixed;
            left: 0px;
            top: 0;
            width: 100%;
            height: 100%;
            line-height: 1.5em;
            z-index: 9999;
        }
        #oopss #error-text {
            font-size: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: 'Shabnam', Tahoma, sans-serif;
            color: #000;
            direction: rtl;
        }
        #oopss #error-text img {
            margin: 85px auto 20px;
            height: 342px;
        }
        #oopss #error-text span {
            position: relative;
            font-size: 3.3em;
            font-weight: 900;
            margin-bottom: 50px;
        }
        #oopss #error-text p.p-a {
            font-size: 19px;
            margin: 30px 0 15px 0;
        }
        #oopss #error-text p.p-b {
            font-size: 15px;
        }
        #oopss #error-text .back {
            background: #fff;
            color: #000;
            font-size: 30px;
            text-decoration: none;
            margin: 2em auto 0;
            padding: .7em 2em;
            border-radius: 500px;
            box-shadow: 0 20px 70px 4px rgba(0, 0, 0, 0.1), inset 7px 33px 0 0px #fff300;
            font-weight: 900;
            transition: all 300ms ease;
        }
        #oopss #error-text .back:hover {
            -webkit-transform: translateY(-13px);
            transform: translateY(-13px);
            box-shadow: 0 35px 90px 4px rgba(0, 0, 0, 0.3), inset 0px 0 0 3px #000;
        }
    </style>
</head>

<body>

  <div id='oopss'>
    <div id='error-text'>
        <img src="https://cdn.rawgit.com/ahmedhosna95/upload/1731955f/sad404.svg" alt="404">

        <p class="p-a">
           {{__("payment cann't complete something went wrong")}}</p>
        <button class="back">{{__('... Back to App')}}</button>
    </div>
</div>

</body>

</html>
