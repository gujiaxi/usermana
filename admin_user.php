<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
$userId = $_GET['id'];

//Check if selected user exists
if(!userIdExists($userId)){
	header("Location: admin_users.php"); die();
}

$userdetails = fetchUserDetails(NULL, NULL, $userId); //Fetch user details

//Forms posted
if(!empty($_POST))
{
	//Delete selected account
	if(!empty($_POST['delete'])){
		$deletions = $_POST['delete'];
		if ($deletion_count = deleteUsers($deletions)) {
			$successes[] = lang("ACCOUNT_DELETIONS_SUCCESSFUL", array($deletion_count));
		}
		else {
			$errors[] = lang("SQL_ERROR");
		}
	}
	else
	{
		//Update real name
		if ($userdetails['real_name'] != $_POST['realname']){
			$real_name = trim($_POST['realname']);

			//Validate real name
			if(minMaxRange(5,25,$real_name))
			{
				$errors[] = lang("ACCOUNT_REAL_CHAR_LIMIT",array(5,25));
			}
			else {
				if (updateRealName($userId, $real_name)){
					$successes[] = lang("ACCOUNT_REALNAME_UPDATED", array($real_name));
				}
				else {
					$errors[] = lang("SQL_ERROR");
				}
			}

		}
		else {
			$real_name = $userdetails['real_name'];
		}

		//Activate account
		if(isset($_POST['activate']) && $_POST['activate'] == "activate"){
			if (setUserActive($userdetails['activation_token'])){
				$successes[] = lang("ACCOUNT_MANUALLY_ACTIVATED", array($real_name));
			}
			else {
				$errors[] = lang("SQL_ERROR");
			}
		}

		//Update email
		if ($userdetails['email'] != $_POST['email']){
			$email = trim($_POST["email"]);

			//Validate email
			if(!isValidEmail($email))
			{
				$errors[] = lang("ACCOUNT_INVALID_EMAIL");
			}
			elseif(emailExists($email))
			{
				$errors[] = lang("ACCOUNT_EMAIL_IN_USE",array($email));
			}
			else {
				if (updateEmail($userId, $email)){
					$successes[] = lang("ACCOUNT_EMAIL_UPDATED");
				}
				else {
					$errors[] = lang("SQL_ERROR");
				}
			}
		}

		//Update title
		if ($userdetails['title'] != $_POST['title']){
			$title = trim($_POST['title']);

			//Validate title
			if(minMaxRange(1,50,$title))
			{
				$errors[] = lang("ACCOUNT_TITLE_CHAR_LIMIT",array(1,50));
			}
			else {
				if (updateTitle($userId, $title)){
					$successes[] = lang("ACCOUNT_TITLE_UPDATED", array ($real_name, $title));
				}
				else {
					$errors[] = lang("SQL_ERROR");
				}
			}
		}

		//Remove permission level
		if(!empty($_POST['removePermission'])){
			$remove = $_POST['removePermission'];
			if ($deletion_count = removePermission($remove, $userId)){
				$successes[] = lang("ACCOUNT_PERMISSION_REMOVED", array ($deletion_count));
			}
			else {
				$errors[] = lang("SQL_ERROR");
			}
		}

		if(!empty($_POST['addPermission'])){
			$add = $_POST['addPermission'];
			if ($addition_count = addPermission($add, $userId)){
				$successes[] = lang("ACCOUNT_PERMISSION_ADDED", array ($addition_count));
			}
			else {
				$errors[] = lang("SQL_ERROR");
			}
		}

		$userdetails = fetchUserDetails(NULL, NULL, $userId);
	}
}

$userPermission = fetchUserPermissions($userId);
$permissionData = fetchAllPermissions();

require_once("models/header.php");

echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1>The 7th International Workshop on IOT and Cloud Computing</h1>
<h2>用户管理</h2>
<div id='left-nav'>";

include("left-nav.php");

echo "
</div>
<div id='main'>";

echo resultBlock($errors,$successes);

echo "
<form name='adminUser' action='".$_SERVER['PHP_SELF']."?id=".$userId."' method='post'>
<table class='admin'><tr><td>
<h3>User Information</h3>
<div id='regbox'>
<p>
<label>ID:</label>
".$userdetails['id']."
</p>
<p>
<label>Username:</label>
".$userdetails['user_name']."
</p>
<p>
<label>Real Name:</label>
<input type='text' name='realname' value='".$userdetails['real_name']."' />
</p>
<p>
<label>Email:</label>
<input type='text' name='email' value='".$userdetails['email']."' />
</p>
<p>
<label>Active:</label>";

//Display activation link, if account inactive
if ($userdetails['active'] == '1'){
	echo "Yes";
}
else{
	echo "No
	</p>
	<p>
	<label>Activate:</label>
	<input type='checkbox' name='activate' id='activate' value='activate'>
	";
}

echo "
</p>
<p>
<label>Title:</label>
<input type='text' name='title' value='".$userdetails['title']."' />
</p>
<p>
<label>Sign Up:</label>
".date("j M, Y", $userdetails['sign_up_stamp'])."
</p>
<p>
<label>Last Sign In:</label>";

//Last sign in, interpretation
if ($userdetails['last_sign_in_stamp'] == '0'){
	echo "Never";
}
else {
	echo date("j M, Y", $userdetails['last_sign_in_stamp']);
}

echo "
</p>
<p>
<label>Delete:</label>
<input type='checkbox' name='delete[".$userdetails['id']."]' id='delete[".$userdetails['id']."]' value='".$userdetails['id']."'>
</p>
<p>
<label>&nbsp;</label>
<input type='submit' value='Update' class='submit' />
</p>
</div>
</td>
<td>
<h3>Permission Membership</h3>
<div id='regbox'>
<p>Remove Permission:";

//List of permission levels user is apart of
foreach ($permissionData as $v1) {
	if(isset($userPermission[$v1['id']])){
		echo "<br><input type='checkbox' name='removePermission[".$v1['id']."]' id='removePermission[".$v1['id']."]' value='".$v1['id']."'> ".$v1['name'];
	}
}

//List of permission levels user is not apart of
echo "</p><p>Add Permission:";
foreach ($permissionData as $v1) {
	if(!isset($userPermission[$v1['id']])){
		echo "<br><input type='checkbox' name='addPermission[".$v1['id']."]' id='addPermission[".$v1['id']."]' value='".$v1['id']."'> ".$v1['name'];
	}
}

echo"
</p>
</div>
</td>
</tr>
</table>
</form>
</div>
<div id='bottom'></div>
</div>
</body>
</html>";

?>
