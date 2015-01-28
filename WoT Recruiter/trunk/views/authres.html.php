<?php include 'head.php'; ?>
<a href="?mod=auth">refresh</a>
<h2>auth results</h2>
<?php echo $this->validationResult ? 'login success' : 'login failed';?>
<h3>db last res</h3>
<pre>
<?php 
$db = Engine::getInstance()->db;
print_r( $db->errorInfo() );
?>
</pre>
<h3>attributes</h3>
<pre><?php print_r( $this->openid->getAttributes() );?></pre>
<br />
<h3>$this->openid</h3>
<pre><?php print_r( $this->openid );?></pre>
<br />
<h3>$engine</h3>
<pre><?php print_r( $this->engine );?></pre>
<?php include 'foot.php'; ?>