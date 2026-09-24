<?php
require_once dirname(__DIR__).'/src/bootstrap.php'; requireAuth(); $cid=companyId();
$projects=(int)(val("SELECT COUNT(*) FROM project WHERE company_id=? AND status='ACTIVE'",[$cid])??0);
$claimed=(float)(val("SELECT COALESCE(SUM(claimed_amount),0) FROM claim WHERE company_id=?",[$cid])??0);
$cert=(float)(val("SELECT COALESCE(SUM(certified_amount),0) FROM claim WHERE company_id=?",[$cid])??0);
$paid=(float)(val("SELECT COALESCE(SUM(paid_amount),0) FROM claim WHERE company_id=?",[$cid])??0);
headerHtml('Dashboard'); ?>
<h1 class="h3 mb-4">Dashboard</h1>
<div class="row g-3">
<?php foreach([['Active projects',$projects],['Claimed',money($claimed)],['Certified',money($cert)],['Outstanding',money(max(0,$cert-$paid))]] as $x):?>
<div class="col-6 col-lg-3"><div class="card shadow-sm h-100"><div class="card-body">
<div class="small text-secondary"><?=e($x[0])?></div><div class="fs-4 fw-bold"><?=e($x[1])?></div>
</div></div></div><?php endforeach;?>
</div>
<div class="card shadow-sm mt-4"><div class="card-body">
<h2 class="h5">v0.1 flow</h2>
<p class="mb-0">Customer → Project → Contract → Claim. BOQ, certification detail, variations and proper CIS logic come next.</p>
</div></div><?php footerHtml(); ?>
