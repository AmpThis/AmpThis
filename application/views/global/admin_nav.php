    <div class="admin_nav">
    	<h2>Navigation</h2>
        <h3>Users</h3>
        <ul>
            <?php foreach($user_nav as $item) : ?>
            	<li class="<?= $item->class; ?>"><a href="<?= base_url(); ?><?= $item->href; ?>"><?= $item->name; ?></a></li>
            <?php endforeach; ?>
        </ul>
        <?php if($user_level >= 3): ?>
        <h3>Configuration Settings</h3>
        <ul>
            <?php foreach($config_nav as $item): ?>
            	<li class="<?= $item->class; ?>"><a href="<?= base_url(); ?><?= $item->href; ?>"><?= $item->name; ?></a></li>
            <? endforeach; ?>
        </ul>
        <?php endif; ?>
        <div class="CLEARFIX"></div>
    </div>
