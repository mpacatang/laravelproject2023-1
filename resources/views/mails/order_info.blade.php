<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"
      xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta content="width=device-width" name="viewport"/>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type"/>
    <title>Shopy</title>

</head>
<body style="font-family: Nunito, sans-serif; font-size: 15px; font-weight: 400;">


<!-- Hero Start -->
<div style="margin-top: 50px;">
    <table cellpadding="0" cellspacing="0"
           style="max-width: 600px; margin: 0 auto; border-radius: 6px; overflow: hidden; background-color: #fff; border: 1px solid rgba(1,29,66,0.09);">
        <tbody>
        <tr>
            <td style="padding: 30px 24px; color: #161c2d; font-size: 18px; font-weight: 600; text-align: center">
                Hi, Order Information
            </td>
        </tr>
        <tr>
            <td style="padding: 15px 24px 15px; color: #404e62;">
                Here is Order OTP : {{$otp}}
            </td>
        </tr>

        <tr>
            <td style="padding: 0 24px;">
                You can find invoice <a href="{{$url}}">here</a>
            </td>
        </tr>

        <tr>
            <td style="padding: 15px 24px 0; color: #ff4f2a;">
                We recommended you to don't share to anyone
            </td>
        </tr>

        <tr style="text-align: end; font-weight: 500">
            <td style="padding: 24px 24px 24px; color: #222f42;">
                Shopy <br> Support Team
            </td>
        </tr>

        <tr>
            <td style="padding: 16px 8px; color: #001b44; background-color: #f2f5ff; text-align: center; font-weight: 600">
                ©
                <script type="text/javascript">document.write(new Date().getFullYear())</script>
                Shopy
            </td>
        </tr>
        </tbody>
    </table>
</div>
<!-- Hero End -->

</body>
</html>
