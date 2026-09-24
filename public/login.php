<?php
require_once dirname(__DIR__).'/src/bootstrap.php';
if(user()){header('Location:/');exit;}
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verifyCsrf();
 $u=row("SELECT u.*,c.company_name FROM app_user u JOIN company c ON c.company_id=u.company_id WHERE u.email=? LIMIT 1",[trim($_POST['email'])]);
 if($u && $u['is_active'] && password_verify($_POST['password'],$u['password_hash'])){
   unset($u['password_hash']); $_SESSION['user']=$u; session_regenerate_id(true); header('Location:/');exit;
 }
 $err='Invalid email or password.';
}
headerHtml('Login'); ?>
<div class="row justify-content-center py-5"><div class="col-md-5 col-lg-4">
<div class="card shadow-sm"><div class="card-body p-4">
<h1 class="h3">SubClaim</h1><p class="text-secondary">Claims, certifications & retention.</p>
<?php if($err):?><div class="alert alert-danger"><?=e($err)?></div><?php endif;?>
<form method="post"><?=csrfField()?>
<div class="mb-3"><label class="form-label">Email</label><input class="form-control" type="email" name="email" required></div>
<div class="mb-3"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required></div>
<button class="btn btn-dark w-100">Sign in</button></form>
</div></div></div></div><?php footerHtml(); ?>
