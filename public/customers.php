<?php
require_once dirname(__DIR__).'/src/bootstrap.php'; requireAuth(); $cid=companyId(); $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verifyCsrf(); $id=(int)($_POST['id']??0); $name=trim($_POST['name']??'');
 if(!$name)$err='Name required';
 else{
  if($id) execsql("UPDATE customer SET customer_name=?,email=?,phone=? WHERE customer_id=? AND company_id=?",[$name,trim($_POST['email']),trim($_POST['phone']),$id,$cid]);
  else insertid("INSERT INTO customer(company_id,customer_name,email,phone) VALUES(?,?,?,?)",[$cid,$name,trim($_POST['email']),trim($_POST['phone'])]);
  header('Location:/customers.php');exit;
 }
}
$edit=!empty($_GET['edit'])?row("SELECT * FROM customer WHERE customer_id=? AND company_id=?",[(int)$_GET['edit'],$cid]):null;
$list=rows("SELECT * FROM customer WHERE company_id=? ORDER BY customer_name",[$cid]);
headerHtml('Customers'); ?>
<div class="row g-4"><div class="col-lg-4"><div class="card shadow-sm"><div class="card-body">
<h1 class="h5"><?=$edit?'Edit':'New'?> customer</h1><?php if($err):?><div class="alert alert-danger"><?=e($err)?></div><?php endif;?>
<form method="post"><?=csrfField()?><input type="hidden" name="id" value="<?=e($edit['customer_id']??0)?>">
<div class="mb-3"><label class="form-label">Name / Main contractor</label><input class="form-control" name="name" value="<?=e($edit['customer_name']??'')?>" required></div>
<div class="mb-3"><label class="form-label">Email</label><input class="form-control" name="email" value="<?=e($edit['email']??'')?>"></div>
<div class="mb-3"><label class="form-label">Phone</label><input class="form-control" name="phone" value="<?=e($edit['phone']??'')?>"></div>
<button class="btn btn-dark">Save</button></form></div></div></div>
<div class="col-lg-8"><div class="card shadow-sm"><div class="table-responsive"><table class="table mb-0">
<thead><tr><th>Name</th><th>Email</th><th>Phone</th><th></th></tr></thead><tbody>
<?php foreach($list as $r):?><tr><td><?=e($r['customer_name'])?></td><td><?=e($r['email'])?></td><td><?=e($r['phone'])?></td>
<td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="?edit=<?=$r['customer_id']?>">Edit</a></td></tr><?php endforeach;?>
</tbody></table></div></div></div></div><?php footerHtml(); ?>
