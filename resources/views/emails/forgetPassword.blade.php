<!DOCTYPE html>
<html>
   <head>
      <title>Forgot Password Email</title>
   </head>
   <body>
      <table style="border: 1px solid #e8f1ff;font-family: Geneva, sans-serif;" width="600" cellspacing="0" cellpadding="0" border="0" align="center">
         <tbody>
            <tr>
               <td style="background-color: #022b71; padding: 20px; text-align: center;">
                  <a href="#"><img src="{{ asset('public/images/logo.png') }}" alt="Logo" width="130" height="67"></a>
               </td>
            </tr>
            <tr>
               <td style="padding: 20px;">
                  <h2 style="font-size: 24px; margin-bottom: 20px;color: #144eb0;">Forgot Your Password?</h2>
                  <p style="color: #424242; font-size: 15px; line-height: 1.5;">Dear <strong>{{ ucfirst($name) }}</strong>,</p>
                  <p style="color: #424242; font-size: 15px; line-height: 1.5;">We received a request to reset your password. To reset your password, click on the button below:</p>
                  <p style="color: #424242; text-align: center; margin: 30px 0;">
                     <a href="{{$url}}" style="display: inline-block; background-color: #0d6efd; color: #ffffff; padding: 12px 24px; font-size: 15px; text-decoration: none;border-radius: 3px;">Reset Password</a>
                  </p>
                  <p style="color: #424242; font-size: 15px; line-height: 1.5;">If you did not request a password reset, please disregard this email.</p>
               </td>
            </tr>
            <tr>
               <td>
                  <table style="width: 149px;margin: 0 auto;" cellspacing="0" cellpadding="0">
                     <tbody>
                        <tr>
                           <td colspan="4">
                              <p style="text-align: center;color: #5e5e5e;"><strong>Follow Us:</strong></p>
                           </td>
                        </tr>
                        <tr>
                           <td class="es-p10r" valign="top" align="center"><a target="_blank" href="#"><img src="{{ asset('public/images/facebook-rounded-gray.png') }}" alt="Fb" title="Facebook" width="28"></a></td>
                           <td class="es-p10r" valign="top" align="center"><a target="_blank" href="#"><img src="{{ asset('public/images/twitter-rounded-gray.png') }}" alt="Tw" title="Twitter" width="28"></a></td>
                           <td class="es-p10r" valign="top" align="center"><a target="_blank" href="#"><img src="{{ asset('public/images/instagram-rounded-gray.png') }}" alt="Ig" title="Instagram" width="28"></a></td>
                           <td class="es-p10r" valign="top" align="center"><a target="_blank" href="#"><img src="{{ asset('public/images/linkedin-rounded-gray.png') }}" alt="In" title="Linkedin" width="28"></a></td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
            <tr>
               <td height="25"></td>
            </tr>
            <tr>
               <td style="background-color: #657288; padding: 20px; text-align: center;">
                  <p style="font-size: 14px;color: #fff;">© 2023 Alium. All rights reserved.</p>
               </td>
            </tr>
         </tbody>
      </table>
   </body>
</html>
