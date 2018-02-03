 <!--footer-->
<div id="footer">
<div class="footlft"><img src="/static/images/i12.png">欢迎您 <?php echo $user['username']?>&nbsp;&nbsp;&nbsp;&nbsp;
姓名：<?php echo $user['realname']?> &nbsp;&nbsp;&nbsp;&nbsp;
角色：<?php echo $user['role']?> &nbsp;&nbsp;&nbsp;&nbsp;登录次数：<?php echo $user['logincount']?> &nbsp;&nbsp;&nbsp;&nbsp;上一次登录时间：<?php echo $user['lastlogintime']?>&nbsp;&nbsp;&nbsp;&nbsp;上一次登录IP：<?php echo $user['lastloginip']?></div>
</div>
</div>
<?php
//debug_info();
?>
</body>
</html>