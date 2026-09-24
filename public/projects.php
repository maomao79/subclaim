<?php
require_once dirname(__DIR__).'/src/bootstrap.php'; requireAuth(); $cid=companyId(); $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verifyCsrf(); $id=(int)($_POST['id']??0); $cust=(int)$_POST['customer_id']; $name=trim($_POST['name']??'');
 $ok=(int)(val("SELECT COUNT(*) FROM customer WHERE customer_id=? AND company_id=?",[$cust,$cid])??0);
 if(!$name||!$ok)$err='Valid customer and project name required';
 else{
  if($id) execsql("UPDATE project SET customer_id=?,project_name=?,reference_no=?,status=? WHERE project_id=? AND company_id=?",[$cust,$name,trim($_POST['ref']),$_POST['status'],$id,$cid]);
  else insertid("INSERT INTO project(company_id,customer_id,project_name,reference_no,status) VALUES(?,?,?,?,?)",[$cid,$cust,$name,trim($_POST['ref']),$_POST['status']]);
  header('Location:/projects.php');exit;
 }
}
$edit=!empty($_GET['edit'])?row("SELECT * FROM project WHERE project_id=? AND company_id=?",[(int)$_GET['edit'],$cid]):null;
$customers=rows("SELECT customer_id,customer_name FROM customer WHERE company_id=? ORDER BY customer_name",[$cid]);
$list=rows("SELECT p.*,c.customer_name FROM project p JOIN customer c ON c.customer_id=p.customer_id AND c.company_id=p.company_id WHERE p.company_id=? ORDER BY p.project_id DESC",[$cid]);
headerHtml('Projects'); ?>
<div class="row g-4"><div class="col-lg-4"><div class="card shadow-sm"><div class="card-body">
<h1 class="h5"><?=$edit?'Edit':'New'?> project</h1><?php if($err):?><div class="alert alert-danger"><?=e($err)?></div><?php endif;?>
<form method="post"><?=csrfField()?><input type="hidden" name="id" value="<?=e($edit['project_id']??0)?>">
<div class="mb-3"><label class="form-label">Customer</label><select class="form-select" name="customer_id" required><option value="">Select...</option>
<?php foreach($customers as $c):?><option value="<?=$c['customer_id']?>" <?=((int)($edit['customer_id']??0)==(int)$c['customer_id'])?'selected':''?>><?=e($c['customer_name'])?></option><?php endforeach;?>
</select></div>
<div class="mb-3"><label class="form-label">Project</label><input class="form-control" name="name" value="<?=e($edit['project_name']??'')?>" required></div>
<div class="mb-3"><label class="form-label">Reference</label><input class="form-control" name="ref" value="<?=e($edit['reference_no']??'')?>"></div>
<div class="mb-3"><label class="form-label">Status</label><select class="form-select" name="status"><?php foreach(['ACTIVE','ON_HOLD','COMPLETE'] as $s):?><option <?=($edit['status']??'ACTIVE')===$s?'selected':''?>><?=$s?></option><?php endforeach;?></select></div>
<button class="btn btn-dark">Save</button></form></div></div></div>
<div class="col-lg-8"><div class="card shadow-sm"><div class="table-responsive"><table class="table mb-0">
<thead><tr><th>Project</th><th>Customer</th><th>Ref</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach($list as $r):?><tr><td><?=e($r['project_name'])?></td><td><?=e($r['customer_name'])?></td><td><?=e($r['reference_no'])?></td><td><?=e($r['status'])?></td><td><a class="btn btn-sm btn-outline-secondary" href="?edit=<?=$r['project_id']?>">Edit</a></td></tr><?php endforeach;?>
</tbody></table></div></div></div></div><?php footerHtml(); ?>
