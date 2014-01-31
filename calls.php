<?php
$user = OpenVBX::getCurrentUser();
$account = OpenVBX::getAccount();
$tenant_id = $user->values['tenant_id'];
$flows = OpenVBX::getFlows(array('tenant_id' => $tenant_id));
if(isset($_POST['sid']) && isset($_POST['flow'])):
  $flow = OpenVBX::getFlows(array('id' => $_POST['flow'], 'tenant_id' => $tenant_id));
  $call = $account->calls->get($_POST['sid']);
  if($flow && $flow[0]->values['data'] && $call)
    $call->update(array(
    	'Url' => site_url('twiml/start/voice/' . $flow[0]->values['id']), 
    	'Method' => 'POST'
    ));
endif;
if(isset($_GET['json'])):
  $calls = $account->calls->getIterator(0, 50, array(     
  	'Status' => 'in-progress'
  ));
  $res = array();
  foreach($calls as $call)
    if(in_array($call->direction, array('inbound', 'outbound-api')) && !empty($call->to) && !empty($call->from))
      $res[$call->sid] = array(
        'to' => $call->to_formatted,
        'from' => $call->from_formatted,
        'time' => date('j-M-y g:i:sa', strtotime($call->start_time)),
        'duration' => $call->duration
      );
  die(json_encode($res));
endif;
OpenVBX::addJS('calls.js');
?>
<style>
  .header {
    font-size: 16px;
    font-weight: bold;
  }
  .header,
  .call {
    overflow: hidden;
    padding: 5px 0;
    border-bottom: 1px solid #eee;
  }
  .header span,
	.call span {
		display: inline-block;
		float: left;
		width: 25%;
		text-align: center;
	}
	.template {
  	display: none;
	}
</style>
<div class="vbx-content-main">
	<div class="vbx-content-menu vbx-content-menu-top">
		<h2 class="vbx-content-heading">Live Calls</h2>
	</div>
	<div class="vbx-table-section">
    <div class="calls">
      <p class="header">
        <span>To</span>
        <span>From</span>
        <span>Time</span>
        <span>Redirect</span>
      </p>
    </div>
	</div>
	<div class="template">
    <select>
      <option>Select a Flow</option>
<?php         foreach($flows as $flow): ?>
      <option value="<?php echo $flow->values['id']; ?>"><?php echo $flow->values['name']; ?></option>
<?php         endforeach; ?>
    </select>
	</div>
</div>