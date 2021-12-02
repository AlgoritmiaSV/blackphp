<?php
include "zklibrary.php";
$zk = new ZKLibrary('172.16.0.201', 4370);
$zk->connect();
$zk->disableDevice();
/*
$users = $zk->getUser();
?>
<table width="100%" border="1" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
<thead>
  <tr>
    <td width="25">No</td>
    <td>UID</td>
    <td>ID</td>
    <td>Name</td>
    <td>Role</td>
    <td>Password</td>
  </tr>
</thead>

<tbody>
<?php
$no = 0;
foreach($users as $key=>$user)
{
  $no++;
?>

  <tr>
    <td align="right"><?php echo $no;?></td>
    <td><?php echo $key;?></td>
    <td><?php echo $user[0];?></td>
    <td><?php echo $user[1];?></td>
    <td><?php echo $user[2];?></td>
    <td><?php echo $user[3];?></td>
  </tr>

<?php
}
?>

</tbody>
</table>
<?php
*/
$att_logs = $zk->getAttendance();
//$zk->clearAttendance();
$zk->enableDevice();
$zk->disconnect();
foreach($att_logs as $log)
{
	echo $log[0] . " " . $log[3] . "<br>";
}
echo 'OK';
?>
