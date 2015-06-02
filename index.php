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
<h2>Membership Management System</h2>
<div id='left-nav'>";
include("left-nav.php");

echo "
</div>
<div id='main'>
<p>欢迎参加第七届国际物联网与云计算大会，请相关人员进行会前登记注册，谢谢。</p>
<p>详询：187xxxxxxxx(x先生)</p>
</div>
<div id='bottom'></div>
</div>
</body>
</html>";

?>
