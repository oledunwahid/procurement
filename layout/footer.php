<footer class="footer">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-6">
				<script>
					document.write(new Date().getFullYear())
				</script> © PT. Mineral Alam Abadi.
			</div>
			<div class="col-sm-6">
				<div class="text-sm-end d-none d-sm-block">
					Design & Develop by Team HR-GA
				</div>
			</div>
		</div>



	</div>


</footer>


<script>
	document.addEventListener("DOMContentLoaded", function() {  
		<?php if(isset($_SESSION['Messages'])) { ?>
			Swal.fire({
				icon: "<?php echo $_SESSION['Icon']; ?>",
				title: "<?php echo $_SESSION['Messages']; ?>",
				showConfirmButton: !1,
				timer: 2000,
				showCloseButton: !0
			});
			<?php unset($_SESSION['Messages']); ?>
                            <?php } ?>
		})
</script>


