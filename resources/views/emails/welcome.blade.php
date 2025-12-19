<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome Email</title>
<style>
body {margin:0;padding:0;font-family:Helvetica, Arial, sans-serif;background:#f4f4f4;}
.container {max-width:600px;margin:0 auto;background:#ffffff;padding:20px;border-radius:8px;}
h1 {color:#000;font-size:24px;margin-bottom:10px;}
p {color:#333;font-size:15px;line-height:22px;}
.button {display:inline-block;margin-top:20px;padding:12px 20px;background:#343565;color:#fff;text-decoration:none;border-radius:5px;font-weight:bold;}
.footer {font-size:12px;color:#666;text-align:center;margin-top:30px;line-height:18px;}
</style>
</head>
<body>
  <div class="container">
    <div style="text-align:center;">
      <a href="{{ url('/') }}" target="_blank">
        <img src="{{ URL::asset('/'.getcong('site_logo')) }}" alt="{{getcong('site_name')}}" style="max-width:200px;height:auto;">
      </a>
    </div>
    <h1>Welcome to {{getcong('site_name')}}, {{$name}}!</h1>
    <p>We’re excited to have you join us. Your account has been created successfully.</p>
    <p>To access your account, simply click the button below and sign in with your email:</p>
    <p><strong>Email:</strong> {{$email}}</p>

    <a href="{{ url('/login') }}" class="button" target="_blank">Login to Your Account</a>

    <p>If you didn’t create this account, please ignore this email or contact our support team.</p>

    <div class="footer">
      <p>Stay Connected: 
        <a href="{{stripslashes(getcong('footer_fb_link'))}}" target="_blank">Facebook</a> | 
        <a href="{{stripslashes(getcong('footer_instagram_link'))}}" target="_blank">Instagram</a> | 
        <a href="{{stripslashes(getcong('footer_twitter_link'))}}" target="_blank">Twitter</a>
      </p>
      <p>© {{date('Y')}} {{getcong('site_name')}}. All Rights Reserved.</p>
      <p>You received this email because you signed up at <a href="{{ url('/') }}">{{ url('/') }}</a>.<br>
      If you no longer wish to receive these emails, <a href="{{ url('/unsubscribe') }}">unsubscribe here</a>.</p>
    </div>
  </div>
</body>
</html>