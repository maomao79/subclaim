<?php
require_once dirname(__DIR__).'/src/bootstrap.php'; requireAuth(); $cid=companyId(); $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verifyCsrf(); $ctid=(int)$_POST['contract_id'];
 $ct=row("SELECT * FROM contract WHERE contract_id=? AND company_id=?",[$ctid,$cid]);
 if(!$ct)$err='Valid contract required';
 else{
  $claimed=max(0,(float)$_POST['claimed']); $cert=max(0,(float)$_POST['certified']); $paid=max(0,(float)$_POST['paid']);
  $ret=round($cert*((float)$ct['retention_rate']/100),2);
  $cis=round(max(0,$cert-$ret)*((float)$ct['cis_rate']/100),2);
  insertid("INSERT INTO claim(company_id,contract_id,project_id,claim_no,period_to,claimed_amount,certified_amount,retention_amount,cis_amount,paid_amount,status) VALUES(?,?,?,?,?,?,?,?,?,?,?)",
   [$cid,$ctid,$ct['project_id'],trim($_POST['no']),$_POST['period'],$claimed,$cert,$ret,$cis,$paid,$_POST['status']]);
  header('Location:/claims.php');exit;
 }
}
$contracts=rows("SELECT ct.*,p.project_name FROM contract ct JOIN project p ON p.project_id=ct.project_id AND p.company_id=ct.company_id WHERE ct.company_id=? ORDER BY p.project_name",[$cid]);
$list=rows("SELECT c.*,p.project_name,ct.contract_no FROM claim c JOIN project p ON p.project_id=c.project_id AND p.company_id=c.company_id JOIN contract ct ON ct.contract_id=c.contract_id AND ct.company_id=c.company_id WHERE c.company_id=? ORDER BY c.claim_id DESC",[$cid]);
headerHtml('Claims'); ?>
<div class="row g-4"><div class="col-lg-4"><div class="card shadow-sm"><div class="card-body"><h1 class="h5">New claim</h1>
<?php if($err):?><div class="alert alert-danger"><?=e($err)?></div><?php endif;?><form method="post"><?=csrfField()?>
<div class="mb-3"><label class="form-label">Contract</label><select class="form-select" name="contract_id" id="contract" required><option value="">Select...</option><?php foreach($contracts as $c):?><option value="<?=$c['contract_id']?>" data-ret="<?=$c['retention_rate']?>" data-cis="<?=$c['cis_rate']?>"><?=e($c['project_name'].' · '.$c['contract_no'])?></option><?php endforeach;?></select></div>
<div class="row g-2"><div class="col"><label class="form-label">Claim no.</label><input class="form-control" name="no" required></div><div class="col"><label class="form-label">Period to</label><input class="form-control" type="date" name="period" value="<?=date('Y-m-d')?>" required></div></div>
<div class="mt-3"><label class="form-label">Claimed £</label><input class="form-control" type="number" step=".01" name="claimed" value="0"></div>
<div class="mt-3"><label class="form-label">Certified £</label><input class="form-control" id="certified" type="number" step=".01" name="certified" value="0"></div>
<div class="mt-3"><label class="form-label">Paid £</label><input class="form-control" type="number" step=".01" name="paid" value="0"></div>
<div class="mt-3"><label class="form-label">Status</label><select class="form-select" name="status"><?php foreach(['DRAFT','SUBMITTED','CERTIFIED','PAID'] as $s):?><option><?=$s?></option><?php endforeach;?></select></div>
<div class="alert alert-light border mt-3 mb-0"><div>Retention: <b id="ret">GBP 0.00</b></div><div>CIS: <b id="cis">GBP 0.00</b></div><div>Net: <b id="net">GBP 0.00</b></div></div>
<button class="btn btn-dark mt-3">Save claim</button></form></div></div></div>
<div class="col-lg-8"><div class="card shadow-sm"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Claim</th><th>Project</th><th class="text-end">Claimed</th><th class="text-end">Certified</th><th class="text-end">Paid</th><th>Status</th></tr></thead><tbody>
<?php foreach($list as $r):?><tr><td><?=e($r['claim_no'])?></td><td><?=e($r['project_name'])?></td><td class="text-end"><?=e(money($r['claimed_amount']))?></td><td class="text-end"><?=e(money($r['certified_amount']))?></td><td class="text-end"><?=e(money($r['paid_amount']))?></td><td><?=e($r['status'])?></td></tr><?php endforeach;?>
</tbody></table></div></div></div></div>
<div class="alert alert-warning small mt-3">v0.1 CIS is deliberately simplified. Do not use it for live UK calculations yet; next version will split labour/materials and certification details.</div>
<script>
function calc(){
 const o=$('#contract option:selected'), c=parseFloat($('#certified').val())||0;
 const rr=parseFloat(o.data('ret'))||0, cr=parseFloat(o.data('cis'))||0;
 const r=Math.round(c*rr)/100, cis=Math.round(Math.max(0,c-r)*cr)/100, n=c-r-cis;
 $('#ret').text('GBP '+r.toFixed(2)); $('#cis').text('GBP '+cis.toFixed(2)); $('#net').text('GBP '+n.toFixed(2));
}
$(document).on('input change','#contract,#certified',calc);
</script><?php footerHtml(); ?>
