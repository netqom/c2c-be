<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>GAME PLATFORM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body style="margin:0; padding:10px 0 0 0;" bgcolor="#F8F8F8">
	<table align="center" cellpadding="0" cellspacing="0" width="95%%">
		<tr>
			<td align="center">
				<table align="center"  cellpadding="0" cellspacing="0" width="600"
					   style="border-collapse: separate;  box-shadow: 1px 0 1px 1px #B8B8B8;"
					   bgcolor="#FFFFFF">
					<tr>
						<td align="center" style="padding: 20px 5px 0">
							<a href="{{ url('/') }}" target="_blank">
								<img src="{{ asset('public/images/logo.png') }}" alt="Logo" style="width:186px;border:0;"/>
							</a> 
						</td>
					</tr>
					<tr>
						<td bgcolor="#ffffff" style="padding: 20px 20px;">
							<table cellpadding="0" cellspacing="0" width="100%%">
								<tr>
									<td style="padding: 0px 0; font-family: Avenir, sans-serif; font-size: 14px;">
									   @yield('content')
									</td>
								</tr>
								<tr>
									<td valign="top" style="padding: 5px 0; font-family: Avenir, sans-serif; font-size: 12px;">
										<p style="margin: 0;">Customer Support</p>
										<p style="color: #0673ba; font-weight: 600; margin: 0; font-size: 14px; font-family: Avenir, sans-serif;">GAME PLATFORM</p>
										<p style="margin: 0;">Phone</p>
										<p style="margin: 0;">gamesupport@gmail.com</p>
										<p style="margin: 0;">www.game-platform.com</p>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#0872ba" style="padding: 10px 15px; font-family: Avenir, sans-serif;  text-align: center;">
							<table cellpadding="0" cellspacing="0" width="100%%" align="center">
								<tr>
									<td align="center" class="footer-text" style="color: #ffffff;">
										<a href="{{ url('/') }}" style="color: #ffffff; text-decoration: none;">GAME PLATFORM</a>
									</td>
								</tr>
								<tr >
									<td align="center" style="padding: 4px 0px 0; color: #ffffff;" class="footer-text">
										<small style="color: #ffffff;">gamesupport@gmail.com</small>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

</body>
</html>