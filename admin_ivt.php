<?php
///**
// * Created by PhpStorm.
// * User: Tengo
// * Date: 2015/6/1
// * Time: 11:15
// */

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

//Form posted
if(!empty($_POST))
{
    if (isset($_POST['update']) && isset($_POST['delete']) && $_POST['delete']) {
        $deletions = $_POST['delete'];
        if ($deletion_count = deleteInviteCodes($deletions)){
            $successes[] = "Delete successfully!";
        }
        else {
            $errors[] = "Error!";
        }
    }
    if (isset($_POST['gencode']) && $_POST['gencode']) {
        $i = 0;
        while ($i++ < 5) {
            $ivtcode = genInviteCode();
            $successes[] = $ivtcode;
        }
    }
}

//Fetch all invite codes
$ivtlist = fetchAllInviteCodes();

require_once("models/header.php");

echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1>The 7th International Workshop on IOT and Cloud Computing</h1>
<h2>邀请管理</h2>
<div id='left-nav'>";

include("left-nav.php");

echo "
</div>
<div id='main'>";

echo resultBlock($errors,$successes);

echo "
<form name='ivt_code' action='".$_SERVER['PHP_SELF']."' method='post'>
<table class='admin'>
<tr><th>删除</th><th>邀请码</th><th>用户名</th><th>邮箱</th></tr>";

//Display list of Invite codes
if ($ivtlist){
    foreach ($ivtlist as $ivt){
        echo "
        <tr>
        <td><input type='checkbox' name='delete[".$ivt['id']."]' id='delete[".$ivt['id']."]' value='".$ivt['id']."'></td>
        <td>".$ivt['ivt_code']."</td>
        <td>".$ivt['username']."</td>
        <td>".$ivt['email']."</td>
        ";
    }
}

echo "
</table>

<div id='submit'>
<input type='submit' name='update' value='删除' />
<input type='submit' name='gencode' value='生成邀请码' />
</div>
</form>";

echo "
<div id='bottom'></div>
</div>
</body>
</html>";

?>