<!--left-->
<div class="sideleft" id="_doc_left">
	<div class="menu">
		<div class="onemenu">
        <?php foreach ($menulist as $key =>$value){
        	if ($key == 20) continue;
        	?>
			<div class="menutit">
				<span class="iopen menu<?=$value['code']?>" style="background:url(/static/images/ebh/menu/menu_<?=$value['code']?>.png) no-repeat left center;"></span>
                <a class="_menu_ck" href="javascript:;"><?php echo $value['title']?><b class="arrmal">></b></a>
			</div>
			<div class="twomenu" style="display: none">
				<ul>
                	<?php foreach ($value['child'] as $menuid => $menu){?>
					<li>
                        <a href="javascript:;"  class="_menu_item_ck" link="<?php geturl($menu['codepath'],1)?>" title="<?php echo $menu['title']?>" id="_menu_item_<?=$menu['code']?>">
                            <img src="/static/images/ebh/menu/menu_<?=$menu['code']?>.png" onerror="this.onerror=null; this.src='/static/images/ebh/menu/menu.png'" /><?php echo $menu['title']?>
                        </a>
                    </li>
                    <?php }?>
				</ul>
			</div>
        <?php }?>
        <?php if (!empty($menulist[20])){//系统日志?>
			<div class="menutit">
				<span class="iopen" style="background:url(/static/images/ebh/menu/menu_log.png) left center no-repeat;"></span>
                <a class="_menu_ck" href="javascript:;"><?php echo $menulist[20]['title']?><b class="arrmal">></b></a>
			</div>
			<?php 
				krsort($menulist[20]['child']);
			?>
			<div class="twomenu" style="display: none">
				<ul>
                	<?php foreach ($menulist[20]['child'] as $menuid => $menu){?>
					<li>
                        <a href="javascript:;" class="_menu_item_ck" link="<?php echo geturl($menu['codepath'])?>" title="<?php echo $menu['title']?>" id="_menu_item_<?=$menu['code']?>">
                            <img src="/static/images/ebh/menu/menu_<?=$menu['code']?>.png" onerror="this.onerror=null; this.src='/static/images/ebh/menu/menu.png'" /><?php echo $menu['title']?>
                        </a>
                    </li>
                    <?php }?>
				</ul>
			</div>
        <?php }?>
		</div>	
	</div>
</div>
