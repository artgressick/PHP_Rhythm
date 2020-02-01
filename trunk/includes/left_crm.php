    <!-- #Middle top part -->
    <tr>
    	<td class='lefttoolbar'>
			<img src='<?=$BF?>images/spacer.jpg' alt='175spacer' />
<!-- begin profile-->
			<div style="padding-left:5px; padding-bottom:15px; padding-right: 5px;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td><a href="<?=$BF?>myprofile.php" title="Edit My Profile"><img src="<?=$BF?>avatars/default.gif" width="50" height="50" /></a></td>
						<td style="font-size:10px; line-height:14px;" width="100%">
							<div style="padding-left:5px;">Welcome,</div>
							<div style="padding-left:5px;"><?=$_SESSION['chrFirst'] ." ". $_SESSION['chrLast']?></div>
							<div style="padding-left:5px;">Site User</div>
						</td>
					</tr>
				</table>
			</div>
<!-- end of profile -->
			<div class='leftTitle'>Customers</div>
			<div class='leftContent'>
				<div<?=($leftlink == 'viewcustomer' ? ' class="active"' : '')?>><a href='index.php'>View Customers</a></div>
			</div>

			<div class='leftTitle'>Contacts</div>
			<div class='leftContent'>
				<div<?=($leftlink == 'viewcontact' ? ' class="active"' : '')?>><a href='contacts.php'>View Contacts</a></div>
			</div>

      	</td>
      	<td class='maincontent'>
			<table class="banner" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="icon"><?=(isset($banner_icon) && $banner_icon!="" ? '<img src="'.$BF.'images/'.$banner_icon.'" alt="Icon" />' : "")?></td>
					<td class="main">
						<div class="title"><?=$banner_title?><?=(isset($banner_xtra) ? '&nbsp;<span class="xtra">'.$banner_xtra.'</span>' : '')?></div>
						<?=(isset($banner_instructions) ? '<div class="inst">'.$banner_instructions.'</div>' : '')?>
					</td>
				</tr>
			</table>
			<div class="content">
		