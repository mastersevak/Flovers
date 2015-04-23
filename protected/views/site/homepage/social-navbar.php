<!-- Social navbar -->
<section class="content content-border nopad-xs social-widget grey-container-dark">
	<div class="container">
		<div class="row">
			<div class="col-sm-6 col-md-6  col-lg-6 newsletter collapsed-block">
				<div class="row">

					<div class="col-lg-5  col-md-12 col-sm-12 ">
						<h3><?=t('front', 'РАССЫЛКА НОВОСТЕЙ')?>
							<a class="expander visible-xs" href="#TabBlock-1">+</a>
						</h3>
					</div>

					<div class="col-sm-12 col-md-12 col-lg-6 tabBlock" id="TabBlock-1">

						<p><?=t('front', 'Введите адрес электронной почты, и мы вышлем Вам купон с 10%-ной скидкой с вашим следующим заказом.')?></p>

						<form class="form-inline" role="form">
							<div class="form-group input-control">
								<button type="submit" class="button">
									<span class="icon-envelop"></span>
								</button>
								<input type="text" class="form-control" value="<?=t('front', 'Ваш адрес электронной почты...')?>" onblur="if (this.value == '') {this.value = 'Ваш адрес электронной почты...';}" onfocus="if(this.value == 'Ваш адрес электронной почты...') {this.value = '';}" ></div>
						</form>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-md-6 col-lg-6 collapsed-block">
				<h3>
					<?=t('front', 'НАЙДИТЕ НАС В')?>
					<a class="expander visible-xs" href="#TabBlock-2">+</a>
				</h3>
				<div  class="tabBlock" id="TabBlock-2">
					<ul class="find-us">
						<li class="divider">
							<a href="#" class="animate-scale">
								<span class="icon icon-facebook-3"></span>
							</a>
						</li>
						<li class="divider">
							<a href="#" class="animate-scale">
								<span class="icon icon-twitter-3"></span>
							</a>
						</li>
						<li class="divider">
							<a href="#" class="animate-scale">
								<span class="icon icon-linkedin-2"></span>
							</a>
						</li>
						<li class="divider">
							<a href="#" class="animate-scale">
								<span class="icon icon-youtube-3"></span>
							</a>
						</li>
						<li class="divider">
							<a href="#" class="animate-scale">
								<span class="icon icon-pinterest-2"></span>
							</a>
						</li>
						<li class="divider">
							<a href="#" class="animate-scale">
								<span class="icon icon-googleplus-2"></span>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>