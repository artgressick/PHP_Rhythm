	</head>
<body onLoad="<?=(isset($bodyParams) ? $bodyParams : '')?>">

<table cellpadding="0" cellspacing="0" class='frame'>
    <!-- #Begin top part -->
	<tr class='topbar'>
	    	<td class='logo' align="center"><a href="<?=$BF?>index.php"><img src="<?=$BF?>images/ebp-logo.gif" alt='top logo' width="150" height="45" border="0"></a></td>
		  	<td style='height: 60px;'>
				<table cellpadding="0" cellspacing="0" class='innertop'>
					<tr>
						<td>
							<div style='margin-bottom: 10px;'>
<?	if(isset($_SESSION['intAttempts'])) {
		if($_SESSION['intAttempts'] != 0) { ?>
								<span class='ErrorMessage'>There has been <?=$_SESSION['intAttempts']?> failed login attempts on this account since your last login!</span><?		}
		unset($_SESSION['intAttempts']);
	} ?>
							</div>
						</td>
						<td style='text-align: right; width: 300px;'>
							<div style='margin-bottom: 10px; '><a href="?logout=1"><img src='<?=$BF?>images/logoff.gif' alt='logout' /></a></div>
							<div style='white-space: nowrap;'>Questions? 1-800-492-2448</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class='datetime'><?=date('l, F d, Y');?></td>
			<td class='buttons'>
				<table cellspacing="0" cellpadding="0" class="buttonmenu">
					<tr>
						<td<?=($section == '' ? " class='active'" : '')?>><a href='<?=$BF?>'>Dashboard</a></td>
						<td<?=($section == 'crm' ? " class='active'" : '')?>><a href='<?=$BF?>crm/'>CRM</a></td>
						<td<?=($section == 'quotes' ? " class='active'" : '')?>><a href='<?=$BF?>quotes/'>Quotes</a></td>
						<td<?=($section == 'projects' ? " class='active'" : '')?>><a href='<?=$BF?>projects/'>Projects</a></td>
						<td<?=($section == 'calendar' ? " class='active'" : '')?>><a href='<?=$BF?>calendar/'>Calendar</a></td>
						<td<?=($section == 'discussions' ? " class='active'" : '')?>><a href='<?=$BF?>discussions/'>Discussion</a></td>
						<td<?=($section == 'admin' ? " class='active'" : '')?>><a href='<?=$BF?>admin/'>Admin</a></td>
						<td class='right'>&nbsp;</td>
					</tr>
				</table>
			</td>
	</tr>
	<tr>
		<td colspan='2' class='graysplitter'></td>
	</tr>
