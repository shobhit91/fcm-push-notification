<?php
/*Template Name:[contact us]*/
get_header();?>
<div class="contact-form-home">
	<div class="container">
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				<div class="row">
					<div class="col-md-3">
					</div>
					<div class="col-md-6">
						<div class="row">
							<div class="col-md-12">
								<h1>Contact Us</h1>
							</div>
							<?php echo do_shortcode('[contact-form-7 id="10" title="Contact form"]');?>
						</div>
					</div>
					<div class="col-md-3">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php get_footer();?>