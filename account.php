<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");

echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1>The 7th International Workshop on IOT and Cloud Computing</h1>
<h2>账户</h2>
<div id='left-nav'>";

include("left-nav.php");

echo "
</div>
<div id='main'>
你好， $loggedInUser->real_name ，欢迎参加物联网大赛。
</div>
<div id='bottom'></div>
</div>
</body>
</html>";

?>
