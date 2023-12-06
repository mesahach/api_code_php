<?php

final class emailMessagesClass {
  private $notificationMessage;

  public function setNotificationMessage($title, $messageInfo, array $user_data) {

    $message = '
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<meta charset="utf-8"> 
<!-- utf-8 works for most cases -->
<meta name="viewport" content="width=device-width"> 
<meta http-equiv="X-UA-Compatible" content="IE=edge"> 
<meta name="x-apple-disable-message-reformatting">  <!-- Disable auto-scale in iOS 10 Mail entirely -->
<title></title> <!-- The title tag shows in email notifications, like Android 4.4. -->

<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- CSS Reset : BEGIN -->

</head>

<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f1f1f1;">
<center style="width: 100%; background-color: #f1f1f1;">
<div style="display: none; font-size: 1px;max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
</div>
<div style="max-width: 600px; margin: 0 auto;" class="email-container">
<!-- BEGIN BODY -->
<table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
<tr>
<td valign="top" class="bg_white" style="">
<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td class="logo" style="text-align: center;">
      <img src="'.siteLink.'/logo.png" alt="" style="width: 100%; height: 80px; margin: auto; display: block;">
      <br>
        <h1><a href="#">'.$title.'</a></h1>
      </td>
  </tr>
</table>
</td>
</tr><!-- end tr -->

<tr>
<td bgcolor="#ffffff" align="center" style="padding: 0px 30px 0px 30px; color: #666666; font-family: \'Lato\', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
<p style="margin: 0;">Hey '.$user_data['firstname'].'<br>
'.$messageInfo.'
</p>
</td>
</tr> <!-- COPY -->
</table>
</td>
</tr><!-- end tr -->
<!-- 1 Column Text + Button : END -->
</table>
<table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
<tr>
<td valign="middle" class="bg_light footer email-section">
<table>
<tr>
<td valign="top" width="33.333%" style="padding-top: 20px;">
  <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
      <td style="text-align: left; padding-right: 10px;">
        <h3 class="heading">About</h3>
        <p> here is site description</p>
      </td>
    </tr>
  </table>
</td>
<td valign="top" width="33.333%" style="padding-top: 20px;">
  <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
      <td style="text-align: left; padding-left: 5px; padding-right: 5px;">
        <h3 class="heading">Contact Info</h3>
          <ul>
            <li><span class="text">support@'.siteDomain.'</span></a></li>
            <li><span class="text">info@'.siteDomain.'</span></a></li>
          </ul>
      </td>
    </tr>
  </table>
</td>
</tr>
</table>
</td>
</tr><!-- end: tr -->
<tr>
<td class="bg_light" style="text-align: center;">
<p>you can always reply us from <a href="mailto:support@'.siteDomain.'" style="color: rgba(0,0,0,.8);">HERE</a></p>
</td>
</tr>
</table>

</div>
</center>
</body>
</html>

';
    return $message;
  }

}