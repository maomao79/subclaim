<?php
require_once dirname(__DIR__).'/src/bootstrap.php';
$msg=''; $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verifyCsrf();
 try{
   db()->exec(file_get_contents(dirname(__DIR__).'/sql/schema.sql'));
   if(!(int)(val("SELECT COUNT(*) FROM company")??0)){
     $cid=insertid("INSERT INTO company(company_name) VALUES(?)",[trim($_POST['company_name'])]);
     insertid("INSERT INTO app_user(company_id,email,password_hash,display_name) VALUES(?,?,?,?)",[
       $cid,trim($_POST['email']),password_hash($_POST['password'],PASSWORD_DEFAULT),'Admin'
     ]);
   }
   $msg='Setup complete. You can sign in.';
 }catch(Throwable $x){$err=$x->getMessage();}
}
headerHtml('Setup'); ?>
<div class="row justify-content-center"><div class="col-md-6">
<div class="card shadow-sm"><div class="card-body p-4">
<h1 class="h3">SubClaim setup</h1>
<?php if($msg):?><div class="alert alert-success"><?=e($msg)?></div><a class="btn btn-dark" href="/login.php">Login</a><?php else:?>
<?php if($err):?><div class="alert alert-danger"><?=e($err)?></div><?php endif;?>
<form method="post"><?=csrfField()?>
<div class="mb-3"><label class="form-label">Company</label><input class="form-control" name="company_name" value="Demo Subcontractor Ltd" required></div>
<div class="mb-3"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="admin@example.com" required></div>
<div class="mb-3"><label class="form-label">Password</label><input class="form-control" type="password" name="password" value="ChangeMe123!" required></div>
<button class="btn btn-dark">Create database tables</button></form>
<div class="alert alert-warning small mt-3 mb-0">Delete/block setup.php before public deployment.</div>
<?php endif;?></div></div></div></div>
<?php footerHtml(); ?>
