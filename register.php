<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

//Prevent the user visiting the logged in page if he/she is already logged in
if(isUserLoggedIn()) { header("Location: account.php"); die(); }

//Forms posted
if(!empty($_POST))
{
	$errors = array();
	$username = trim($_POST["username"]);
	$password = trim($_POST["password"]);
	$confirm_pass = trim($_POST["passwordc"]);
	$captcha = md5($_POST["captcha"]);

    $ivt_code = trim($_POST["ivt_code"]);
    $real_name = trim($_POST["real_name"]);
    $email = trim($_POST["email"]);

    if (!inviteCodeExists($ivt_code))
    {
        $errors[] = "Wrong invitation code!";
    }
    else {
        updateInviteCode($ivt_code, $username, $email);
    }

	if ($captcha != $_SESSION['captcha'])
	{
		$errors[] = lang("CAPTCHA_FAIL");
	}
	if(minMaxRange(5,25,$username))
	{
		$errors[] = lang("ACCOUNT_USER_CHAR_LIMIT",array(5,25));
	}
	if(!ctype_alnum($username)){
		$errors[] = lang("ACCOUNT_USER_INVALID_CHARACTERS");
	}
	if(minMaxRange(8,50,$password) && minMaxRange(8,50,$confirm_pass))
	{
		$errors[] = lang("ACCOUNT_PASS_CHAR_LIMIT",array(8,50));
	}
	else if($password != $confirm_pass)
	{
		$errors[] = lang("ACCOUNT_PASS_MISMATCH");
	}
	if(!isValidEmail($email))
	{
		$errors[] = lang("ACCOUNT_INVALID_EMAIL");
	}
	//End data validation
	if(count($errors) == 0)
	{
		//Construct a user object
		// $user = new User($username,$password,$real_name,$email);
        $user = new User($username, $password, $real_name, $email);

		//Checking this flag tells us whether there were any errors such as possible data duplication occured
		if(!$user->status)
		{
			if($user->username_taken) $errors[] = lang("ACCOUNT_USERNAME_IN_USE",array($username));
			if($user->email_taken) 	  $errors[] = lang("ACCOUNT_EMAIL_IN_USE",array($email));
		}
		else
		{
			//Attempt to add the user to the database, carry out finishing  tasks like emailing the user (if required)
			if(!$user->userCakeAddUser())
			{
				if($user->mail_failure) $errors[] = lang("MAIL_ERROR");
				if($user->sql_failure)  $errors[] = lang("SQL_ERROR");
			}
		}
	}
	if(count($errors) == 0) {
		$successes[] = $user->success;
	}
}

require_once("models/header.php");
echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1>The 7th International Workshop on IOT and Cloud Computing</h1>
<h2>Register</h2>

<div id='left-nav'>";
include("left-nav.php");
echo "
</div>

<div id='main'>";

echo resultBlock($errors,$successes);

echo "
<div id='regbox'>
<form name='newUser' action='".$_SERVER['PHP_SELF']."' method='post'>

<div id='logbox'>
<h3>登录信息</h3>
<p>
<label>用户名:</label>
<input type='text' name='username' />
</p>
<p>
<label>邀请码:</label>
<input type='text' name='ivt_code' />
</p>
<p>
<label>密码:</label>
<input type='password' name='password' />
</p>
<p>
<label>确认密码:</label>
<input type='password' name='passwordc' />
</p>
<p>
<label>验证码:</label>
<img src='models/captcha.php'>
</p>
<label>输入验证码:</label>
<input name='captcha' type='text'>
</p>
</div>

<div id='infobox'>
<h3>注册信息</h3>
<p>
<label>姓名:</label>
<input type='text' name='real_name' />
</p>
<p>
<label>性别:</label>
<input type='radio' name='gender' value='male' checked='checked' />男
<input type='radio' name='gender' value='female' />女
</p>
<p>
<label>注册类型:</label>
<input type='checkbox' name='reg_type[0]' value='teacher' />教师 - 职称<input type='text' name=reg_type_extra />
<input type='checkbox' name='reg_type[1]' value='student' />学生
</p>
<p>
<label>支付状态:</label>
<input type='radio' name='pay_state' value='paid' />已支付
<input type='radio' name='pay_state' value='unpaid' checked='checked' />未支付
</p>
<p>
<label>单位:</label>
<input type='text' name='company' />
</p>
<p>
<label>学历:</label>
<input type='checkbox' name='edu_degree' value='doctor' />博士
<input type='checkbox' name='edu_degree' value='master' />硕士
<input type='checkbox' name='edu_degree' value='others' />其他
</p>
<p>
<label>手机:</label>
<input type='text' name='phone_number' />
</p>
<p>
<label>邮箱:</label>
<input type='text' name='email' />
</p>
<p>
<h4>【来程】</h4>
<label>车次/航班</label>
<input type='text' name='trip_in_flight' /><br/>
<label>到达站点</label>
<input type='text' name='trip_in_site' /><br/>
<label>到达时间</label>
<input type='text' name='trip_in_date' />日<input type='text' name='trip_in_time' />时
</p>
<p>
<h4>【返程】</h4>
<label>车次/航班</label>
<input type='text' name='trip_out_flight' /><br/>
<label>出发站点</label>
<input type='text' name='trip_out_site' /><br/>
<label>出发时间</label>
<input type='text' name='trip_out_date' />日<input type='text' name='trip_out_time' />时
</p>
<p>
<label>注册费发票抬头:</label>
<input type='radio' name='invoice_title' value='same' checked='checked' />与所在单位名称一致
<input type='radio' name='invoice_title' value='others' />其他<input type='text' name='invoice_title_extra' />
</p>
<p>
<label>注册费发票内容:</label>
<input type='checkbox' name='invoice_content' value='i1' />注册费
<input type='checkbox' name='invoice_content' value='i2' />会员费
<input type='checkbox' name='invoice_content' value='i3' />会务费
<input type='checkbox' name='invoice_content' value='i4' />培训费
</p>
<p>
<label>入住日期</label>
<input type='text' name='accom_in_date' />
</p>
<p>
<label>离开日期</label>
<input type='text' name='accom_out_date' />
</p>
<p>
<label>酒店类型</label>
<input type='checkbox' name='accom_type[0]' value='a' />A
<input type='checkbox' name='accom_type[1]' value='b' />B
<input type='checkbox' name='accom_type[2]' value='s' />单住
<input type='checkbox' name='accom_type[3]' value='c' />合住
</p>

<p>
<label>&nbsp;<br>
<input type='submit' value='注册'/>
</p>
</div>
</form>
</div>

</div>
<div id='bottom'></div>
</div>
</body>
</html>";
?>
