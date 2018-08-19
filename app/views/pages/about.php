<?php require APPROOT . '/views/inc/header.php'; ?>

<h1>
    <?php echo $data['title'];?>
</h1>
<p><?=$data['description'];?></p>
<p>Version: <?=APPVERSION;?></p>

<?php require APPROOT . '/views/inc/footer.php'; ?>