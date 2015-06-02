<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/

if (!securePage($_SERVER['PHP_SELF'])){die();}

//Links for logged in user
if(isUserLoggedIn()) {
	echo "
	<ul>
	<li><a href='account.php'>账号首页</a></li>
	<li><a href='user_settings.php'>个人信息</a></li>
	<li><a href='logout.php'>退出</a></li>
	</ul>";
	
	//Links for permission level 2 (default admin)
	if ($loggedInUser->checkPermission(array(2))){
	echo "
	<ul>
	<li><a href='admin_configuration.php'>网站设置</a></li>
	<li><a href='admin_users.php'>用户管理</a></li>
	<li><a href='admin_ivt.php'>邀请管理</a></li>
	<li><a href='admin_permissions.php'>权限管理</a></li>
	<li><a href='admin_pages.php'>页面管理</a></li>
	</ul>";
	}
} 
//Links for users not logged in
else {
	echo "
	<ul>
	<li><a href='index.php'>主页</a></li>
	<li><a href='login.php'>登陆</a></li>
	<li><a href='register.php'>注册</a></li>
	<li><a href='forgot-password.php'>忘记密码</a></li>";
	if ($emailActivation)
	{
	echo "<li><a href='resend-activation.php'>重新发送激活邮件</a></li>";
	}
	echo "</ul>";
}

?>
