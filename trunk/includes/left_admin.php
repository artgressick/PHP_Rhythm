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
			<div class='leftTitle'>Access</div>
			<div class='leftContent'>
				<div<?=($leftlink == 'siteusers' ? ' class="active"' : '')?>><a href='index.php'>Site Users</a></div>
				<div<?=($leftlink == 'customeraccess' ? ' class="active"' : '')?>><a href='customeraccess.php'>Customer Access</a></div>
			</div>

			<div class='leftTitle'>Attributes</div>
			<div class='leftContent'>
				<div<?=($leftlink == 'divisions' ? ' class="active"' : '')?>><a href='divisions.php'>Division</a></div>
				<div<?=($leftlink == 'currencies' ? ' class="active"' : '')?>><a href='currencies.php'>Currencies</a></div>
				<div<?=($leftlink == 'imtypes' ? ' class="active"' : '')?>><a href='imtypes.php'>IM Types</a></div>
				<div<?=($leftlink == 'phonetypes' ? ' class="active"' : '')?>><a href='phonetypes.php'>Phone Types</a></div>
				<div<?=($leftlink == 'customeraddresstypes' ? ' class="active"' : '')?>><a href='customeraddresstypes.php'>Address Types</a></div>
			</div>

			<div class='leftTitle'>Discussion</div>
			<div class='leftContent'>
				<div<?=($leftlink == 'dis_groups' ? ' class="active"' : '')?>><a href='dis_groups.php'>Groups</a></div>
				<div<?=($leftlink == 'dis_topics' ? ' class="active"' : '')?>><a href='dis_topics.php'>Topics</a></div>
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