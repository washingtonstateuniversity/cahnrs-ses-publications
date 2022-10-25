<?php if( get_post_type() == 'publication' ) {?>

<?php wp_nonce_field('submit_sespub','sespub_nonce'); ?>

<div class="or_input_wrap" style="width: 80%; display: inline-block; ">
	<label>Short Title</label><br />
	<input value="<?php echo $this->model->short_title; ?>" type="text" name="_short_title" style="width: 90%;" />
</div>

<div class="or_input_wrap" style="width: 80%; display: inline-block; ">


</div>


<div class="or_input_wrap" style="width: 80%; display: inline-block; ">
	<label>Issue and Pages</label><br />
	<input value="<?php echo $this->model->issue_pages; ?>" type="text" name="_issue_pages" style="width: 50%;" />
</div>

<div class="or_input_wrap" style="width: 80%; display: inline-block; ">
	<label>URL  </label><br />
	<input value="<?php echo $this->model->redirect; ?>" type="text" name="_redirect_to" style="width: 50%;" />
</div>


 <?php } ?>