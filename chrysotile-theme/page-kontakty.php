<?php
/**
 * Template Name: Контакты
 * Template Post Type: page
 *
 * Страница контактов (как в generatepress-child/page-kontakty.php), оформление Chrysotile Child.
 * Для автоподстановки создайте страницу со ярлыком kontakty или назначьте этот шаблон вручную.
 *
 * @package Chrysotile_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$contact_status = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : '';
$contact_reason = isset( $_GET['contact_reason'] ) ? sanitize_key( wp_unslash( $_GET['contact_reason'] ) ) : '';
$contact_debug  = get_transient( 'chrysotile_contact_debug_message' );
if ( is_string( $contact_debug ) && '' !== $contact_debug ) {
	delete_transient( 'chrysotile_contact_debug_message' );
}
?>

<section class="chrysotile-contacts-page">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'chrysotile-contacts-article' ); ?>>
			<header class="chrysotile-contacts-header">
				<h1 class="chrysotile-contacts-title"><?php the_title(); ?></h1>
			</header>

			<div class="chrysotile-contacts-body">
				<?php if ( 'error' === $contact_status ) : ?>
					<p class="chrysotile-contacts-notice chrysotile-contacts-notice--error" role="alert">
						<?php esc_html_e( 'Не удалось отправить. Проверьте поля и попробуйте снова.', 'chrysotile-child' ); ?>
						<?php if ( current_user_can( 'manage_options' ) && '' !== $contact_reason ) : ?>
							<br />
							<small><?php echo esc_html( 'Debug: ' . $contact_reason ); ?></small>
							<?php if ( is_string( $contact_debug ) && '' !== $contact_debug ) : ?>
								<br />
								<small><?php echo esc_html( $contact_debug ); ?></small>
							<?php endif; ?>
						<?php endif; ?>
					</p>
				<?php endif; ?>

				<div class="chrysotile-contacts-inner">
					<?php
					$yandex_map_src = function_exists( 'chrysotile_contacts_yandex_map_embed_url' )
						? chrysotile_contacts_yandex_map_embed_url()
						: '';
					if ( is_string( $yandex_map_src ) && '' !== $yandex_map_src ) :
						?>
					<div class="chrysotile-contacts-map-wrap">
						<iframe
							class="chrysotile-contacts-map"
							src="<?php echo esc_url( $yandex_map_src ); ?>"
							width="100%"
							height="400"
							allowfullscreen
							loading="lazy"
							title="<?php esc_attr_e( 'Карта: г. Житикара, ул. Ленина, 67', 'chrysotile-child' ); ?>"
						></iframe>
					</div>
						<?php
					endif;
					?>
					<div class="chrysotile-contacts-grid">
						<section class="chrysotile-contacts-form-section" aria-labelledby="chrysotile-contacts-form-title">
							<h2 id="chrysotile-contacts-form-title" class="chrysotile-contacts-section-title">
								<?php esc_html_e( 'Напишите нам', 'chrysotile-child' ); ?>
							</h2>
							<form class="chrysotile-contacts-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
								<input type="hidden" name="action" value="chrysotile_contact" />
								<?php wp_nonce_field( 'chrysotile_contact', 'chrysotile_contact_nonce' ); ?>
								<div class="chrysotile-contacts-field-row">
									<p class="chrysotile-contacts-field">
										<label for="chrysotile_contact_name"><?php esc_html_e( 'Имя', 'chrysotile-child' ); ?></label>
										<input type="text" id="chrysotile_contact_name" name="chrysotile_contact_name" required autocomplete="name" />
									</p>
									<p class="chrysotile-contacts-field">
										<label for="chrysotile_contact_phone"><?php esc_html_e( 'Телефон', 'chrysotile-child' ); ?></label>
										<input type="tel" id="chrysotile_contact_phone" name="chrysotile_contact_phone" required autocomplete="tel" pattern="^\+?[0-9\-\s\(\)]{10,25}$" minlength="10" maxlength="25" title="<?php esc_attr_e( 'Введите реальный номер телефона (10-15 цифр, можно с +, пробелами, скобками и дефисами).', 'chrysotile-child' ); ?>" />
									</p>
									<p class="chrysotile-contacts-field">
										<label for="chrysotile_contact_email"><?php esc_html_e( 'Email', 'chrysotile-child' ); ?></label>
										<input type="email" id="chrysotile_contact_email" name="chrysotile_contact_email" required autocomplete="email" />
									</p>
								</div>
								<p class="chrysotile-contacts-field chrysotile-contacts-field--message">
									<label for="chrysotile_contact_message"><?php esc_html_e( 'Сообщение', 'chrysotile-child' ); ?></label>
									<textarea id="chrysotile_contact_message" name="chrysotile_contact_message" rows="4" required></textarea>
								</p>
								<p class="chrysotile-contacts-consent-wrap">
									<label class="chrysotile-contacts-consent-label" for="chrysotile_contact_consent">
										<input type="checkbox" id="chrysotile_contact_consent" name="chrysotile_contact_consent" value="1" required />
										<span>
											<?php
											printf(
												/* translators: %s is URL to personal data policy/rules page. */
												wp_kses(
													__( 'Я подтверждаю согласие на обработку персональных данных и принимаю <a href="%s" target="_blank" rel="noopener noreferrer">правила обработки данных</a>.', 'chrysotile-child' ),
													array(
														'a' => array(
															'href'   => true,
															'target' => true,
															'rel'    => true,
														),
													)
												),
												esc_url( function_exists( 'chrysotile_child_get_page_url_by_slug' ) ? chrysotile_child_get_page_url_by_slug( 'rulse_use_material' ) : home_url( '/' ) )
											);
											?>
										</span>
									</label>
								</p>
								<p class="chrysotile-contacts-submit-wrap">
									<button type="submit" class="chrysotile-contacts-submit"><?php esc_html_e( 'Отправить', 'chrysotile-child' ); ?></button>
								</p>
							</form>
						</section>

						<aside class="chrysotile-contacts-aside" aria-label="<?php esc_attr_e( 'Реквизиты', 'chrysotile-child' ); ?>">
							<div class="chrysotile-contacts-card">
								<h2 class="chrysotile-contacts-section-title"><?php esc_html_e( 'Как с нами связаться', 'chrysotile-child' ); ?></h2>
								<address class="chrysotile-contacts-address">
									<?php esc_html_e( '110700, Республика Казахстан, Костанайская область, г. Житикара, ул. Ленина 67, Управление АО КМ, 3 этаж, 35 кабинет', 'chrysotile-child' ); ?>
								</address>
								<p class="chrysotile-contacts-line">
									<span class="chrysotile-contacts-label"><?php esc_html_e( 'Телефон', 'chrysotile-child' ); ?>:</span>
									<a href="tel:+77143522599">+7 7143 52-25-99</a>
								</p>
								<p class="chrysotile-contacts-line">
									<span class="chrysotile-contacts-label"><?php esc_html_e( 'Почта', 'chrysotile-child' ); ?>:</span>
									<a href="mailto:editor@km.kz">editor@km.kz</a>
								</p>
								<p class="chrysotile-contacts-note">
									<?php esc_html_e( 'Более 10 лет наша газета радует читателей свежими и интересными новостями.', 'chrysotile-child' ); ?>
								</p>
							</div>
						</aside>
					</div>

					<?php
					global $post;
					if ( $post instanceof WP_Post && trim( $post->post_content ) !== '' ) {
						echo '<div class="chrysotile-contacts-editor entry-content">';
						the_content();
						echo '</div>';
					}
					?>
				</div>
			</div>
		</article>
		<?php
	endwhile;
	?>
</section>

<?php if ( 'sent' === $contact_status ) : ?>
	<div class="chrysotile-contacts-modal" id="chrysotile-contacts-modal" role="dialog" aria-modal="true" aria-labelledby="chrysotile-contacts-modal-title">
		<div class="chrysotile-contacts-modal__backdrop"></div>
		<div class="chrysotile-contacts-modal__content">
			<button type="button" class="chrysotile-contacts-modal__close" aria-label="<?php esc_attr_e( 'Закрыть', 'chrysotile-child' ); ?>">×</button>
			<h2 id="chrysotile-contacts-modal-title"><?php esc_html_e( 'Спасибо за обращение!', 'chrysotile-child' ); ?></h2>
			<p><?php esc_html_e( 'Мы получили ваше сообщение и скоро с вами свяжемся.', 'chrysotile-child' ); ?></p>
		</div>
	</div>
	<script>
		(function () {
			var modal = document.getElementById('chrysotile-contacts-modal');
			if (!modal) { return; }
			function closeModal() {
				modal.classList.remove('is-open');
				document.body.classList.remove('chrysotile-contacts-modal-open');
			}
			modal.classList.add('is-open');
			document.body.classList.add('chrysotile-contacts-modal-open');
			var closeBtn = modal.querySelector('.chrysotile-contacts-modal__close');
			var backdrop = modal.querySelector('.chrysotile-contacts-modal__backdrop');
			if (closeBtn) { closeBtn.addEventListener('click', closeModal); }
			if (backdrop) { backdrop.addEventListener('click', closeModal); }
			document.addEventListener('keydown', function (event) {
				if (event.key === 'Escape') { closeModal(); }
			});
		})();
	</script>
<?php endif; ?>

<?php
get_footer();
