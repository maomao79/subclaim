<?php
require_once dirname(__DIR__).'/src/bootstrap.php'; requireAuth(); $cid=companyId();
if($_SERVER['REQUEST_METHOD']==='POST'){
 verifyCsrf(); $pid=(int)$_POST['project_id'];
 if((int)(val("SELECT COUNT(*) FROM project WHERE project_id=? AND company_id=?",[$pid,$cid])??0)){
  insertid("INSERT INTO contract(company_id,project_id,contract_no,contract_value,retention_rate,cis_rate) VALUES(?,?,?,?,?,?)",
   [$cid,$pid,trim($_POST['no']),(float)$_POST['value'],(float)$_POST['retention'],(float)$_POST['cis']]);
  header('Location:/contracts.php');exit;
 }
}
$projects=rows("SELECT project_id,project_name FROM project WHERE company_id=? ORDER BY project_name",[$cid]);
$list=rows("SELECT ct.*,p.project_name FROM contract ct JOIN project p ON p.project_id=ct.project_id AND p.company_id=ct.company_id WHERE ct.company_id=? ORDER BY ct.contract_id DESC",[$cid]);
headerHtml('Contracts'); ?>
<div class="row g-4"><div class="col-lg-4"><div class="card shadow-sm"><div class="card-body"><h1 class="h5">New contract</h1>
<form method="post"><?=csrfField()?>
<div class="mb-3"><label class="form-label">Project</label><select class="form-select" name="project_id" required><option value="">Select...</option><?php foreach($projects as $p):?><option value="<?=$p['project_id']?>"><?=e($p['project_name'])?></option><?php endforeach;?></select></div>
<div class="mb-3"><label class="form-label">Contract no.</label><input class="form-control" name="no" required></div>
<div class="mb-3"><label class="form-label">Value £</label><input class="form-control" type="number" step=".01" name="value" value="0"></div>
<div class="row g-2"><div class="col"><label class="form-label">Retention %</label><input class="form-control" type="number" step=".01" name="retention" value="5"></div><div class="col"><label class="form-label">CIS %</label><input class="form-control" type="number" step=".01" name="cis" value="20"></div></div>
<button class="btn btn-dark mt-3">Save</button></form></div></div></div>
<div class="col-lg-8"><div class="card shadow-sm"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Project</th><th>No.</th><th class="text-end">Value</th><th>Ret.</th><th>CIS</th></tr></thead><tbody>
<?php foreach($list as $r):?><tr><td><?=e($r['project_name'])?></td><td><?=e($r['contract_no'])?></td><td class="text-end"><?=e(money($r['contract_value']))?></td><td><?=e($r['retention_rate'])?>%</td><td><?=e($r['cis_rate'])?>%</td></tr><?php endforeach;?>
</tbody></table></div></div></div></div><?php footerHtml(); ?>
