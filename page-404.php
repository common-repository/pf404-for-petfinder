<?php get_header();?>

<section id="petfinder-404-page">
	<div class="petfinder-404-wrap">
		<?php 
		$apikey = get_option('pf404_options')['pf404_field_apikey'];
		$explictperm = get_option('pf404_options')['pf404_field_explicitperm'];
		if ($apikey && !$petfinder404->error && $explictperm === 'Yes'){ ?>
			<div class="pf404-content">
				<h2><?php echo(get_option('pf404_options')['pf404_field_heading']) ?></h2>
				<p><?php echo(get_option('pf404_options')['pf404_field_pagecontent']) ?></p>
			</div>
			<div class="pf404grid">
				<?php
					$pets = $petfinder404->lookup_pets();
					foreach ($pets as $key => $value) {
						
						?>
						<div class="pf404item">
						<a target="_blank" href="https://www.petfinder.com/petdetail/<?=$value->pf_id?>">
							<img src="<?=$value->pf_image?>" alt="<?=$value->pf_name?>">
							<label><?=$value->pf_name?></label>
						</a>
						</div>
						<?php
					}
				?>
			</div>
			<p><a class="pf404search" href="https://www.petfinder.com/">Find <?php echo(get_option('pf404_options')['pf404_field_animal']) ?>s in your area</a></p>
		<?php } else { ?>
			<div class="pf404-content">
			<h2>404 Page Not Found</h2>
			<?php if (is_user_logged_in()) { ?>
				<p>Site Administrator: PF404 for PetFinder requires an API key and link permission allowed, please see Setting > <a href="/wp-admin/options-general.php?page=pf404">PF404 for PetFinder</a> in the admin menu for more details. Note that this messsage is only shown to logged in users.</p>
			<?php } ?>
			</div>
		<?php } ?>
	</div>
</section>
<script>
$(function() {
	var $grid = $('.pf404grid').masonry({
	  itemSelector: '.pf404item',
	  percentPosition: true
	})
	$grid.imagesLoaded().progress( function() {
	  $grid.masonry('layout');
	});
});
</script>
<?php get_footer();?>