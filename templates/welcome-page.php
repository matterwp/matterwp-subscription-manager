<?php
/**
 * Welcome page template for WP Subscription Manager
 *
 * @var string $plugin_slug
 * @var string $plugin_name
 * @var string $after_subscription_url
 * @var WP_User $current_user
 * @var string $email_endpoint
 * @var array  $branding
 */

defined( 'ABSPATH' ) || exit;

$logo        = isset( $branding['logo'] ) ? $branding['logo'] : $default_logo;
$logo_width  = isset( $branding['logo_width'] ) ? $branding['logo_width'] : '56px';
$heading     = isset( $branding['heading'] ) ? $branding['heading'] : sprintf( 'Thank you for installing %s!', $plugin_name );
$description = isset( $branding['description'] ) ? $branding['description'] : 'Join our email list for updates on security and new features! Providing a few details about your WordPress setup will help us optimize the plugin specifically for your site.';
$btn_text    = isset( $branding['button_text'] ) ? $branding['button_text'] : 'Proceed & Go to Settings';
$btn_loading = isset( $branding['button_loading_text'] ) ? $branding['button_loading_text'] : 'Processing...';
$btn_success = isset( $branding['button_success_text'] ) ? $branding['button_success_text'] : 'Subscribed successfully';
$btn_color   = isset( $branding['button_color'] ) ? $branding['button_color'] : '#b45309';
$btn_hover   = isset( $branding['button_hover_color'] ) ? $branding['button_hover_color'] : '#92400e';
$privacy_url = isset( $branding['privacy_url'] ) ? $branding['privacy_url'] : 'https://matterwp.com/privacy-policy/';
$terms_url   = isset( $branding['terms_url'] ) ? $branding['terms_url'] : 'https://matterwp.com/terms-of-service/';
?>

<style>
.wp-admin {
	background-color: #f4f4f5;
}

.mttr-brand {
	margin-bottom: 20px;
}

.mttr-brand img {
	height: auto;
}

.mttr-subscribe-panel {
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	padding-top: 90px;
	-webkit-font-smoothing: antialiased;
	-moz-osx-font-smoothing: grayscale;
}

.mttr-subscribe-panel-content {
	max-width: 500px;
	width: 100%;
	background-color: #FFF;
	border-radius: 20px;
	padding: 40px;
	text-align: center;
	filter: drop-shadow(0 2px 1px rgb(0 0 0 / 0.05));
}

.mttr-subscribe-panel-content h1 {
	font-size: 22px;
	font-weight: 600;
}

.mttr-subscribe-panel-content .notice {
	display: none !important;
}

.mttr-plugin-desc {
	font-size: 15px;
	color: #64748b;
	line-height: 1.5;
}

.mttr-subscribe-panel-footer p {
	color: #71717a;
	font-size: 12px;
}

.mttr-subscribe-panel-footer-actions {
	display: flex;
	justify-content: center;
	gap: 15px;
	margin-top: 30px;
}

.btn-subscribe,
.btn-skip {
	border-radius: 10px;
	padding: 0 20px;
	display: flex;
	height: 40px;
	align-items: center;
	font-weight: 600;
	font-size: 13px;
	border: none;
	cursor: pointer;
}

.btn-subscribe {
	color: #fff;
	display: flex;
	align-items: center;
	gap: 5px;
	transition: all 0.3s ease;
	background-color:
		<?php
		echo esc_attr( $btn_color );
		?>
	;
}

.btn-subscribe:hover {
	background-color:
		<?php
		echo esc_attr( $btn_hover );
		?>
	;
}

.btn-subscribe svg {
	width: 16px;
	height: 16px;
}

.btn-skip {
	background-color: #fafafa;
	border: 1px solid #f4f4f5;
	color: #52525b;
	text-decoration: none;
	transition: all 0.3s ease;
}

.btn-skip:hover {
	background-color: #e4e4e7;
	border: 1px solid #e4e4e7;
}

.animate-spin {
	animation: spin 1s linear infinite;
	opacity: 0.5;
	display: inline-block;
}

@keyframes spin {
	from {
		transform: rotate(0deg);
	}

	to {
		transform: rotate(360deg);
	}
}

.btn-success {
	display: flex;
	align-items: center;
	gap: 5px;
}

.btn-subscribe:disabled {
	cursor: not-allowed;
}

.btn-loading {
	display: flex;
	align-items: center;
	gap: 5px;
}

@keyframes check {
	from {
		stroke-dashoffset: 48;
	}

	to {
		stroke-dashoffset: 0;
	}
}

.animate-check {
	stroke-dasharray: 48;
	stroke-dashoffset: 48;
	animation: check 1.5s ease forwards;
}
</style>

<div id="mttr-subscribe" class="wrap <?php echo esc_attr( $plugin_slug ); ?>-welcome-page">
	<div class="mttr-subscribe-panel">
		<div class="mttr-subscribe-panel-content">
			<div class="mttr-brand">
				<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $plugin_name ); ?> logo" style="width: <?php echo esc_attr( $logo_width ); ?>" />
			</div>
			<h1><?php echo esc_html( $heading ); ?></h1>
			<p class="mttr-plugin-desc"><?php echo esc_html( $description ); ?></p>
			<div class="welcome-panel-column-container">
				<form id="subscription-form" method="POST">
					<?php wp_nonce_field( $plugin_slug . '_subscription_nonce' ); ?>
					<input type="hidden" name="is_subscribed" value="1">
					<p class="mttr-subscribe-panel-footer-actions">
						<button type="button" class="btn-skip">Skip</button>
						<button type="submit" class="btn-subscribe">
							<span class="btn-text"><?php echo esc_html( $btn_text ); ?></span>
							<span class="btn-loading" style="display: none;">
								<svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" width="16" height="16">
									<circle class="opacity-25" cx="12" cy="12" r="10" stroke="rgba(255, 255, 255, 0.5)" stroke-width="4"></circle>
									<path class="opacity-75" fill="white" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
								</svg>
								<?php echo esc_html( $btn_loading ); ?>
							</span>
							<span class="btn-success" style="display: none;">
								<svg class="animate-check" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<polyline points="20 6 9 17 4 12"></polyline>
								</svg>
								<span><?php echo esc_html( $btn_success ); ?></span>
							</span>
							<svg class="btn-arrow" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
								<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m21 12l-5-5m5 5l-5 5m5-5H3" />
							</svg>
						</button>
					</p>
				</form>
			</div>
		</div>
		<div class="mttr-subscribe-panel-footer">
			<p>By subscribing, you agree to our <a href="<?php echo esc_url( $privacy_url ); ?>" target="_blank">Privacy Policy</a> and <a href="<?php echo esc_url( $terms_url ); ?>" target="_blank">Terms of Service</a>.</p>
		</div>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	class SubscriptionForm {
		constructor() {
			this.initElements();
			this.init();
		}

		initElements() {
			this.$form = $('#subscription-form');
			this.$button = this.$form.find('.btn-subscribe');
			this.$skipButton = this.$form.find('.btn-skip');
			this.$btnText = this.$button.find('.btn-text');
			this.$btnLoading = this.$button.find('.btn-loading');
			this.$btnSuccess = this.$button.find('.btn-success');
			this.$btnArrow = this.$button.find('.btn-arrow');
			this.$isSubscribed = this.$form.find('input[name="is_subscribed"]');
			this.isSkipped = false;
		}

		init() {
			this.$form.on('submit', (e) => {
				e.preventDefault();
				this.handleSubmit();
			});

			this.$skipButton.on('click', () => {
				this.$isSubscribed.val('0');
				this.isSkipped = true;
				this.handleSubmit();
			});
		}

		async handleSubmit() {
			if (!this.isSkipped) {
				this.setLoadingState();
			}

			try {
				const response = await $.ajax({
					url: ajaxurl,
					method: 'POST',
					data: {
						action: 'get_diagnosis',
						nonce: '<?php echo wp_create_nonce( 'get_diagnosis' ); ?>'
					}
				});

				if (!response.success) {
					throw new Error('Failed to get system data');
				}

				const formData = {
					...response.data,
					is_subscribed: this.$isSubscribed.val()
				};

				await fetch('https://das.matterwp.com/', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					},
					mode: 'no-cors',
					body: JSON.stringify(formData)
				});

				if (!this.isSkipped) {
					this.setSuccessState();
				}

				if (this.isSkipped) {
					setTimeout(() => {
						window.location.href = '<?php echo esc_js( $after_subscription_url ); ?>';
					}, 0);
				} else {
					setTimeout(() => {
						window.location.href = '<?php echo esc_js( $after_subscription_url ); ?>';
					}, 1500);
				}
			} catch (error) {
				console.error('Error:', error);
				alert('An error occurred. Please try again.');
				if (!this.isSkipped) {
					this.resetState();
				}
			}
		}

		setLoadingState() {
			this.$button.prop('disabled', true);
			this.$btnText.hide();
			this.$btnArrow.hide();
			this.$btnLoading.show();
			this.$skipButton.hide();
		}

		setSuccessState() {
			this.$button.css('background-color', '#047857');
			this.$btnLoading.hide();
			this.$btnSuccess.show();
			this.$skipButton.hide();
		}

		resetState() {
			this.$button.prop('disabled', false);
			this.$btnText.show();
			this.$btnArrow.show();
			this.$btnLoading.hide();
			this.$skipButton.show();
		}
	}

	new SubscriptionForm();
});
</script>
